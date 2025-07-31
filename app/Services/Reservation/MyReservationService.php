<?php

namespace App\Services\Reservation;

use App\Models\Reservation\Reservation;
use App\Exceptions\BusinessValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Carbon\Carbon;

class MyReservationService
{
    /**
     * Get reservations for a specific user, with optional filters.
     *
     * @param int $userId
     * @param array $filters
     * @return array
     */
    public function getUserReservations(int $userId, array $filters = []): array
    {
        $query = Reservation::with([
            'accommodation.accommodatable.apartment.building',
            'accommodation.accommodatable',
            'academicTerm',
        ])
        ->where('user_id', $userId);

        // Filter by property (apartment or building name)
        if (!empty($filters['property'])) {
            $property = $filters['property'];
            $query->whereHas('accommodation.accommodatable.apartment', function ($q) use ($property) {
                $q->where('name', 'like', '%' . $property . '%');
            })->orWhereHas('accommodation.accommodatable.apartment.building', function ($q) use ($property) {
                $q->where('name', 'like', '%' . $property . '%');
            });
        }

        // Filter by status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by date range
        if (!empty($filters['date_from'])) {
            $query->whereDate('check_in_date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('check_out_date', '<=', $filters['date_to']);
        }

        // Pagination: get page and per_page from filters, with defaults
        $page = !empty($filters['page']) ? (int)$filters['page'] : 1;
        $perPage = !empty($filters['per_page']) ? (int)$filters['per_page'] : 10;

        $paginator = $query->orderByDesc('created_at')->paginate($perPage, ['*'], 'page', $page);

        // Format for card display
        $data = $paginator->getCollection()->map(function ($reservation) {
            return [
                'id' => $reservation->id,
                'reservation_number' => $reservation->reservation_number,
                'status' => $reservation->status,
                'status_label' => $this->getStatusLabel($reservation->status),
                'property_name' => $this->getPropertyName($reservation),
                'property_address' => $this->getPropertyAddress($reservation),
                'check_in_date' => $reservation->check_in_date ? formatDate($reservation->check_in_date) : null,
                'check_out_date' => $reservation->check_out_date ? formatDate($reservation->check_out_date) : null,
                'guests' => $reservation->guests ?? 1,
                'price' => $reservation->price ?? null,
                'price_label' => $reservation->price ? '$' . number_format($reservation->price) : null,
                'created_at' => $reservation->created_at ? formatDate($reservation->created_at) : null,
                'apartment_number' => $reservation->accommodation?->accommodatable?->apartment?->number ?? null,
                'room_number' => $reservation->accommodation?->accommodatable?->number ?? null,
                'academic_term' => $reservation->academicTerm?->name ?? null,
            ];
        })->toArray();

        // Return paginated structure
        return [
            'data' => $data,
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
        ];
    }

    /**
     * Get a human-readable status label (with bootstrap color class).
     *
     * @param string $status
     * @return array
     */
    protected function getStatusLabel(string $status): array
    {
        $map = [
            'confirmed' => ['label' => 'Confirmed', 'class' => 'bg-label-success'],
            'pending' => ['label' => 'Pending', 'class' => 'bg-label-warning'],
            'cancelled' => ['label' => 'Cancelled', 'class' => 'bg-label-danger'],
            'completed' => ['label' => 'Completed', 'class' => 'bg-label-info'],
            'checked_in' => ['label' => 'Checked In', 'class' => 'bg-label-primary'],
            'checked_out' => ['label' => 'Checked Out', 'class' => 'bg-label-secondary'],
        ];
        return $map[$status] ?? ['label' => ucfirst($status), 'class' => 'bg-label-default'];
    }

    /**
     * Get the property name for a reservation.
     *
     * @param Reservation $reservation
     * @return string|null
     */
    protected function getPropertyName(Reservation $reservation): ?string
    {
        // Try apartment name, then building name, then fallback
        return $reservation->accommodation?->accommodatable?->apartment?->name
            ?? $reservation->accommodation?->accommodatable?->apartment?->building?->name
            ?? null;
    }

    /**
     * Get the property address for a reservation.
     *
     * @param Reservation $reservation
     * @return string|null
     */
    protected function getPropertyAddress(Reservation $reservation): ?string
    {
        // Try apartment address, then building address, then fallback
        return $reservation->accommodation?->accommodatable?->apartment?->address
            ?? $reservation->accommodation?->accommodatable?->apartment?->building?->address
            ?? null;
    }

    /**
     * Cancel a reservation for the user.
     *
     * @param int $reservationId
     * @param int $userId
     * @return Reservation
     * @throws BusinessValidationException
     */
    public function cancelReservation(int $reservationId, int $userId): Reservation
    {
        $reservation = Reservation::where('id', $reservationId)
            ->where('user_id', $userId)
            ->firstOrFail();

        if (!in_array($reservation->status, ['pending', 'confirmed'])) {
            throw new BusinessValidationException('Reservation cannot be cancelled.');
        }

        $reservation->status = 'cancelled';
        $reservation->save();

        return $reservation;
    }

    /**
     * Delete a reservation for the user.
     *
     * @param int $reservationId
     * @param int $userId
     * @return bool
     * @throws BusinessValidationException
     */
    public function deleteReservation(int $reservationId, int $userId): bool
    {
        $reservation = Reservation::where('id', $reservationId)
            ->where('user_id', $userId)
            ->firstOrFail();

        if ($reservation->status === 'checked_in') {
            throw new BusinessValidationException('Cannot delete a reservation that has been checked in.');
        }

        return (bool) $reservation->delete();
    }
}
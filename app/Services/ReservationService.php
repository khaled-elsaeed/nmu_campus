<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\User;
use App\Models\Resident\Student;
use App\Models\Reservation\ِAccommodation;
use App\Models\Academic\AcademicTerm;
use App\Models\Housing\Room;
use App\Models\Housing\Apartment;
use App\Exceptions\BusinessValidationException;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use App\Services\Reservation\CreateReservationService;

class ReservationService
{
    public function __construct(protected CreateReservationService $createReservationService)
    {}

    /**
     * Create a new reservation.
     *
     * @param array $data
     * @return Reservation|array
     */
    public function createReservation(array $data)
    {
        return DB::transaction(function () use ($data) {
            return $this->createReservationService->create($data);
        });
    }

    /**
     * Update an existing reservation.
     *
     * @param Reservation $reservation
     * @param array $data
     * @return Reservation
     */
    public function updateReservation(Reservation $reservation, array $data): Reservation
    {
        return DB::transaction(function () use ($reservation, $data) {
            $this->updateReservationRecord($reservation, $data);
            return $reservation->fresh(['user', 'accommodation', 'academicTerm']);
        });
    }


    /**
     * Get a single reservation with relationships.
     *
     * @param int $id
     * @return array
     */
    public function getReservation(int $id): array
    {
        $reservation = Reservation::select([
            'id',
            'student_id',
            'room_id',
            'academic_term_id',
            'status',
            'start_date',
            'end_date'
        ])->find($id);

        if (!$reservation) {
            throw new BusinessValidationException('Reservation not found.');
        }

        return [
            'id' => $reservation->id,
            'student_id' => $reservation->student_id,
            'room_id' => $reservation->room_id,
            'academic_term_id' => $reservation->academic_term_id,
            'status' => $reservation->status,
            'start_date' => $reservation->start_date,
            'end_date' => $reservation->end_date,
        ];
    }

    /**
     * Delete a reservation.
     *
     * @param int $reservationId
     * @return bool
     * @throws BusinessValidationException
     */
    public function deleteReservation(int $reservationId): bool
    {
        $reservation = Reservation::findOrFail($reservationId);

        if ($reservation->status === 'checked_in') {
            throw new BusinessValidationException('Cannot delete a reservation that has been checked in.');
        }

        $deleted = $reservation->delete();

        return $deleted;
    }

    /**
     * Get all reservations (for dropdowns/forms).
     *
     * @return array
     */
    public function getAll(): array
    {
        return Reservation::select(['id', 'student_id', 'room_id', 'status'])->get()->toArray();
    }

    /**
     * Get reservation statistics.
     *
     * @return array
     */
    public function getStats(): array
    {
        $totalReservations = Reservation::count();
        $activeReservations = Reservation::where('active', true)->count();
        $inactiveReservations = Reservation::where('active', false)->count();
        $pendingReservations = Reservation::where('status', 'pending')->count();
        $confirmedReservations = Reservation::where('status', 'confirmed')->count();
        $checkedInReservations = Reservation::where('status', 'checked_in')->count();
        $checkedOutReservations = Reservation::where('status', 'checked_out')->count();
        $cancelledReservations = Reservation::where('status', 'cancelled')->count();
        
        $lastUpdateTime = formatDate(Reservation::max('updated_at'));
        $activeLastUpdate = formatDate(Reservation::where('active', true)->max('updated_at'));
        $inactiveLastUpdate = formatDate(Reservation::where('active', false)->max('updated_at'));
        
        return [
            'total' => [
                'count' => formatNumber($totalReservations),
                'lastUpdateTime' => $lastUpdateTime
            ],
            'active' => [
                'count' => formatNumber($activeReservations),
                'lastUpdateTime' => $activeLastUpdate
            ],
            'inactive' => [
                'count' => formatNumber($inactiveReservations),
                'lastUpdateTime' => $inactiveLastUpdate
            ],
            'statuses' => [
                'pending' => formatNumber($pendingReservations),
                'confirmed' => formatNumber($confirmedReservations),
                'checked_in' => formatNumber($checkedInReservations),
                'checked_out' => formatNumber($checkedOutReservations),
                'cancelled' => formatNumber($cancelledReservations),
            ]
        ];
    }

    /**
     * Get reservation data for DataTables.
     *
     * @return JsonResponse
     */
    public function getDatatable(): JsonResponse
    {
        $query = Reservation::with(['user', 'accommodation', 'academicTerm']);
        $query = $this->applySearchFilters($query);
        
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('reservation_number', fn($reservation) => $reservation->reservation_number)
            ->addColumn('name', fn($reservation) => $reservation->user?->name ?? 'N/A')
            ->addColumn('accommodation_info', fn($reservation) => $this->getAccommodationInfo($reservation))
            ->addColumn('academic_term', fn($reservation) => $reservation->academicTerm?->name ?? 'N/A')
            ->addColumn('check_in_date', fn($reservation) => $reservation->check_in_date ? formatDate($reservation->check_in_date) : 'N/A')
            ->addColumn('check_out_date', fn($reservation) => $reservation->check_out_date ? formatDate($reservation->check_out_date) : 'N/A')
            ->addColumn('status', fn($reservation) => ucfirst($reservation->status))
            ->addColumn('active', fn($reservation) => $reservation->active ? 'Active' : 'Inactive')
            ->addColumn('created_at', fn($reservation) => formatDate($reservation->created_at))
            ->addColumn('action', fn($reservation) => $this->renderActionButtons($reservation))
            
            // Order columns for related tables
            ->orderColumn('reservation_number', 'reservations.reservation_number $1')
            ->orderColumn('user_name', function ($query, $order) {
                return $query->leftJoin('users', 'reservations.user_id', '=', 'users.id')
                             ->orderBy('users.name_en', $order);
            })
            ->orderColumn('accommodation_info', function ($query, $order) {
                return $query->leftJoin('accommodations', 'reservations.accommodation_id', '=', 'accommodations.id')
                             ->orderBy('accommodations.id', $order);
            })
            ->orderColumn('academic_term', function ($query, $order) {
                return $query->leftJoin('academic_terms', 'reservations.academic_terms_id', '=', 'academic_terms.id')
                             ->orderBy('academic_terms.name_en', $order);
            })
            ->orderColumn('check_in_date', 'reservations.check_in_date $1')
            ->orderColumn('check_out_date', 'reservations.check_out_date $1')
            ->orderColumn('status', 'reservations.status $1')
            ->orderColumn('active', 'reservations.active $1')
            ->orderColumn('created_at', 'reservations.created_at $1')
            
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Apply search filters to the query.
     *
     * @param Builder $query
     * @return Builder
     */
    protected function applySearchFilters($query): Builder
    {
        if (request()->filled('search_reservation_number') && !empty(request('search_reservation_number'))) {
            $search = mb_strtolower(request('search_reservation_number'));
            $query->whereRaw('LOWER(reservation_number) LIKE ?', ['%' . $search . '%']);
        }
        
        if (request()->filled('search_user_name') && !empty(request('search_user_name'))) {
            $search = mb_strtolower(request('search_user_name'));
            $query->whereHas('user', function ($q) use ($search) {
                $q->whereRaw('LOWER(name_en) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(name_ar) LIKE ?', ['%' . $search . '%']);
            });
        }
        
        if (request()->filled('search_status')) {
            $query->where('status', request('search_status'));
        }
        
        if (request()->filled('search_active')) {
            $query->where('active', request('search_active'));
        }
        
        if (request()->filled('search_academic_term_id')) {
            $query->where('academic_terms_id', request('search_academic_term_id'));
        }
        
        if (request()->filled('search_accommodation_id')) {
            $query->whereHas('accommodation', function ($q) {
                $q->where('accommodatable_id', request('search_accommodation_id'));
            });
        }
        
        return $query;
    }

    /**
     * Render action buttons for datatable rows.
     *
     * @param Reservation $reservation
     * @return string
     */
    protected function renderActionButtons($reservation): string
    {
        return view('components.ui.datatable.data-table-actions', [
            'mode' => 'dropdown',
            'actions' => ['view', 'edit', 'delete'],
            'id' => $reservation->id,
            'type' => 'Reservation',
            'singleActions' => []
        ])->render();
    }

    /**
     * Get accommodation information for display.
     *
     * @param Reservation $reservation
     * @return string
     */
    private function getAccommodationInfo(Reservation $reservation): string
    {
        if (!$reservation->accommodation) {
            return 'N/A';
        }
        return $reservation->accommodation->detail ?? 'N/A';
    }

} 
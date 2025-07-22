<?php

namespace App\Services\Payment;

use App\Models\Insurance;
use App\Exceptions\BusinessValidationException;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use App\Models\Reservation\Reservation;

class InsuranceService
{
    /**
     * Create a new insurance record.
     *
     * @param array $data
     * @return Insurance
     */
    public function createInsurance(array $data): Insurance
    {
        if (isset($data['reservation_number'])) {
            $reservation = $this->validateReservationNumber($data['reservation_number']);
            $data['reservation_id'] = $reservation->id;
        }
        return Insurance::create($data);
    }

    /**
     * Validate reservation number and return Reservation model.
     *
     * @param string $reservationNumber
     * @return Reservation
     * @throws BusinessValidationException
     */
    private function validateReservationNumber(string $reservationNumber): Reservation
    {
        $reservation = Reservation::where('reservation_number', $reservationNumber)->first();
        if (!$reservation) {
            throw new BusinessValidationException('Reservation not found.', 404);
        }
        return $reservation;
    }

    /**
     * Update an existing insurance record.
     *
     * @param Insurance $insurance
     * @param array $data
     * @return Insurance
     */
    public function updateInsurance(Insurance $insurance, array $data): Insurance
    {
        $reservation = $this->validateReservationNumber($data['reservation_number']);
        $data['reservation_id'] = $reservation->id;
        $insurance->update($data);
        return $insurance->fresh();
    }

    /**
     * Get a single insurance record.
     *
     * @param int $id
     * @return array
     */
    public function getInsurance(int $id): array
    {
        $insurance = Insurance::select([
            'id',
            'student_id',
            'academic_term_id',
            'amount',
            'is_paid',
            'paid_at'
        ])->find($id);

        if (!$insurance) {
            throw new BusinessValidationException('Insurance not found.');
        }

        return [
            'id' => $insurance->id,
            'student_id' => $insurance->student_id,
            'academic_term_id' => $insurance->academic_term_id,
            'amount' => $insurance->amount,
            'is_paid' => $insurance->is_paid,
            'paid_at' => $insurance->paid_at,
        ];
    }

    /**
     * Delete an insurance record.
     *
     * @param int $id
     * @return void
     */
    public function deleteInsurance($id): void
    {
        $insurance = Insurance::findOrFail($id);
        $insurance->delete();
    }


    /**
     * Get insurance statistics.
     *
     * @return array
     */
    public function getStats(): array
    {
        $total = Insurance::count();
        $active = Insurance::where('status', 'active')->count();
        $refunded = Insurance::where('status', 'refunded')->count();
        $carriedOver = Insurance::where('status', 'carried_over')->count();
        $cancelled = Insurance::where('status', 'cancelled')->count();

        $lastUpdate = Insurance::max('updated_at');
        $activeLastUpdate = Insurance::where('status', 'active')->max('updated_at');
        $refundedLastUpdate = Insurance::where('status', 'refunded')->max('updated_at');
        $carriedOverLastUpdate = Insurance::where('status', 'carried_over')->max('updated_at');
        $cancelledLastUpdate = Insurance::where('status', 'cancelled')->max('updated_at');

        return [
            'total' => [
                'count' => formatNumber($total),
                'lastUpdateTime' => formatDate($lastUpdate),
            ],
            'active' => [
                'count' => formatNumber($active),
                'lastUpdateTime' => formatDate($activeLastUpdate),
            ],
            'refunded' => [
                'count' => formatNumber($refunded),
                'lastUpdateTime' => formatDate($refundedLastUpdate),
            ],
            'carried_over' => [
                'count' => formatNumber($carriedOver),
                'lastUpdateTime' => formatDate($carriedOverLastUpdate),
            ],
            'cancelled' => [
                'count' => formatNumber($cancelled),
                'lastUpdateTime' => formatDate($cancelledLastUpdate),
            ],
        ];
    }

    /**
     * Get insurance data for DataTables.
     *
     * @return JsonResponse
     */
    public function getDatatable(): JsonResponse
    {
        $query = Insurance::with(['reservation.user.profile']);
        $query = $this->applySearchFilters($query);
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('user_name', function ($insurance) {
                $user = $insurance->reservation && $insurance->reservation->user ? $insurance->reservation->user : null;
                $profile = $user && $user->profile ? $user->profile : null;
                return $profile ? $profile->name : '--';
            })
            ->addColumn('national_id', function ($insurance) {
                $user = $insurance->reservation && $insurance->reservation->user ? $insurance->reservation->user : null;
                $profile = $user && $user->profile ? $user->profile : null;
                return $profile ? $profile->national_id : '--';
            })
            ->addColumn('reservation_number', function ($insurance) {
                return $insurance->reservation ? $insurance->reservation->reservation_number : '--';
            })
            ->editColumn('status', fn($insurance) => ucfirst($insurance->status))
            ->editColumn('amount', fn($insurance) => formatCurrency($insurance->amount))
            ->editColumn('created_at', fn($insurance) => formatDate($insurance->created_at))
            ->addColumn('action', fn($insurance) => $this->renderActionButtons($insurance))
            ->orderColumn('amount', 'amount $1')
            ->orderColumn('status', 'status $1')
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
        $searchStatus = request('search_status');
        if (!empty($searchStatus)) {
            $query->where('status', $searchStatus);
        }
        // Remove user_id search filter
        $searchNationalId = request('search_national_id');
        if (!empty($searchNationalId)) {
            $query->whereHas('reservation.user.profile', function($q) use ($searchNationalId) {
                $q->where('national_id', $searchNationalId);
            });
        }
        $searchReservationNumber = request('search_reservation_number');
        if (!empty($searchReservationNumber)) {
            $query->whereHas('reservation', function($q) use ($searchReservationNumber) {
                $q->where('reservation_number', $searchReservationNumber);
            });
        }
        return $query;
    }

    /**
     * Render action buttons for datatable rows.
     *
     * @param Insurance $insurance
     * @return string
     */
    public function renderActionButtons(Insurance $insurance): string
    {
        $actions = ['view', 'delete'];
        $singleActions = [];
        if ($insurance->status === 'active') {
            $singleActions[] = [
                'action' => 'refund',
                'icon' => 'bx bx-undo',
                'class' => 'btn-warning',
                'label' => 'Refund'
            ];
            $singleActions[] = [
                'action' => 'cancel',
                'icon' => 'bx bx-x',
                'class' => 'btn-danger',
                'label' => 'Cancel'
            ];
        }
        return view('components.ui.datatable.data-table-actions', [
            'mode' => 'both',
            'actions' => $actions,
            'id' => $insurance->id,
            'type' => 'Insurance',
            'singleActions' => $singleActions
        ])->render();
    }

    /**
     * Cancel an insurance.
     *
     * @param int $id
     * @return void
     */
    public function cancel($id): void
    {
        $insurance = Insurance::findOrFail($id);
        if ($insurance->status !== 'active') {
            throw new BusinessValidationException('Only active insurances can be cancelled.', 400);
        }
        $insurance->status = 'cancelled';
        $insurance->save();
    }

    /**
     * Refund an insurance.
     *
     * @param int $id
     * @return void
     */
    public function refund($id): void
    {
        $insurance = Insurance::findOrFail($id);
        if ($insurance->status !== 'active') {
            throw new BusinessValidationException('Only active insurances can be refunded.', 400);
        }
        $insurance->status = 'refunded';
        $insurance->save();
    }

    /**
     * Get all insurance records.
     *
     * @return array
     */
    public function getAll(): array
    {
        return Insurance::select(['id', 'student_id', 'academic_term_id', 'amount', 'is_paid'])
            ->get()
            ->toArray();
    }
}
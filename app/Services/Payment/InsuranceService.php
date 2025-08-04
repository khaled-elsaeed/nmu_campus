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
        $stats = Insurance::leftJoin('reservations as r', 'insurances.reservation_id', '=', 'r.id')
            ->leftJoin('users as u', 'r.user_id', '=', 'u.id')
            ->selectRaw("
                COUNT(*) as total_count,
                COUNT(CASE WHEN insurances.status = 'active' THEN 1 END) as active_count,
                COUNT(CASE WHEN insurances.status = 'refunded' THEN 1 END) as refunded_count,
                COUNT(CASE WHEN insurances.status = 'carried_over' THEN 1 END) as carried_over_count,
                COUNT(CASE WHEN insurances.status = 'cancelled' THEN 1 END) as cancelled_count,
                
                COUNT(CASE WHEN u.gender = 'male' THEN 1 END) as total_male,
                COUNT(CASE WHEN u.gender = 'female' THEN 1 END) as total_female,
                
                COUNT(CASE WHEN insurances.status = 'active' AND u.gender = 'male' THEN 1 END) as active_male,
                COUNT(CASE WHEN insurances.status = 'active' AND u.gender = 'female' THEN 1 END) as active_female,
                
                COUNT(CASE WHEN insurances.status = 'refunded' AND u.gender = 'male' THEN 1 END) as refunded_male,
                COUNT(CASE WHEN insurances.status = 'refunded' AND u.gender = 'female' THEN 1 END) as refunded_female,
                
                COUNT(CASE WHEN insurances.status = 'carried_over' AND u.gender = 'male' THEN 1 END) as carried_over_male,
                COUNT(CASE WHEN insurances.status = 'carried_over' AND u.gender = 'female' THEN 1 END) as carried_over_female,
                
                COUNT(CASE WHEN insurances.status = 'cancelled' AND u.gender = 'male' THEN 1 END) as cancelled_male,
                COUNT(CASE WHEN insurances.status = 'cancelled' AND u.gender = 'female' THEN 1 END) as cancelled_female,
                
                MAX(insurances.updated_at) as last_update,
                MAX(CASE WHEN insurances.status = 'active' THEN insurances.updated_at END) as active_last_update,
                MAX(CASE WHEN insurances.status = 'refunded' THEN insurances.updated_at END) as refunded_last_update,
                MAX(CASE WHEN insurances.status = 'carried_over' THEN insurances.updated_at END) as carried_over_last_update,
                MAX(CASE WHEN insurances.status = 'cancelled' THEN insurances.updated_at END) as cancelled_last_update
            ")
            ->first();

        return [
            'insurances' => [
                'count' => formatNumber($stats->total_count),
                'male' => formatNumber($stats->total_male),
                'female' => formatNumber($stats->total_female),
                'lastUpdateTime' => formatDate($stats->last_update),
            ],
            'insurances-active' => [
                'count' => formatNumber($stats->active_count),
                'male' => formatNumber($stats->active_male),
                'female' => formatNumber($stats->active_female),
                'lastUpdateTime' => formatDate($stats->active_last_update),
            ],
            'insurances-refunded' => [
                'count' => formatNumber($stats->refunded_count),
                'male' => formatNumber($stats->refunded_male),
                'female' => formatNumber($stats->refunded_female),
                'lastUpdateTime' => formatDate($stats->refunded_last_update),
            ],
            'insurances-carried-over' => [
                'count' => formatNumber($stats->carried_over_count),
                'male' => formatNumber($stats->carried_over_male),
                'female' => formatNumber($stats->carried_over_female),
                'lastUpdateTime' => formatDate($stats->carried_over_last_update),
            ],
            'insurances-cancelled' => [
                'count' => formatNumber($stats->cancelled_count),
                'male' => formatNumber($stats->cancelled_male),
                'female' => formatNumber($stats->cancelled_female),
                'lastUpdateTime' => formatDate($stats->cancelled_last_update),
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
        $actions = ['delete'];
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
}
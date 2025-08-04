<?php

namespace App\Services\Payment;

use App\Models\Payment;
use App\Models\Reservation\Reservation;
use App\Exceptions\BusinessValidationException;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Create a new payment.
     *
     * @param array $data
     * @return Payment
     */
    public function createPayment(array $data): Payment
    {
        $preparedDetails = $this->preapreDetails($data);

        $reservation = $this->getReservationByNumber($data);

        if(!$reservation){
            throw new BusinessValidationException('Reservation not found.');
        }

        $paymentData = [
            'reservation_id' => $reservation ? $reservation->id : null,
            'amount' => $data['amount'] ?? null,
            'status' => $data['status'] ?? 'pending',
            'notes' => $data['notes'] ?? null,
            'details' => $data['details'] ?? null,
        ];

        return Payment::create($paymentData);
    }

    private function preapreDetails(array $data): array
    {
        if (isset($data['details']) && is_array($data['details'])) {
            foreach ($data['details'] as &$detail) {
                $detail['amount'] = isset($detail['amount']) ? (float)$detail['amount'] : 0.0;
                $detail['type'] = $detail['type'] ?? 'other';
            }
            return $data['details'];
        }
        return [];
    }
    
    private function getReservationByNumber(array $data)
    {
        if (isset($data['reservation_number'])) {
            return Reservation::where('reservation_number', $data['reservation_number'])->first();
        }
        return null;
    }

    /**
     * Update an existing payment.
     *
     * @param Payment $payment
     * @param array $data
     * @return Payment
     */
    public function updatePayment(Payment $payment, array $data): Payment
    {
        $payment->update($data);
        return $payment->fresh();
    }

    /**
     * Get a single payment.
     *
     * @param int $id
     * @return array
     */
    public function getPayment(int $id): array
    {
        $payment = Payment::select([
            'id',
            'reservation_id',
            'amount',
            'payment_method',
            'transaction_id',
            'status'
        ])->find($id);

        if (!$payment) {
            throw new BusinessValidationException('Payment not found.');
        }

        return [
            'id' => $payment->id,
            'reservation_id' => $payment->reservation_id,
            'amount' => $payment->amount,
            'payment_method' => $payment->payment_method,
            'transaction_id' => $payment->transaction_id,
            'status' => $payment->status,
        ];
    }

    /**
     * Delete a payment.
     *
     * @param int $id
     * @return void
     */
    public function deletePayment($id): void
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();
    }

    /**
     * Get all payments.
     *
     * @return array
     */
    public function getAll(): array
    {
        return Payment::select(['id', 'reservation_id', 'amount', 'status'])->get()->toArray();
    }

    /**
     * Get payment statistics.
     *
     * @return array
     */
   public function getStats(): array
    {
        $stats = Payment::leftJoin('reservations as r', 'payments.reservation_id', '=', 'r.id')
            ->leftJoin('users as u', 'r.user_id', '=', 'u.id')
            ->selectRaw("
                COUNT(*) as total_count,
                COUNT(CASE WHEN payments.status = 'pending' THEN 1 END) as pending_count,
                COUNT(CASE WHEN payments.status = 'completed' THEN 1 END) as completed_count,
                COUNT(CASE WHEN payments.status = 'cancelled' THEN 1 END) as cancelled_count,
                
                COUNT(CASE WHEN u.gender = 'male' THEN 1 END) as total_male,
                COUNT(CASE WHEN u.gender = 'female' THEN 1 END) as total_female,
                
                COUNT(CASE WHEN payments.status = 'pending' AND u.gender = 'male' THEN 1 END) as pending_male,
                COUNT(CASE WHEN payments.status = 'pending' AND u.gender = 'female' THEN 1 END) as pending_female,
                
                COUNT(CASE WHEN payments.status = 'completed' AND u.gender = 'male' THEN 1 END) as completed_male,
                COUNT(CASE WHEN payments.status = 'completed' AND u.gender = 'female' THEN 1 END) as completed_female,
                
                COUNT(CASE WHEN payments.status = 'cancelled' AND u.gender = 'male' THEN 1 END) as cancelled_male,
                COUNT(CASE WHEN payments.status = 'cancelled' AND u.gender = 'female' THEN 1 END) as cancelled_female,
                
                MAX(payments.updated_at) as last_update,
                MAX(CASE WHEN payments.status = 'pending' THEN payments.updated_at END) as pending_last_update,
                MAX(CASE WHEN payments.status = 'completed' THEN payments.updated_at END) as completed_last_update,
                MAX(CASE WHEN payments.status = 'cancelled' THEN payments.updated_at END) as cancelled_last_update
            ")
            ->first();

        return [
            'payments' => [
            'count' => formatNumber($stats->total_count),
            'male' => formatNumber($stats->total_male),
            'female' => formatNumber($stats->total_female),
            'lastUpdateTime' => $stats->last_update,
            ],
            'payments-pending' => [
            'count' => formatNumber($stats->pending_count),
            'male' => formatNumber($stats->pending_male),
            'female' => formatNumber($stats->pending_female),
            'lastUpdateTime' => $stats->pending_last_update,
            ],
            'payments-completed' => [
            'count' => formatNumber($stats->completed_count),
            'male' => formatNumber($stats->completed_male),
            'female' => formatNumber($stats->completed_female),
            'lastUpdateTime' => $stats->completed_last_update,
            ],
            'payments-cancelled' => [
            'count' => formatNumber($stats->cancelled_count),
            'male' => formatNumber($stats->cancelled_male),
            'female' => formatNumber($stats->cancelled_female),
            'lastUpdateTime' => $stats->cancelled_last_update,
            ],
        ];
    }

    /**
     * Get payment data for DataTables.
     *
     * @return JsonResponse
     */
    public function getDatatable(): JsonResponse
    {
        $query = Payment::with(['reservation.user']);
        $query = $this->applySearchFilters($query);
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('reservation_number', function ($payment) {
                return $payment->reservation ? $payment->reservation->reservation_number : '--';
            })
            ->addColumn('user_name', function ($payment) {
                return $payment->reservation && $payment->reservation->user ? $payment->reservation->user->name : '--';
            })
            ->addColumn('details', function ($payment) {
                $count = $payment->details ? $payment->details->count() : 0;
                return '<button class="btn btn-sm btn-info viewDetailsBtn" data-id="' . $payment->id . '"><i class="bx bx-list-ul"></i> ' . $count . '</button>';
            })
            ->editColumn('status', fn($payment) => ucfirst($payment->status))
            ->editColumn('amount', fn($payment) => number_format($payment->amount, 2))
            ->editColumn('created_at', fn($payment) => $payment->created_at ? $payment->created_at->format('Y-m-d H:i') : null)
            ->addColumn('action', fn($payment) => $this->renderActionButtons($payment))
            ->orderColumn('amount', 'amount $1')
            ->orderColumn('status', 'status $1')
            ->rawColumns(['action', 'details'])
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
        $searchReservation = request('search_reservation_id');
        if (!empty($searchReservation)) {
            $query->where('reservation_id', $searchReservation);
        }
        return $query;
    }

    /**
     * Render action buttons for datatable rows.
     *
     * @param Payment $payment
     * @return string
     */
    public function renderActionButtons(Payment $payment): string
    {
        $actions = ['view', 'delete'];
        if ($payment->status === 'pending') {
            array_splice($actions, 1, 0, 'edit'); // Insert 'edit' after 'view'
        }
        $singleActions = [];
        if ($payment->status === 'pending') {
            $singleActions[] = [
                'action' => 'complete',
                'icon' => 'bx bx-check',
                'class' => 'btn-success',
                'label' => 'Mark as Completed'
            ];
        }
        if ($payment->status === 'completed') {
            $singleActions[] = [
                'action' => 'refund',
                'icon' => 'bx bx-undo',
                'class' => 'btn-warning',
                'label' => 'Refund'
            ];
        }
        if ($payment->status !== 'cancelled') {
            $singleActions[] = [
                'action' => 'cancel',
                'icon' => 'bx bx-block',
                'class' => 'btn-danger',
                'label' => 'Cancel'
            ];
        }
        return view('components.ui.datatable.data-table-actions', [
            'mode' => 'both',
            'actions' => $actions,
            'id' => $payment->id,
            'type' => 'Payment',
            'singleActions' => $singleActions
        ])->render();
    }

    /**
     * Get only the details array for a payment.
     *
     * @param int $id
     * @return array
     */
    public function getDetails($id): array
    {
        $payment = Payment::findOrFail($id);
        return $payment->details ? $payment->details->toArray() : [];
    }
}
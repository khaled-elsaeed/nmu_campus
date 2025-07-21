<?php

namespace App\Services\Payment;

use App\Models\Payment;
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
        return Payment::create($data);
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
     * @return Payment
     */
    public function getPayment($id): Payment
    {
        return Payment::findOrFail($id);
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
        return Payment::get()->map(function ($payment) {
            return [
                'id' => $payment->id,
                'reservation_id' => $payment->reservation_id,
                'amount' => $payment->amount,
                'status' => $payment->status,
                'notes' => $payment->notes,
                'details' => $payment->details,
                'completed_at' => $payment->completed_at,
                'refunded_at' => $payment->refunded_at,
                'cancelled_at' => $payment->cancelled_at,
                'created_at' => $payment->created_at,
                'updated_at' => $payment->updated_at,
            ];
        })->toArray();
    }

    /**
     * Get payment statistics.
     *
     * @return array
     */
    public function getStats(): array
    {
        $total = Payment::count();
        $pending = Payment::where('status', 'pending')->count();
        $completed = Payment::where('status', 'completed')->count();
        $refunded = Payment::where('status', 'refunded')->count();
        $cancelled = Payment::where('status', 'cancelled')->count();
        $lastUpdate = Payment::max('updated_at');
        $pendingLastUpdate = Payment::where('status', 'pending')->max('updated_at');
        $completedLastUpdate = Payment::where('status', 'completed')->max('updated_at');
        $refundedLastUpdate = Payment::where('status', 'refunded')->max('updated_at');
        $cancelledLastUpdate = Payment::where('status', 'cancelled')->max('updated_at');
        return [
            'total' => [
                'count' => $total,
                'lastUpdateTime' => $lastUpdate,
            ],
            'pending' => [
                'count' => $pending,
                'lastUpdateTime' => $pendingLastUpdate,
            ],
            'completed' => [
                'count' => $completed,
                'lastUpdateTime' => $completedLastUpdate,
            ],
            'refunded' => [
                'count' => $refunded,
                'lastUpdateTime' => $refundedLastUpdate,
            ],
            'cancelled' => [
                'count' => $cancelled,
                'lastUpdateTime' => $cancelledLastUpdate,
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
<?php

namespace App\Services\Reservation\Pipeline\Services\Cancel;

use App\Models\Payment\Payment;

class PaymentService
{
    /**
     * Cancel payment for a reservation.
     *
     * @param int $reservationId
     * @return void
     */
    public function cancelPayment(int $reservationId): void
    {
        $payment = Payment::where('reservation_id', $reservationId)
            ->where('type', 'reservation_fee')
            ->where('status', 'pending')
            ->first();

        if ($payment) {
            $payment->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);
        }
    }
}

<?php

namespace App\Services\Reservation\Cancel;

use App\Models\Payment;
use App\Models\Reservation\Reservation;
use App\Models\Equipment;
use App\Exceptions\BusinessValidationException;

class PaymentService
{
    /**
     * Cancel a payment record for equipment damages.
     *
     * @param int $reservationId
     * @throws BusinessValidationException
     */
    public function cancelPayment(int $reservationId): void
    {
        $reservation = Reservation::find($reservationId);

        if (!$reservation) {
            throw new BusinessValidationException('Reservation not found.');
        }

        $payment = $reservation->payments()->where('status', '!=', 'cancelled')->first();

        if (!$payment) {
            throw new BusinessValidationException('Payment not found.');
        }

        $this->cancelInsurance($reservationId);

        $payment->status = 'cancelled';
        $payment->cancelled_at = now();
        $payment->save();
    }

    private function cancelInsurance(int $reservationId): void
    {
        $reservation = Reservation::find($reservationId);

        if (!$reservation) {
            throw new BusinessValidationException('Reservation not found.');
        }

        $insurance = $reservation->insurance;

        if ($insurance) {
            $insurance->status = 'cancelled';
            $insurance->save();
        }
    }

}
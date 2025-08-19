<?php

namespace App\Services\Reservation\Pipeline\Services\Cancel;

use App\Models\Reservation\Reservation;
use App\Exceptions\BusinessValidationException;

class ReservationValidator
{
    /**
     * Validate reservation before cancellation.
     *
     * @param int $reservationId
     * @throws BusinessValidationException
     */
    public function validateBeforeCancel(int $reservationId): void
    {
        $reservation = Reservation::findOrFail($reservationId);

        if ($reservation->status === 'checked_in') {
            throw new BusinessValidationException(__('Cannot cancel a reservation that has been checked in.'));
        }

        if ($reservation->status === 'cancelled') {
            throw new BusinessValidationException(__('Reservation is already cancelled.'));
        }

        if ($reservation->status === 'completed') {
            throw new BusinessValidationException(__('Cannot cancel a completed reservation.'));
        }
    }
}

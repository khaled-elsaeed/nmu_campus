<?php

namespace App\Services\Reservation\Cancel;

use App\Models\Reservation\Reservation;
use App\Exceptions\BusinessValidationException;
use Carbon\Carbon;

class ReservationValidator
{
    /**
     * Validate reservation before completing or checking out.
     *
     * @param int $reservationId
     * @throws BusinessValidationException
     */
    public function validateBeforeCancel(int $reservationId): void
    {
        $reservation = Reservation::find($reservationId);

        if (!$reservation) {
            throw new BusinessValidationException('Reservation not found.');
        }

        // Only allow cancellation if reservation is in a valid state
        if (!$reservation->status === 'pending' || $reservation->status === 'checked_in') {
            throw new BusinessValidationException(__('Reservation cannot be canceled in its current status.'));
        }
    }
}
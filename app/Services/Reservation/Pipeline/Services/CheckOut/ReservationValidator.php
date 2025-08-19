<?php

namespace App\Services\Reservation\Pipeline\Services\CheckOut;

use App\Models\Reservation\Reservation;
use App\Exceptions\BusinessValidationException;

class ReservationValidator
{
    /**
     * Validate reservation before check-out.
     *
     * @param array $data
     * @throws BusinessValidationException
     */
    public function validateBeforeCheckOut(array $data): void
    {
        $reservation = Reservation::findOrFail($data['reservation_id']);

        if ($reservation->status !== 'checked_in') {
            throw new BusinessValidationException(__('Reservation must be checked in before check-out.'));
        }

        if (!$reservation->active) {
            throw new BusinessValidationException(__('Reservation is not active.'));
        }
    }
}

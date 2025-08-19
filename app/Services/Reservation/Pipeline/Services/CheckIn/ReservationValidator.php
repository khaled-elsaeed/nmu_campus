<?php

namespace App\Services\Reservation\Pipeline\Services\CheckIn;

use App\Models\Reservation\Reservation;
use App\Exceptions\BusinessValidationException;

class ReservationValidator
{
    /**
     * Validate reservation before check-in.
     *
     * @param array $data
     * @throws BusinessValidationException
     */
    public function validateBeforeCheckIn(array $data): void
    {
        $reservation = Reservation::findOrFail($data['reservation_id']);

        if ($reservation->status !== 'confirmed') {
            throw new BusinessValidationException(__('Reservation must be confirmed before check-in.'));
        }

        if ($reservation->active) {
            throw new BusinessValidationException(__('Reservation is already active.'));
        }

        // Check if check-in date is valid
        if ($reservation->check_in_date && now()->lt($reservation->check_in_date)) {
            throw new BusinessValidationException(__('Check-in date has not been reached yet.'));
        }
    }
}

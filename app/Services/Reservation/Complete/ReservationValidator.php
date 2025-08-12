<?php

namespace App\Services\Reservation\Complete;

use App\Models\Reservation\Reservation;
use App\Exceptions\BusinessValidationException;
use Carbon\Carbon;

class ReservationValidator
{
    /**
     * Validate reservation before completing or checking out.
     *
     * @param Reservation $reservation
     * @throws BusinessValidationException
     */
    public function validateBeforeComplete(array $data): void
    {
        $reservation = Reservation::find($data['reservation_id']);

        if (!$reservation) {
            throw new BusinessValidationException('Reservation not found.');
        }

        // Only allow completion if reservation is in a valid state
        if (!in_array($reservation->status, ['confirmed', 'checked_in'])) {
            throw new BusinessValidationException(__('Reservation cannot be completed in its current status.'));
        }

        // Check if already completed or checked out
        if ($reservation->status === 'checked_out' || $reservation->status === 'completed') {
            throw new BusinessValidationException(__('Reservation has already been completed or checked out.'));
        }

        // Check if check-out date is not in the future
        if ($reservation->check_out_date) {
            $now = Carbon::now()->startOfDay();
            $checkOut = Carbon::parse($reservation->check_out_date)->startOfDay();
            if ($checkOut->gt($now)) {
                throw new BusinessValidationException(__('Cannot complete reservation before the check-out date.'));
            }
        }
    }
}
<?php

namespace App\Services\Reservation\CheckIn;

use App\Models\Reservation\Reservation;
use App\Exceptions\BusinessValidationException;
use Carbon\Carbon;

class ReservationValidator
{
    /**
     * Validate reservation before checking in.
     *
     * @param Reservation $reservation
     * @throws BusinessValidationException
     */
    public function validateBeforeCheckIn(array $data): void
    {
        $reservation = Reservation::find($data['reservation_id']);

        if (!$reservation) {
            throw new BusinessValidationException('Reservation not found.');
        }

        
        // Check if already checked in
        if ($reservation->status === 'checked_in') {
            throw new BusinessValidationException(__('Reservation has already been checked in.'));
        }

        // Only allow check-in if reservation is confirmed
        if ($reservation->status !== "confirmed") {
            throw new BusinessValidationException(__('Reservation can only be checked in if it is confirmed.'));
        }
        // Check if already checked in
        if ($reservation->check_in_date) {
            $now = Carbon::now()->startOfDay();
            $checkIn = Carbon::parse($reservation->check_in_date)->startOfDay();
            if (!$checkIn->equalTo($now)) {
                throw new BusinessValidationException(__('Reservation can only be checked in on the check-in date.'));
            }
        }
    }
}
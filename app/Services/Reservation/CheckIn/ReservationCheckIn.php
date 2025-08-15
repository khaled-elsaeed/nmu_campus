<?php

namespace App\Services\Reservation\CheckIn;

use App\Models\Reservation\Reservation;
use Carbon\Carbon;

class ReservationCheckIn
{
    /**
     * Mark a reservation as checked in.
     *
     * @param array $data Array containing at least 'reservation_id'
     */
    public function checkInReservation(array $data): void
    {
        $reservationId = $data['reservation_id'] ?? null;

        $reservation = Reservation::findOrFail($reservationId);

        $reservation->status = 'checked_in';
        $reservation->active = true;
        $reservation->checked_in_at = Carbon::now();
        $reservation->save();
    }
}

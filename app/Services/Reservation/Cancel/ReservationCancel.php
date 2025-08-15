<?php

namespace App\Services\Reservation\Cancel;

use App\Models\Reservation\Reservation;
use Carbon\Carbon;

class ReservationCancel
{
    /**
     * Mark a reservation as canceled.
     *
     * @param int $reservationId
     */
    public function cancelReservation(int $reservationId): void
     {
        $reservation = Reservation::findOrFail($reservationId);
        $reservation->status = 'cancelled';
        $reservation->active = false;
        $reservation->cancelled_at = Carbon::now();
        $reservation->save();
    }
}

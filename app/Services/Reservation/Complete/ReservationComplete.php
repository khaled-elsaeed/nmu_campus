<?php

namespace App\Services\Reservation\Complete;

use App\Models\Reservation\Reservation;
use Carbon\Carbon;

class ReservationComplete
{
    /**
     * Mark a reservation as completed (ended).
     *
     * @param array $data Array containing at least 'reservation_id'
     * @return Reservation
     */
    public function completeReservation(array $data): Reservation
    {
        $reservationId = $data['reservation_id'] ?? null;

        $reservation = Reservation::findOrFail($reservationId);

        $reservation->status = 'checked_out';
        $reservation->active = false;
        $reservation->checked_out_at = Carbon::now();
        $reservation->save();

        return $reservation;
    }
}

<?php

namespace App\Services\Reservation\CheckOut;

use App\Models\Reservation\Reservation;
use Carbon\Carbon;

class ReservationCheckOut
{
    /**
     * Mark a reservation as checked out.
     *
     * @param array $data Array containing at least 'reservation_id'
     * @return Reservation
     */
    public function checkOutReservation(array $data): Reservation
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

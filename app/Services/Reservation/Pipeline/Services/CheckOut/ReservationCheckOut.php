<?php

namespace App\Services\Reservation\Pipeline\Services\CheckOut;

use App\Models\Reservation\Reservation;

class ReservationCheckOut
{
    /**
     * Check out a reservation.
     *
     * @param array $data
     * @return Reservation
     */
    public function checkOutReservation(array $data): Reservation
    {
        $reservation = Reservation::findOrFail($data['reservation_id']);
        
        $reservation->update([
            'status' => 'checked_out',
            'active' => false,
            'check_out_date' => now(),
        ]);

        return $reservation->fresh();
    }
}

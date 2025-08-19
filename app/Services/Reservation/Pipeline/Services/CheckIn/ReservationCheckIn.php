<?php

namespace App\Services\Reservation\Pipeline\Services\CheckIn;

use App\Models\Reservation\Reservation;

class ReservationCheckIn
{
    /**
     * Check in a reservation.
     *
     * @param array $data
     * @return void
     */
    public function checkInReservation(array $data): void
    {
        $reservation = Reservation::findOrFail($data['reservation_id']);
        
        $reservation->update([
            'status' => 'checked_in',
            'active' => true,
            'check_in_date' => now(),
        ]);
    }
}

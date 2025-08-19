<?php

namespace App\Services\Reservation\Pipeline\Services\Cancel;

use App\Models\Reservation\Reservation;

class ReservationCancel
{
    /**
     * Cancel a reservation.
     *
     * @param int $reservationId
     * @return void
     */
    public function cancelReservation(int $reservationId): void
    {
        $reservation = Reservation::findOrFail($reservationId);
        
        $reservation->update([
            'status' => 'cancelled',
            'active' => false,
            'cancelled_at' => now(),
        ]);
    }
}

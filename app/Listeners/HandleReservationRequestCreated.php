<?php

namespace App\Listeners;

use App\Events\ReservationRequestCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class HandleReservationRequestCreated
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ReservationRequestCreated $event): void
    {
        $reservationRequest = $event->reservationRequest;
        log::info('Reservation request created:', $reservationRequest->toArray());
    }
}

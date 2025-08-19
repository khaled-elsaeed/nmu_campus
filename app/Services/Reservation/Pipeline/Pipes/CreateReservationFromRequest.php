<?php

namespace App\Services\Reservation\Pipeline\Pipes;

use Closure;
use App\Services\Reservation\Shared\ReservationCreator;

class CreateReservationFromRequest
{
    public function __construct(
        protected ReservationCreator $creator
    ) {}

    /**
     * Handle the incoming request.
     *
     * @param array $data
     * @param Closure $next
     * @return mixed
     */
    public function handle(array $data, Closure $next)
    {
        $request = $data['reservation_request'];
        
        $reservation = $this->creator->create(
            userId: $request->user_id,
            academicTermId: $request->academic_term_id,
            checkInDate: $request->check_in_date,
            checkOutDate: $request->check_out_date,
            status: 'pending',
            active: false,
            notes: $data['notes'] ?? null,
            periodType: $request->period_type
        );

        $data['reservation'] = $reservation;
        $data['reservation_id'] = $reservation->id;

        return $next($data);
    }
}

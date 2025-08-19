<?php

namespace App\Services\Reservation\Pipeline\Pipes\CheckOut;

use Closure;
use App\Services\Reservation\Pipeline\Services\CheckOut\ReservationCheckOut;

class ProcessCheckOut
{
    public function __construct(
        protected ReservationCheckOut $checkOutService
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
        $reservation = $this->checkOutService->checkOutReservation($data);
        $data['reservation'] = $reservation;
        return $next($data);
    }
}

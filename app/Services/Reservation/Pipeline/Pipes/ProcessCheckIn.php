<?php

namespace App\Services\Reservation\Pipeline\Pipes;

use Closure;
use App\Services\Reservation\CheckIn\ReservationCheckIn;

class ProcessCheckIn
{
    public function __construct(
        protected ReservationCheckIn $checkInService
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
        $this->checkInService->checkInReservation($data);
        return $next($data);
    }
}

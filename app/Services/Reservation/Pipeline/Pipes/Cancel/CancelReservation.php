<?php

namespace App\Services\Reservation\Pipeline\Pipes\Cancel;

use Closure;
use App\Services\Reservation\Pipeline\Services\Cancel\ReservationCancel;

class CancelReservation
{
    public function __construct(
        protected ReservationCancel $cancelService
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
        $this->cancelService->cancelReservation($data['reservation_id']);
        return $next($data);
    }
}

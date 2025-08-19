<?php

namespace App\Services\Reservation\Pipeline\Pipes\Request;

use Closure;
use App\Services\Reservation\Pipeline\Services\Shared\ReservationValidator;

class ValidateRequestDuplicates
{
    public function __construct(
        protected ReservationValidator $validator
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
        
        $this->validator->checkForDuplicateReservation(
            $request->user_id,
            $request->period_type,
            $request->academic_term_id,
            $request->check_in_date,
            $request->check_out_date
        );

        return $next($data);
    }
}

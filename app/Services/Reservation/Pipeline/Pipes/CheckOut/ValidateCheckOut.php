<?php

namespace App\Services\Reservation\Pipeline\Pipes\CheckOut;

use Closure;
use App\Services\Reservation\Pipeline\Services\CheckOut\ReservationValidator;
use App\Exceptions\BusinessValidationException;

class ValidateCheckOut
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
     * @throws BusinessValidationException
     */
    public function handle(array $data, Closure $next)
    {
        $this->validator->validateBeforeCheckOut($data);
        return $next($data);
    }
}

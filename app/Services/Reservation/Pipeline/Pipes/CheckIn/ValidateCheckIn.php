<?php

namespace App\Services\Reservation\Pipeline\Pipes\CheckIn;

use Closure;
use App\Services\Reservation\Pipeline\Services\CheckIn\ReservationValidator;
use App\Exceptions\BusinessValidationException;

class ValidateCheckIn
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
        $this->validator->validateBeforeCheckIn($data);
        return $next($data);
    }
}

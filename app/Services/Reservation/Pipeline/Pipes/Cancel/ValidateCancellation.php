<?php

namespace App\Services\Reservation\Pipeline\Pipes\Cancel;

use Closure;
use App\Services\Reservation\Pipeline\Services\Cancel\ReservationValidator;
use App\Exceptions\BusinessValidationException;

class ValidateCancellation
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
        $this->validator->validateBeforeCancel($data['reservation_id']);
        return $next($data);
    }
}

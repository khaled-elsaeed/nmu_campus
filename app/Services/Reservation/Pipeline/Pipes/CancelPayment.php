<?php

namespace App\Services\Reservation\Pipeline\Pipes;

use Closure;
use App\Services\Reservation\Cancel\PaymentService;

class CancelPayment
{
    public function __construct(
        protected PaymentService $paymentService
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
        $this->paymentService->cancelPayment($data['reservation_id']);
        return $next($data);
    }
}

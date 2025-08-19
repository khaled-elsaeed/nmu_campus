<?php

namespace App\Services\Reservation\Pipeline\Pipes\Shared;

use Closure;
use App\Services\Reservation\Pipeline\Services\Shared\PaymentService;

class CreatePaymentRecord
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
        // Create payment record if reservation exists
        if (isset($data['reservation'])) {
            $payment = $this->paymentService->createPaymentRecord(
                $data['reservation'],
                $data['payment_notes'] ?? null
            );

            $data['payment'] = $payment;
        }

        return $next($data);
    }
}

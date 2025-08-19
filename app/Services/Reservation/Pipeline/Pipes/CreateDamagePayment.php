<?php

namespace App\Services\Reservation\Pipeline\Pipes;

use Closure;
use App\Services\Reservation\CheckOut\PaymentService;

class CreateDamagePayment
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
        // Create damage payment if there are damages
        if (!empty($data['damages']) && isset($data['reservation'])) {
            $this->paymentService->createDamagePayment([
                'reservation_id' => $data['reservation']->id,
                'damages' => $data['damages'],
            ]);
        }

        return $next($data);
    }
}

<?php

namespace App\Services\Reservation\Pipeline\Pipes;

use Closure;
use App\Services\Reservation\CheckOut\EquipmentReturnService;

class ReturnEquipment
{
    public function __construct(
        protected EquipmentReturnService $equipmentService
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
        $damages = $this->equipmentService->returnEquipment($data);
        $data['damages'] = $damages;
        return $next($data);
    }
}

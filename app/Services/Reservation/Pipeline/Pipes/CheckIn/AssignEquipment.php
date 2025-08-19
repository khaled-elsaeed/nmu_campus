<?php

namespace App\Services\Reservation\Pipeline\Pipes\CheckIn;

use Closure;
use App\Services\Reservation\Pipeline\Services\CheckIn\EquipmentAssignmentService;

class AssignEquipment
{
    public function __construct(
        protected EquipmentAssignmentService $equipmentService
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
        $this->equipmentService->assignEquipmentIfProvided($data);
        return $next($data);
    }
}

<?php

namespace App\Services\Reservation\Pipeline\Operations;

use App\Models\Reservation\Reservation;
use App\Services\Reservation\Pipeline\ReservationPipeline;
use App\Services\Reservation\Pipeline\Pipes\CheckOut\ValidateCheckOut;
use App\Services\Reservation\Pipeline\Pipes\CheckOut\ReturnEquipment;
use App\Services\Reservation\Pipeline\Pipes\CheckOut\ProcessCheckOut;
use App\Services\Reservation\Pipeline\Pipes\CheckOut\CreateDamagePayment;

class CheckOutReservation extends ReservationPipeline
{
    /**
     * Check out a reservation using the pipeline pattern.
     *
     * @param array $data
     * @return Reservation
     */
    public function execute(array $data): Reservation
    {
        $pipes = [
            ValidateCheckOut::class,
            ReturnEquipment::class,
            ProcessCheckOut::class,
            CreateDamagePayment::class,
        ];

        $result = $this->executePipelineWithFinally(
            $data,
            $pipes,
            function ($result, $data) {
                $this->logPipelineExecution('check_out_reservation', $data, $result);
            }
        );

        return $result['reservation']->load(['equipmentTracking']);
    }
}

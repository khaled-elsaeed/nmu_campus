<?php

namespace App\Services\Reservation\Pipeline\Operations;

use App\Services\Reservation\Pipeline\ReservationPipeline;
use App\Services\Reservation\Pipeline\Pipes\CheckIn\ValidateCheckIn;
use App\Services\Reservation\Pipeline\Pipes\CheckIn\AssignEquipment;
use App\Services\Reservation\Pipeline\Pipes\CheckIn\ProcessCheckIn;

class CheckInReservation extends ReservationPipeline
{
    /**
     * Check in a reservation using the pipeline pattern.
     *
     * @param array $data
     */
    public function execute(array $data): void
    {
        $pipes = [
            ValidateCheckIn::class,
            AssignEquipment::class,
            ProcessCheckIn::class,
        ];

        $this->executePipelineWithFinally(
            $data,
            $pipes,
            function ($result, $data) {
                $this->logPipelineExecution('check_in_reservation', $data, $result);
            }
        );
    }
}

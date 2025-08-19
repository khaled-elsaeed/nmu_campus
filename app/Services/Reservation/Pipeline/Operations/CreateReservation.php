<?php

namespace App\Services\Reservation\Pipeline\Operations;

use App\Models\Reservation\Reservation;
use App\Services\Reservation\Pipeline\ReservationPipeline;
use App\Services\Reservation\Pipeline\Pipes\Shared\ValidateReservationData;
use App\Services\Reservation\Pipeline\Pipes\Shared\CreateReservationRecord;
use App\Services\Reservation\Pipeline\Pipes\Shared\CreateAccommodation;
use App\Services\Reservation\Pipeline\Pipes\Shared\CreatePaymentRecord;

class CreateReservation extends ReservationPipeline
{
    /**
     * Create a new reservation using the pipeline pattern.
     *
     * @param array $data
     * @return Reservation
     */
    public function execute(array $data): Reservation
    {
        $pipes = [
            ValidateReservationData::class,
            CreateReservationRecord::class,
            CreateAccommodation::class,
            CreatePaymentRecord::class,
        ];

        $result = $this->executePipelineWithFinally(
            $data,
            $pipes,
            function ($result, $data) {
                $this->logPipelineExecution('create_reservation', $data, $result);
            }
        );

        return $result['reservation'];
    }
}

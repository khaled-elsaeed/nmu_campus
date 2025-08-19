<?php

namespace App\Services\Reservation\Pipeline\Operations;

use App\Services\Reservation\Pipeline\ReservationPipeline;
use App\Services\Reservation\Pipeline\Pipes\Cancel\ValidateCancellation;
use App\Services\Reservation\Pipeline\Pipes\Cancel\CancelReservation as CancelReservationPipe;
use App\Services\Reservation\Pipeline\Pipes\Cancel\CancelPayment;

class CancelReservation extends ReservationPipeline
{
    /**
     * Cancel a reservation using the pipeline pattern.
     *
     * @param int $reservationId
     */
    public function execute(int $reservationId): void
    {
        $data = ['reservation_id' => $reservationId];
        
        $pipes = [
            ValidateCancellation::class,
            CancelReservationPipe::class,
            CancelPayment::class,
        ];

        $this->executePipelineWithFinally(
            $data,
            $pipes,
            function ($result, $data) {
                $this->logPipelineExecution('cancel_reservation', $data, $result);
            }
        );
    }
}

<?php

namespace App\Services\Reservation\Pipeline\Operations;

use App\Models\Reservation\Reservation;
use App\Services\Reservation\Pipeline\ReservationPipeline;
use App\Services\Reservation\Pipeline\Pipes\Request\FindReservationRequest;
use App\Services\Reservation\Pipeline\Pipes\Request\ValidateRequestDuplicates;
use App\Services\Reservation\Pipeline\Pipes\Request\CreateReservationFromRequest;
use App\Services\Reservation\Pipeline\Pipes\Request\CreateRequestAccommodation;
use App\Services\Reservation\Pipeline\Pipes\Request\UpdateRequestStatus;

class AcceptReservationRequest extends ReservationPipeline
{
    /**
     * Accept a reservation request using the pipeline pattern.
     *
     * @param int $reservationRequestId
     * @param string $accommodationType
     * @param int $roomId
     * @param int|null $apartmentId
     * @param int|null $bedCount
     * @param string|null $notes
     * @return Reservation
     */
    public function execute(
        int $reservationRequestId,
        string $accommodationType,
        int $roomId,
        ?int $apartmentId = null,
        ?int $bedCount = null,
        ?string $notes = null
    ): Reservation {
        $data = [
            'reservation_request_id' => $reservationRequestId,
            'accommodation_type' => $accommodationType,
            'room_id' => $roomId,
            'apartment_id' => $apartmentId,
            'bed_count' => $bedCount,
            'notes' => $notes,
        ];

        $pipes = [
            FindReservationRequest::class,
            ValidateRequestDuplicates::class,
            CreateReservationFromRequest::class,
            CreateRequestAccommodation::class,
            UpdateRequestStatus::class,
        ];

        $result = $this->executePipelineWithFinally(
            $data,
            $pipes,
            function ($result, $data) {
                $this->logPipelineExecution('accept_reservation_request', $data, $result);
            }
        );

        return $result['reservation']->fresh();
    }
}

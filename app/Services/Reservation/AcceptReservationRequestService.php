<?php

namespace App\Services\Reservation;

use App\Models\Reservation\Reservation;
use App\Models\Reservation\ReservationRequest;
use App\Services\Reservation\Create\ReservationValidator;
use App\Services\Reservation\Create\ReservationCreator;
use App\Services\Reservation\Shared\AccommodationService;

use App\Exceptions\BusinessValidationException;

class AcceptReservationRequestService
{
    public function __construct(
        protected ReservationValidator $validator,
        protected ReservationCreator $creator,
        protected AccommodationService $accommodationService,
    ) {}

    /**
     * Accept a reservation request (main entry point).
     *
     * @param array $data
     * @param int $reservationRequestId
     * @return Reservation
     * @throws BusinessValidationException
     */
    public function accept(array $data, int $reservationRequestId): Reservation
    {
        // Validate for duplicates
        $this->validator->checkForDuplicateReservation($data, $reservationRequestId);
        
        // Get reservation request data
        $requestData = $this->getRequestDataForReservationCreation($reservationRequestId, $data);
        
        // Create reservation record
        $reservation = $this->creator->createReservationRecord($requestData);
        
        // Handle accommodation creation
        $this->accommodationService->handleAccommodationCreation($data, $reservation->id);
        
        
        return $reservation->fresh();
    }

    /**
     * Prepare reservation data from reservation request
     *
     * @param int $reservationRequestId
     * @param array $additionalData
     * @return array
     * @throws BusinessValidationException
     */
    private function getRequestDataForReservationCreation(int $reservationRequestId, array $additionalData = []): array
    {
        $reservationRequest = ReservationRequest::findOrFail($reservationRequestId);

        if (!$reservationRequest->user) {
            throw new BusinessValidationException(__('Reservation request must have a valid user.'));
        }

        return [
            'user_id' => $reservationRequest->user->id,
            'reservation_request_id' => $reservationRequestId,
            'academic_term_id' => $reservationRequest->academic_term_id,
            'check_in_date' => $reservationRequest->requested_check_in_date,
            'check_out_date' => $reservationRequest->requested_check_out_date,
            'status' => 'approved',
            'accommodation_type' => $reservationRequest->accommodation_type,
            'accommodation_id' => $reservationRequest->accommodation_id,
            'double_room_bed_option' => $reservationRequest->double_room_bed_option,
            'period_type' => $reservationRequest->academic_term_id ? 'academic' : 'calendar',
        ];
    }
}
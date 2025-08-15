<?php

namespace App\Services\Reservation\Create;

use App\Models\Reservation\Reservation;
use App\Models\Reservation\ReservationRequest;
use App\Exceptions\BusinessValidationException;
use Carbon\Carbon;

class ReservationValidator
{
    /**
     * Check for duplicate reservations based on academic term or date overlap
     *
     * @param array $data
     * @param int $reservationRequestId
     * @throws BusinessValidationException
     */
    public function checkForDuplicateReservation(array $data, int $reservationRequestId): void
    {
        $this->validateInputData($data);

        $reservationRequest = ReservationRequest::findOrFail($reservationRequestId);

        $conflictingReservation = Reservation::findConflictingReservation(
            $reservationRequest->user_id,
            $reservationRequest->academic_term_id,
            $reservationRequest->requested_check_in_date,
            $reservationRequest->requested_check_out_date
        );

        if ($conflictingReservation) {
            $this->throwConflictException($conflictingReservation);
        }
    }

    /**
     * Validate input data requirements
     *
     * @param array $data
     * @throws BusinessValidationException
     */
    private function validateInputData(array $data): void
    {
        $requiredFields = ['building_id', 'apartment_id', 'room_id'];
        
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new BusinessValidationException(
                    __("validation.required", ['attribute' => str_replace('_', ' ', $field)])
                );
            }
        }
    }


    /**
     * Throw appropriate conflict exception with detailed message
     *
     * @param Reservation $conflictingReservation
     * @param int|null $academicTermId
     * @throws BusinessValidationException
     */
    private function throwConflictException(Reservation $conflictingReservation): void
    {
        if ($conflictingReservation->academic_term_id) {
            throw new BusinessValidationException(__('A reservation already exists for this academic term.'));
        }

        if ($conflictingReservation->check_in_date && $conflictingReservation->check_out_date) {
            $startDate = Carbon::parse($conflictingReservation->check_in_date)->format('M j, Y');
            $endDate = Carbon::parse($conflictingReservation->check_out_date)->format('M j, Y');
            
            throw new BusinessValidationException(
                __('A reservation already exists with overlapping dates (:start_date - :end_date)', [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ])
            );
        }

        throw new BusinessValidationException(__('A conflicting reservation already exists for this user.'));
    }
}
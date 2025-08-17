<?php

namespace App\Services\Reservation\Operation;

use App\Models\Reservation\Reservation;
use App\Models\Reservation\ReservationRequest;
use App\Services\Reservation\Shared\ReservationValidator;
use App\Services\Reservation\Shared\ReservationCreator;
use App\Services\Reservation\Shared\AccommodationService;
use App\Exceptions\BusinessValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class AcceptReservationRequestService
{
    public function __construct(
        protected ReservationValidator $validator,
        protected ReservationCreator $reservationCreator,
        protected AccommodationService $accommodationService,
    ) {}

    /**
     * Accept a reservation request (main entry point).
     *
     * @param int $reservationRequestId
     * @param string $accommodationType
     * @param int $roomId
     * @param int|null $apartmentId
     * @param int|null $bedCount
     * @param string|null $notes
     * @return Reservation
     * @throws BusinessValidationException
     * @throws ModelNotFoundException
     */
    public function accept(
        int $reservationRequestId,
        string $accommodationType,
        int $roomId,
        ?int $apartmentId = null,
        ?int $bedCount = null,
        ?string $notes = null
    ): Reservation {
        return DB::transaction(function () use (
            $reservationRequestId,
            $accommodationType,
            $roomId,
            $apartmentId,
            $bedCount,
            $notes
        ) {
            // Find and validate reservation request
            $reservationRequest = $this->findReservationRequest($reservationRequestId);

            // Validate for duplicates
            $this->validateForDuplicates($reservationRequest);

            // Create reservation record
            $reservation = $this->createReservation($reservationRequest, $notes);

            // Handle accommodation creation
            $this->createAccommodation(
                $reservation,
                $reservationRequest,
                $accommodationType,
                $roomId,
                $apartmentId,
                $bedCount,
                $notes
            );

            // Update the reservation request status and link to reservation
            $reservationRequest->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'approved_at' => now(),
                'reservation_id' => $reservation->id
            ]);

            return $reservation->fresh();
        });
    }

    /**
     * Find and validate reservation request exists
     *
     * @param int $reservationRequestId
     * @return ReservationRequest
     * @throws ModelNotFoundException
     */
    private function findReservationRequest(int $reservationRequestId): ReservationRequest
    {
        $reservationRequest = ReservationRequest::with(['user', 'academicTerm'])
            ->find($reservationRequestId);

        if (!$reservationRequest) {
            throw new ModelNotFoundException(__('Reservation request not found.'));
        }

        if ($reservationRequest->status !== 'pending') {
            throw new BusinessValidationException(__('Reservation request is not in pending status.'));
        }

        return $reservationRequest;
    }

    /**
     * Validate for duplicate reservations
     *
     * @param ReservationRequest $reservationRequest
     * @throws BusinessValidationException
     */
    private function validateForDuplicates(ReservationRequest $reservationRequest): void
    {
        $this->validator->checkForDuplicateReservation(
            $reservationRequest->user_id,
            $reservationRequest->period_type,
            $reservationRequest->academic_term_id,
            $reservationRequest->check_in_date,
            $reservationRequest->check_out_date
        );
    }

    /**
     * Create reservation from request
     *
     * @param ReservationRequest $reservationRequest
     * @param string|null $notes
     * @return Reservation
     */
    private function createReservation(ReservationRequest $reservationRequest, ?string $notes): Reservation
    {
        return $this->reservationCreator->create(
            userId: $reservationRequest->user_id,
            academicTermId: $reservationRequest->academic_term_id,
            checkInDate: $reservationRequest->check_in_date,
            checkOutDate: $reservationRequest->check_out_date,
            status: 'pending',
            active: false,
            notes: $notes,
            periodType: $reservationRequest->period_type,
        );
    }

    /**
     * Create accommodation for reservation
     *
     * @param Reservation $reservation
     * @param ReservationRequest $reservationRequest
     * @param string $accommodationType
     * @param int $roomId
     * @param int|null $apartmentId
     * @param int|null $bedCount
     * @param string|null $notes
     * @return mixed
     */
    private function createAccommodation(
        Reservation $reservation,
        ReservationRequest $reservationRequest,
        string $accommodationType,
        int $roomId,
        ?int $apartmentId,
        ?int $bedCount,
        ?string $notes
    ) {
        return $this->accommodationService->createAccommodation(
            $accommodationType,
            $accommodationType === 'room' ? $roomId : null,
            $accommodationType === 'apartment' ? $apartmentId : null,
            $reservationRequest->room_type ?? null,
            $reservationRequest->bed_count ?? null,
            $reservation->id,
            $notes
        );
    }
}
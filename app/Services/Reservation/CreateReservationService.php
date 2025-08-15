<?php

namespace App\Services\Reservation;

use App\Models\Reservation\Reservation;
use App\Services\Reservation\Create\ReservationValidator;
use App\Services\Reservation\Create\ReservationCreator;
use App\Services\Reservation\Shared\AccommodationService;
use App\Services\Reservation\Create\PaymentService;

class CreateReservationService
{
    protected ReservationValidator $validator;
    protected ReservationCreator $creator;
    protected AccommodationService $accommodationService;
    protected PaymentService $paymentService;
    protected EquipmentAssignmentService $equipmentService;

    public function __construct()
    {
        $this->validator = new ReservationValidator();
        $this->creator = new ReservationCreator();
        $this->accommodationService = new AccommodationService();
        $this->paymentService = new PaymentService();
    }

    /**
     * Create a new reservation (main entry point).
     *
     * @param array $data
     * @return Reservation
     */
    public function create(array $data): Reservation
    {
        $this->validator->checkForDuplicateReservation($data);
        $reservation = $this->creator->createReservationRecord($data);
        $this->accommodationService->handleAccommodationCreation($data, $reservation->id);
        $this->paymentService->createPaymentRecord($reservation, $data['payment'] ?? []);
        return $reservation;
    }
}
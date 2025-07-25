<?php

namespace App\Services\Reservation;

use App\Models\Reservation\Reservation;
use App\Services\Reservation\Complete\ReservationValidator;
use App\Services\Reservation\Complete\ReservationCreator;
use App\Services\Reservation\Complete\AccommodationService;
use App\Services\Reservation\Complete\PaymentService;
use App\Services\Reservation\Complete\EquipmentReturnService;

class CompleteReservationService
{
    protected ReservationValidator $validator;
    protected ReservationCreator $creator;
    protected AccommodationService $accommodationService;
    protected PaymentService $paymentService;
    protected EquipmentReturnService $equipmentService;

    public function __construct()
    {
        // $this->validator = new ReservationValidator();
        $this->equipmentService = new EquipmentReturnService();
        $this->creator = new ReservationCreator();
        $this->accommodationService = new AccommodationService();
        $this->paymentService = new PaymentService();
    }

    /**
     * Complete a new reservation (main entry point).
     *
     * @param array $data
     * @return Reservation
     */
    public function complete(array $data): Reservation
    {
        $this->validator->checkForDuplicateReservation($data);
        $reservation = $this->creator->completeReservationRecord($data);
        $this->accommodationService->handleAccommodationCreation($data, $reservation->id);
        $this->paymentService->completePaymentRecord($reservation, $data['payment'] ?? []);
        $this->equipmentService->assignEquipmentIfProvided($reservation, $data['equipment'] ?? []);
        return $reservation->load(['equipment', 'equipmentTracking']);
    }
}
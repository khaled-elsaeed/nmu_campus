<?php

namespace App\Services\Reservation\Operation;

use App\Models\Reservation\Reservation;
use App\Services\Reservation\CheckIn\ReservationValidator;
use App\Services\Reservation\CheckIn\ReservationCheckIn;
use App\Services\Reservation\CheckIn\EquipmentAssignmentService;

class CheckInReservationService
{
    protected ReservationValidator $validator;
    protected ReservationCheckIn $reservationCheckIn;
    protected EquipmentAssignmentService $equipmentService;

    public function __construct()
    {
        $this->validator = new ReservationValidator();
        $this->reservationCheckIn = new ReservationCheckIn();
        $this->equipmentService = new EquipmentAssignmentService();
    }

    /**
     * Check out a reservation (main entry point).
     *
     * @param array $data
     */
    public function checkIn(array $data): void
    {
        $this->validator->validateBeforeCheckIn($data);
        $this->equipmentService->assignEquipmentIfProvided($data);
        $this->reservationCheckIn->checkInReservation($data);
    }
}
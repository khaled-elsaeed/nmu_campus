<?php

namespace App\Services\Reservation;

use App\Models\Reservation\Reservation;
use App\Services\Reservation\Cancel\ReservationValidator;
use App\Services\Reservation\Cancel\ReservationCancel;
use App\Services\Reservation\Cancel\PaymentService;

class CancelReservationService
{
    protected ReservationValidator $validator;
    protected ReservationCancel $reservationCancel;
    protected PaymentService $paymentService;
    protected EquipmentReturnService $equipmentService;

    public function __construct()
    {
        $this->validator = new ReservationValidator();
        $this->reservationCancel = new ReservationCancel();
        $this->paymentService = new PaymentService();
    }

    /**
     * Cancel a reservation (main entry point).
     * @param int $reservationId
     */
    public function cancel(int $reservationId): void
    {
        $this->validator->validateBeforeCancel($reservationId);
        $this->reservationCancel->cancelReservation($reservationId);
        $this->paymentService->cancelPayment($reservationId);
    }
}
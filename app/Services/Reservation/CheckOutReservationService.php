<?php

namespace App\Services\Reservation;

use App\Models\Reservation\Reservation;
use App\Services\Reservation\CheckOut\ReservationValidator;
use App\Services\Reservation\CheckOut\ReservationCheckOut;
use App\Services\Reservation\CheckOut\PaymentService;
use App\Services\Reservation\CheckOut\EquipmentReturnService;

class CheckOutReservationService
{
    protected ReservationValidator $validator;
    protected ReservationCheckOut $reservationCheckOut;
    protected PaymentService $paymentService;
    protected EquipmentReturnService $equipmentService;

    public function __construct()
    {
        $this->validator = new ReservationValidator();
        $this->reservationCheckOut = new ReservationCheckOut();
        $this->equipmentService = new EquipmentReturnService();
        $this->paymentService = new PaymentService();
    }

    /**
     * Check out a reservation (main entry point).
     *
     * @param array $data
     * @return Reservation
     */
    public function checkOut(array $data): Reservation
    {
        $this->validator->validateBeforeCheckOut($data);
        $damages = $this->equipmentService->returnEquipment($data);

        $reservation = $this->reservationCheckOut->checkOutReservation($data);

        if (!empty($damages)) {
            $data['damages'] = $damages;
            $this->paymentService->createDamagePayment([
                'reservation_id' => $reservation->id,
                'damages' => $damages,
            ]);
        }
        return $reservation->load(['equipmentTracking']);
    }
}
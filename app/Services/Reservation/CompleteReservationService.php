<?php

namespace App\Services\Reservation;

use App\Models\Reservation\Reservation;
use App\Services\Reservation\Complete\ReservationValidator;
use App\Services\Reservation\Complete\ReservationComplete;
use App\Services\Reservation\Complete\PaymentService;
use App\Services\Reservation\Complete\EquipmentReturnService;

class CompleteReservationService
{
    protected ReservationValidator $validator;
    protected ReservationComplete $reservationComplete;
    protected PaymentService $paymentService;
    protected EquipmentReturnService $equipmentService;

    public function __construct()
    {
        $this->validator = new ReservationValidator();
        $this->reservationComplete = new ReservationComplete();
        $this->equipmentService = new EquipmentReturnService();
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
        $this->validator->validateBeforeComplete($data);
        $damages = $this->equipmentService->returnEquipment($data);

        $reservation = $this->reservationComplete->completeReservation($data);

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
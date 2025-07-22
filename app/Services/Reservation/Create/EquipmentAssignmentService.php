<?php

namespace App\Services\Reservation\Create;

use App\Models\Reservation\Reservation;
use App\Models\Equipment;
use App\Models\Reservation\ReservationEquipment;
use App\Exceptions\BusinessValidationException;

class EquipmentAssignmentService
{
    public function assignEquipmentIfProvided(Reservation $reservation, array $equipmentData): void
    {
        if (empty($equipmentData)) {
            return;
        }
        $this->assignEquipmentToReservation($reservation, $equipmentData);
    }

    private function assignEquipmentToReservation(Reservation $reservation, array $equipmentData): void
    {
        foreach ($equipmentData as $equipmentItem) {
            $equipmentId = $this->extractEquipmentId($equipmentItem);
            $quantity = $this->extractEquipmentQuantity($equipmentItem);

            $this->validateEquipment($equipmentId, $quantity);
            $this->createReservationEquipment($reservation->id, $equipmentId, $quantity);
        }
    }

    private function extractEquipmentId($equipmentItem): int
    {
        if (is_array($equipmentItem)) {
            return $equipmentItem['equipment_id'] ?? $equipmentItem['id'];
        }
        return $equipmentItem;
    }

    private function extractEquipmentQuantity($equipmentItem): int
    {
        if (is_array($equipmentItem)) {
            return $equipmentItem['quantity'] ?? 1;
        }
        return 1;
    }

    private function validateEquipment(int $equipmentId, int $quantity): void
    {
        $equipment = Equipment::find($equipmentId);
        if (!$equipment) {
            throw new BusinessValidationException("Equipment with ID {$equipmentId} not found.");
        }
        if ($quantity <= 0) {
            throw new BusinessValidationException("Quantity must be greater than 0 for equipment {$equipment->name_en}.");
        }
    }

    private function createReservationEquipment(int $reservationId, int $equipmentId, int $quantity): void
    {
        ReservationEquipment::create([
            'reservation_id' => $reservationId,
            'equipment_id' => $equipmentId,
            'quantity' => $quantity,
            'overall_status' => 'pending',
        ]);
    }
}

<?php

namespace App\Services\Reservation\Create;

use App\Models\Reservation\Reservation;
use App\Models\Equipment;
use App\Models\EquipmentCheckout;
use App\Models\EquipmentCheckoutDetail;
use App\Exceptions\BusinessValidationException;
use Illuminate\Support\Facades\DB;

class EquipmentAssignmentService
{
    /**
     * Assign equipment to a reservation if equipment data is provided.
     *
     * This will create an EquipmentCheckout record (if not exists) and
     * corresponding EquipmentCheckoutDetail records for each equipment item.
     *
     * @param Reservation $reservation
     * @param array $equipmentData
     * @throws BusinessValidationException
     */
    public function assignEquipmentIfProvided(Reservation $reservation, array $equipmentData): void
    {
        if (empty($equipmentData)) {
            return;
        }

        DB::transaction(function () use ($reservation, $equipmentData) {
            $this->assignEquipmentToReservation($reservation, $equipmentData);
        });
    }

    /**
     * Assign equipment items to the reservation's equipment checkout.
     *
     * @param Reservation $reservation
     * @param array $equipmentData
     * @throws BusinessValidationException
     */
    private function assignEquipmentToReservation(Reservation $reservation, array $equipmentData): void
    {
        // Create or get the EquipmentCheckout record for this reservation
        $checkout = EquipmentCheckout::firstOrCreate(
            ['reservation_id' => $reservation->id],
            [
                'overall_status' => 'pending',
                'reviewed_by' => null,
                'given_at' => null,
                'returned_at' => null,
            ]
        );

        foreach ($equipmentData as $equipmentItem) {
            $equipmentId = $this->extractEquipmentId($equipmentItem);
            $quantity = $this->extractEquipmentQuantity($equipmentItem);

            $this->validateEquipment($equipmentId, $quantity);

            // Create or update the EquipmentCheckoutDetail for this equipment
            EquipmentCheckoutDetail::updateOrCreate(
                [
                    'equipment_checkout_id' => $checkout->id,
                    'equipment_id' => $equipmentId,
                ],
                [
                    'quantity_given' => $quantity,
                    'given_status' => 'good',
                    'given_notes' => null,
                    'quantity_returned' => null,
                    'returned_status' => null,
                    'returned_notes' => null,
                ]
            );
        }
    }

    /**
     * Extract the equipment ID from the equipment item.
     *
     * @param mixed $equipmentItem
     * @return int
     */
    private function extractEquipmentId($equipmentItem): int
    {
        if (is_array($equipmentItem)) {
            return $equipmentItem['equipment_id'] ?? $equipmentItem['id'];
        }
        return (int) $equipmentItem;
    }

    /**
     * Extract the quantity from the equipment item.
     *
     * @param mixed $equipmentItem
     * @return int
     */
    private function extractEquipmentQuantity($equipmentItem): int
    {
        if (is_array($equipmentItem)) {
            return $equipmentItem['quantity'] ?? 1;
        }
        return 1;
    }

    /**
     * Validate the equipment exists and the quantity is valid.
     *
     * @param int $equipmentId
     * @param int $quantity
     * @throws BusinessValidationException
     */
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
}

<?php

namespace App\Services\Reservation\Pipeline\Services\CheckIn;

use App\Models\Reservation\Reservation;
use App\Models\Equipment\EquipmentAssignment;
use App\Models\Equipment\Equipment;
use Illuminate\Support\Facades\DB;

class EquipmentAssignmentService
{
    /**
     * Assign equipment to a reservation during check-in.
     *
     * @param array $data
     * @return void
     */
    public function assignEquipmentIfProvided(array $data): void
    {
        if (empty($data['equipment']) || !is_array($data['equipment'])) {
            return;
        }

        $reservation = Reservation::findOrFail($data['reservation_id']);

        foreach ($data['equipment'] as $equipmentItem) {
            $equipment = Equipment::findOrFail($equipmentItem['equipment_id']);
            
            // Check equipment availability
            if (!$this->isEquipmentAvailable($equipment->id, $equipmentItem['quantity'])) {
                continue; // Skip if not available
            }

            EquipmentAssignment::create([
                'reservation_id' => $reservation->id,
                'equipment_id' => $equipment->id,
                'quantity' => $equipmentItem['quantity'],
                'assigned_at' => now(),
                'status' => 'assigned',
                'notes' => $equipmentItem['notes'] ?? null,
            ]);

            // Update equipment availability
            $this->updateEquipmentAvailability($equipment->id, $equipmentItem['quantity']);
        }
    }

    /**
     * Check if equipment is available in the specified quantity.
     *
     * @param int $equipmentId
     * @param int $quantity
     * @return bool
     */
    private function isEquipmentAvailable(int $equipmentId, int $quantity): bool
    {
        $equipment = Equipment::findOrFail($equipmentId);
        $assignedQuantity = EquipmentAssignment::where('equipment_id', $equipmentId)
            ->where('status', 'assigned')
            ->sum('quantity');

        return ($equipment->total_quantity - $assignedQuantity) >= $quantity;
    }

    /**
     * Update equipment availability.
     *
     * @param int $equipmentId
     * @param int $quantity
     * @return void
     */
    private function updateEquipmentAvailability(int $equipmentId, int $quantity): void
    {
        // This could be used for tracking equipment usage
        // For now, we just log the assignment
    }
}

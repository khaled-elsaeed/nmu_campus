<?php

namespace App\Services\Reservation\Pipeline\Services\CheckOut;

use App\Models\Reservation\Reservation;
use App\Models\Equipment\EquipmentAssignment;
use App\Models\Equipment\EquipmentDamage;
use Illuminate\Support\Facades\DB;

class EquipmentReturnService
{
    /**
     * Return equipment and assess damages.
     *
     * @param array $data
     * @return array
     */
    public function returnEquipment(array $data): array
    {
        $reservation = Reservation::findOrFail($data['reservation_id']);
        $damages = [];

        if (empty($data['equipment']) || !is_array($data['equipment'])) {
            return $damages;
        }

        foreach ($data['equipment'] as $equipmentItem) {
            $assignment = EquipmentAssignment::where('reservation_id', $reservation->id)
                ->where('equipment_id', $equipmentItem['equipment_id'])
                ->first();

            if (!$assignment) {
                continue;
            }

            // Update assignment status
            $assignment->update([
                'status' => 'returned',
                'returned_at' => now(),
                'return_notes' => $equipmentItem['notes'] ?? null,
            ]);

            // Check for damages
            if (isset($equipmentItem['returned_status']) && $equipmentItem['returned_status'] === 'damaged') {
                $damage = EquipmentDamage::create([
                    'equipment_id' => $equipmentItem['equipment_id'],
                    'reservation_id' => $reservation->id,
                    'damage_type' => 'return_damage',
                    'description' => $equipmentItem['notes'] ?? 'Equipment returned damaged',
                    'estimated_cost' => $equipmentItem['estimated_cost'] ?? 0,
                    'reported_at' => now(),
                ]);

                $damages[] = $damage;
            }
        }

        return $damages;
    }
}

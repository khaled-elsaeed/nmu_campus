<?php

namespace App\Services\Reservation\CheckOut;

use App\Models\Payment;
use App\Models\Reservation\Reservation;
use App\Models\Equipment;
use App\Models\EquipmentCheckout;
use App\Exceptions\BusinessValidationException;

class PaymentService
{
    /**
     * Create a payment record for equipment damages.
     *
     * @param array $data Array containing at least 'reservation_id' and 'damages' keys.
     *   - reservation_id
     *   - damages: array of damages, each with:
     *     - equipment_id
     *     - quantity_damaged
     *     - estimated_cost
     *     - status
     *     - notes
     *     - equipment_checkout_id
     * @return Payment|null
     * @throws BusinessValidationException
     */
    public function createDamagePayment(array $data): ?Payment
    {
        $reservationId = $data['reservation_id'] ?? null;

        $damages = $data['damages'] ?? [];

        $reservation = Reservation::find($reservationId);

        if (empty($damages)) {
            return null;
        }

        $damageDescriptions = [];
        $totalAmount = 0;

        foreach ($damages as $damage) {
            $equipmentId = $damage['equipment_id'] ?? null;
            $quantity = $damage['quantity_damaged'] ?? 0;
            $estimatedCost = $damage['estimated_cost'] ?? 0;
            $returnedStatus = $damage['status'] ?? null;
            $returnedNotes = $damage['notes'] ?? null;
            $equipmentCheckoutId = $damage['equipment_checkout_id'] ?? null;

            if (!$equipmentId || $quantity <= 0 || $estimatedCost < 0) {
                throw new BusinessValidationException('Invalid damage data provided.');
            }

            $equipment = Equipment::find($equipmentId);
            $equipmentName = $equipment ? $equipment->name : 'Unknown Equipment';

            $equipmentCheckout = $equipmentCheckoutId ? EquipmentCheckout::find($equipmentCheckoutId) : null;

            $desc = $quantity . ' x ' . $equipmentName . ' damaged';
            if ($returnedNotes) {
                $desc .= ' (' . $returnedNotes . ')';
            }
            $desc .= ' - ' . number_format($estimatedCost, 2) . ' EGP';
            $damageDescriptions[] = $desc;

            $totalAmount += $estimatedCost;
        }

        $details = [
            [
                'type' => 'damage_fee',
                'amount' => $totalAmount,
                'description' => implode('; ', $damageDescriptions),
            ],
            [
                'type' => 'total_amount',
                'amount' => $totalAmount,
                'description' => 'Total Payment Amount',
            ],
        ];

        return Payment::create([
            'reservation_id' => $reservation->id,
            'amount' => $totalAmount,
            'status' => 'pending',
            'notes' => 'Payment for equipment damages',
            'details' => $details,
        ]);
    }

    /**
     * Get the checkout detail ID for a given equipment checkout and equipment.
     *
     * @param EquipmentCheckout|null $equipmentCheckout
     * @param int $equipmentId
     * @return int|null
     */
    private function getCheckoutDetailId($equipmentCheckout, $equipmentId)
    {
        if (!$equipmentCheckout) {
            return null;
        }
        $detail = $equipmentCheckout->details()
            ->where('equipment_id', $equipmentId)
            ->first();
        return $detail ? $detail->id : null;
    }
}
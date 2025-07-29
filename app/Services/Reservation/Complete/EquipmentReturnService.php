<?php

namespace App\Services\Reservation\Complete;

use App\Models\Reservation\Reservation;
use App\Models\Equipment;
use App\Models\EquipmentCheckout;
use App\Models\EquipmentCheckoutDetail;
use App\Exceptions\BusinessValidationException;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EquipmentReturnService
{
    /**
     * Mark equipment as returned for a reservation, if equipment data is provided.
     *
     * @param array $data The full request data, including reservation_id, equipment, and checkout_notes
     * @return array Returns array of damages found during return process
     */
    public function returnEquipment(array $data): array
    {
        $reservationId = $data['reservation_id'] ?? null;
        $equipmentData = $data['equipment'] ?? [];
        $checkoutNotes = $data['checkout_notes'] ?? null;
        $reservation = Reservation::find($reservationId);
        return $this->markEquipmentAsReturned($reservation, $equipmentData, $checkoutNotes);
    }

    /**
     * Mark the specified equipment as returned for the reservation.
     * Each equipment item can have a returned_status: 'good', 'damaged', or 'missing'.
     *
     * @param Reservation $reservation
     * @param array $equipmentData
     * @param string|null $checkoutNotes
     * @return array Returns array of damages
     */
    private function markEquipmentAsReturned(Reservation $reservation, array $equipmentData, ?string $checkoutNotes = null): array
    {
        $damages = [];
        $equipmentCheckout = EquipmentCheckout::where('reservation_id', $reservation->id)->first();

        if (!$equipmentCheckout) {
            throw new BusinessValidationException("Equipment checkout record not found for reservation ID {$reservation->id}.");
        }

        foreach ($equipmentData as $equipmentItem) {
            $equipmentId = $this->extractEquipmentId($equipmentItem);
            $quantity = $this->extractEquipmentQuantity($equipmentItem);
            $returnedStatus = $this->extractReturnedStatus($equipmentItem);
            $returnedNotes = $this->extractReturnedNotes($equipmentItem);
            $estimatedCost = $this->extractEstimatedCost($equipmentItem);

            $this->validateEquipment($equipmentId, $quantity);

            $this->updateEquipmentCheckoutDetailReturn(
                $equipmentCheckout,
                $equipmentId,
                $quantity,
                $returnedStatus,
                $returnedNotes
            );

            // Track damages ONLY if equipment is damaged or missing (not good)
            if ($returnedStatus === 'damaged' || $returnedStatus === 'missing') {
                $equipment = Equipment::find($equipmentId);
                $damages[] = [
                    'equipment_id' => $equipmentId,
                    'equipment_name' => $equipment ? $equipment->name : 'Unknown Equipment',
                    'quantity_damaged' => $quantity,
                    'status' => $returnedStatus,
                    'estimated_cost' => $estimatedCost,
                    'notes' => $returnedNotes,
                    'checkout_detail_id' => $this->getCheckoutDetailId($equipmentCheckout, $equipmentId)
                ];
            }
        }

        $equipmentCheckout->overall_status = 'returned';
        $equipmentCheckout->returned_at = Carbon::now();
        $equipmentCheckout->reviewed_by = Auth::id();
        if ($checkoutNotes !== null) {
            $equipmentCheckout->checkout_notes = $checkoutNotes;
        }
        $equipmentCheckout->save();

        return $damages;
    }

    /**
     * Extract equipment ID from the equipment item.
     *
     * @param mixed $equipmentItem
     * @return int
     */
    private function extractEquipmentId($equipmentItem): int
    {
        if (is_array($equipmentItem)) {
            if (isset($equipmentItem['equipment_id'])) {
                return (int)$equipmentItem['equipment_id'];
            }
            if (isset($equipmentItem['id'])) {
                return (int)$equipmentItem['id'];
            }
        }
        return (int)$equipmentItem;
    }

    /**
     * Extract equipment quantity from the equipment item.
     *
     * @param mixed $equipmentItem
     * @return int
     */
    private function extractEquipmentQuantity($equipmentItem): int
    {
        if (is_array($equipmentItem)) {
            if (isset($equipmentItem['quantity']) && is_numeric($equipmentItem['quantity'])) {
                return (int)$equipmentItem['quantity'];
            }
        }
        return 1;
    }

    /**
     * Extract returned status from the equipment item.
     * Allowed values: 'good', 'damaged', 'missing'. Default is 'good'.
     *
     * @param mixed $equipmentItem
     * @return string
     */
    private function extractReturnedStatus($equipmentItem): string
    {
        if (is_array($equipmentItem)) {
            if (isset($equipmentItem['returned_status']) && in_array($equipmentItem['returned_status'], ['good', 'damaged', 'missing'])) {
                return $equipmentItem['returned_status'];
            }
        }
        return 'good';
    }

    /**
     * Extract returned notes from the equipment item.
     * Accepts both 'returned_notes' and 'notes' for compatibility.
     *
     * @param mixed $equipmentItem
     * @return string|null
     */
    private function extractReturnedNotes($equipmentItem): ?string
    {
        if (is_array($equipmentItem)) {
            if (isset($equipmentItem['returned_notes'])) {
                return $equipmentItem['returned_notes'];
            }
            if (isset($equipmentItem['notes'])) {
                return $equipmentItem['notes'];
            }
        }
        return null;
    }

    /**
     * Extract estimated cost for damaged/missing equipment.
     *
     * @param mixed $equipmentItem
     * @return float|null
     */
    private function extractEstimatedCost($equipmentItem): ?float
    {
        if (is_array($equipmentItem) && isset($equipmentItem['estimated_cost']) && is_numeric($equipmentItem['estimated_cost'])) {
            return (float) $equipmentItem['estimated_cost'];
        }
        return null;
    }

    /**
     * Get checkout detail ID for tracking purposes.
     *
     * @param EquipmentCheckout $equipmentCheckout
     * @param int $equipmentId
     * @return int|null
     */
    private function getCheckoutDetailId(EquipmentCheckout $equipmentCheckout, int $equipmentId): ?int
    {
        $checkoutDetail = EquipmentCheckoutDetail::where('equipment_checkout_id', $equipmentCheckout->id)
            ->where('equipment_id', $equipmentId)
            ->first();

        return $checkoutDetail ? $checkoutDetail->id : null;
    }

    /**
     * Validate that the equipment exists and the quantity is valid.
     *
     * @param int $equipmentId
     * @param int $quantity
     * @return void
     * @throws BusinessValidationException
     */
    private function validateEquipment(int $equipmentId, int $quantity): void
    {
        $equipment = Equipment::find($equipmentId);
        if (!$equipment) {
            throw new BusinessValidationException("Equipment with ID {$equipmentId} not found.");
        }

        if ($quantity <= 0) {
            throw new BusinessValidationException("Invalid quantity {$quantity} for equipment ID {$equipmentId}.");
        }
    }

    /**
     * Update the equipment checkout detail record to mark as returned with the specified status and notes.
     *
     * @param EquipmentCheckout $equipmentCheckout
     * @param int $equipmentId
     * @param int $quantity
     * @param string $returnedStatus
     * @param string|null $returnedNotes
     * @return void
     */
    private function updateEquipmentCheckoutDetailReturn(
        EquipmentCheckout $equipmentCheckout,
        int $equipmentId,
        int $quantity,
        string $returnedStatus,
        ?string $returnedNotes
    ): void {
        $checkoutDetail = EquipmentCheckoutDetail::where('equipment_checkout_id', $equipmentCheckout->id)
            ->where('equipment_id', $equipmentId)
            ->first();

        if (!$checkoutDetail) {
            throw new BusinessValidationException("Equipment checkout detail not found for checkout ID {$equipmentCheckout->id} and equipment ID {$equipmentId}.");
        }

        $checkoutDetail->quantity_returned = $quantity;
        $checkoutDetail->returned_status = $returnedStatus;
        $checkoutDetail->returned_notes = $returnedNotes;
        $checkoutDetail->save();
    }
}
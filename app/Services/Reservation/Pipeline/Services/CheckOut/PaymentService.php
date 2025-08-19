<?php

namespace App\Services\Reservation\Pipeline\Services\CheckOut;

use App\Models\Payment\Payment;
use App\Models\Equipment\EquipmentDamage;

class PaymentService
{
    /**
     * Create damage payment for equipment damages.
     *
     * @param array $data
     * @return Payment|null
     */
    public function createDamagePayment(array $data): ?Payment
    {
        if (empty($data['damages'])) {
            return null;
        }

        $totalDamageCost = collect($data['damages'])->sum('estimated_cost');

        if ($totalDamageCost <= 0) {
            return null;
        }

        return Payment::create([
            'reservation_id' => $data['reservation_id'],
            'amount' => $totalDamageCost,
            'status' => 'pending',
            'type' => 'damage_compensation',
            'notes' => 'Payment for equipment damages during check-out',
            'due_date' => now()->addDays(30),
        ]);
    }
}

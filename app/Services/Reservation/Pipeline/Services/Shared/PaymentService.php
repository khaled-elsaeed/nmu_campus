<?php

namespace App\Services\Reservation\Pipeline\Services\Shared;

use App\Models\Reservation\Reservation;
use App\Models\Payment\Payment;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Create initial payment record for a reservation.
     *
     * @param Reservation $reservation
     * @param string|null $notes
     * @return Payment
     */
    public function createPaymentRecord(Reservation $reservation, ?string $notes = null): Payment
    {
        return Payment::create([
            'reservation_id' => $reservation->id,
            'amount' => $this->calculateReservationAmount($reservation),
            'status' => 'pending',
            'type' => 'reservation_fee',
            'notes' => $notes,
            'due_date' => now()->addDays(7),
        ]);
    }

    /**
     * Calculate reservation amount based on type and duration.
     *
     * @param Reservation $reservation
     * @return float
     */
    private function calculateReservationAmount(Reservation $reservation): float
    {
        $baseAmount = 100.00; // Base reservation fee
        
        if ($reservation->period_type === 'academic') {
            // Academic term pricing
            $baseAmount = 500.00;
        } elseif ($reservation->period_type === 'calendar') {
            // Calendar period pricing - calculate by days
            $checkIn = \Carbon\Carbon::parse($reservation->check_in_date);
            $checkOut = \Carbon\Carbon::parse($reservation->check_out_date);
            $days = $checkIn->diffInDays($checkOut);
            $baseAmount = $days * 25.00; // $25 per day
        }

        return $baseAmount;
    }

    /**
     * Get payment details for a reservation.
     *
     * @param int $reservationId
     * @return Payment|null
     */
    public function getPaymentByReservation(int $reservationId): ?Payment
    {
        return Payment::where('reservation_id', $reservationId)
            ->where('type', 'reservation_fee')
            ->first();
    }

    /**
     * Update payment status.
     *
     * @param int $paymentId
     * @param string $status
     * @return Payment
     */
    public function updatePaymentStatus(int $paymentId, string $status): Payment
    {
        $payment = Payment::findOrFail($paymentId);
        $payment->update(['status' => $status]);
        return $payment->fresh();
    }
}

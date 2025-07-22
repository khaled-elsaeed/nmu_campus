<?php

namespace App\Services\Reservation\Create;

use App\Models\Reservation;
use App\Models\Reservation\Accommodation;
use App\Models\Payment;
use Carbon\Carbon;

class PaymentService
{
    const INSURANCE_FEE = 5000;
    const ACADEMIC_TERM_FEE = 10000;
    const MONTHLY_FEE = 2500;
    const WEEKLY_FEE = 1500;
    const DAILY_FEE = 250;

    public function createPaymentRecord(Reservation $reservation, array $paymentData): Payment
    {
        $accommodation = Accommodation::where('reservation_id', $reservation->id)->first();
        $doubleRoomBedOption = $accommodation && $accommodation->double_room_bed_option;
        $amount = $paymentData['amount'] ?? $this->calculatePaymentAmount($reservation, $doubleRoomBedOption);
        $details = $this->buildPaymentDetails($reservation, $amount, $doubleRoomBedOption);

        return Payment::create([
            'reservation_id' => $reservation->id,
            'amount' => $amount,
            'status' => $paymentData['status'] ?? 'pending',
            'notes' => $paymentData['notes'] ?? null,
            'details' => $details,
        ]);
    }

    public function calculatePaymentAmount(Reservation $reservation, $doubleRoomBedOption = false): float
    {
        $totalAmount = self::INSURANCE_FEE;

        if ($this->isLongTermReservation($reservation)) {
            $totalAmount += $this->calculateLongTermFee($doubleRoomBedOption);
        } else {
            $totalAmount += $this->calculateShortTermFee($reservation);
        }

        return $totalAmount;
    }

    private function isLongTermReservation(Reservation $reservation): bool
    {
        return !is_null($reservation->academic_term_id);
    }

    private function calculateLongTermFee($doubleRoomBedOption = false): float
    {
        if ($doubleRoomBedOption) {
            return 16000;
        }
        return self::ACADEMIC_TERM_FEE;
    }

    private function calculateShortTermFee(Reservation $reservation): float
    {
        if (!$reservation->check_in_date || !$reservation->check_out_date) {
            return 0;
        }

        $checkIn = Carbon::parse($reservation->check_in_date);
        $checkOut = Carbon::parse($reservation->check_out_date);
        $totalDays = $checkIn->diffInDays($checkOut);

        return $this->calculateOptimalFee($totalDays);
    }

    private function calculateOptimalFee(int $totalDays): float
    {
        if ($totalDays <= 0) {
            return 0;
        }

        $totalFee = 0;
        $remainingDays = $totalDays;

        $months = intval($remainingDays / 30);
        if ($months > 0) {
            $totalFee += $months * self::MONTHLY_FEE;
            $remainingDays -= $months * 30;
        }

        $weeks = intval($remainingDays / 7);
        if ($weeks > 0) {
            $totalFee += $weeks * self::WEEKLY_FEE;
            $remainingDays -= $weeks * 7;
        }

        if ($remainingDays > 0) {
            $totalFee += $remainingDays * self::DAILY_FEE;
        }

        return $totalFee;
    }

    public function buildPaymentDetails(Reservation $reservation, float $totalAmount, $doubleRoomBedOption = false): array
    {
        $details = [];
        $details[] = [
            'type' => 'insurance_fee',
            'amount' => self::INSURANCE_FEE,
            'description' => 'Insurance Fee',
        ];
        if ($this->isLongTermReservation($reservation)) {
            $details = array_merge($details, $this->getLongTermBreakdown($doubleRoomBedOption));
        } else {
            $details = array_merge($details, $this->getShortTermBreakdown($reservation));
        }
        $details[] = [
            'type' => 'total_amount',
            'amount' => $totalAmount,
            'description' => 'Total Payment Amount',
        ];
        return $details;
    }

    private function getLongTermBreakdown($doubleRoomBedOption = false): array
    {
        return [[
            'type' => 'academic_term',
            'amount' => $doubleRoomBedOption ? 16000 : self::ACADEMIC_TERM_FEE,
            'description' => 'Academic Term Fee' . ($doubleRoomBedOption ? ' (Double Room Bed Option)' : ''),
        ]];
    }

    private function getShortTermBreakdown(Reservation $reservation): array
    {
        if (!$reservation->check_in_date || !$reservation->check_out_date) {
            return [];
        }
        $checkIn = Carbon::parse($reservation->check_in_date);
        $checkOut = Carbon::parse($reservation->check_out_date);
        $totalDays = $checkIn->diffInDays($checkOut);
        $breakdown = $this->getOptimalFeeBreakdown($totalDays);
        $details = [];
        foreach ($breakdown as $item) {
            $details[] = [
                'type' => $item['type'],
                'amount' => $item['amount'],
                'description' => $item['description'],
            ];
        }
        return $details;
    }

    private function getOptimalFeeBreakdown(int $totalDays): array
    {
        $breakdown = [];
        $remainingDays = $totalDays;

        $months = intval($remainingDays / 30);
        if ($months > 0) {
            $breakdown[] = [
                'type' => 'monthly',
                'quantity' => $months,
                'unit_price' => self::MONTHLY_FEE,
                'amount' => $months * self::MONTHLY_FEE,
                'description' => $months . ' month' . ($months > 1 ? 's' : '')
            ];
            $remainingDays -= $months * 30;
        }

        $weeks = intval($remainingDays / 7);
        if ($weeks > 0) {
            $breakdown[] = [
                'type' => 'weekly',
                'quantity' => $weeks,
                'unit_price' => self::WEEKLY_FEE,
                'amount' => $weeks * self::WEEKLY_FEE,
                'description' => $weeks . ' week' . ($weeks > 1 ? 's' : '')
            ];
            $remainingDays -= $weeks * 7;
        }

        if ($remainingDays > 0) {
            $breakdown[] = [
                'type' => 'daily',
                'quantity' => $remainingDays,
                'unit_price' => self::DAILY_FEE,
                'amount' => $remainingDays * self::DAILY_FEE,
                'description' => $remainingDays . ' day' . ($remainingDays > 1 ? 's' : '')
            ];
        }

        return $breakdown;
    }
}

<?php

namespace App\Services\Reservation\Create;

use App\Models\Reservation;
use App\Models\Reservation\Accommodation;
use App\Models\Payment;
use App\Models\Insurance;
use Carbon\Carbon;
use App\Exceptions\BusinessValidationException;

class PaymentService
{
    // Fee constants
    const INSURANCE_FEE = 5000;
    const SINGLE_ROOM_ACADEMIC_FEE = 10000;
    const DOUBLE_ROOM_ONE_BED_ACADEMIC_FEE = 8000;
    const DOUBLE_ROOM_TWO_BED_ACADEMIC_FEE = 16000;
    const MONTHLY_FEE = 2500;
    const WEEKLY_FEE = 1500;
    const DAILY_FEE = 250;

    // Insurance statuses
    const INSURANCE_ACTIVE = 'active';
    const INSURANCE_CARRIED_OVER = 'carried_over';
    const INSURANCE_EXPIRED = 'expired';

    /**
     * Create payment record with comprehensive validation and insurance handling
     *
     * @param Reservation $reservation
     * @param array $paymentData
     * @return Payment
     * @throws BusinessValidationException
     */
    public function createPaymentRecord(Reservation $reservation, array $paymentData): Payment
    {
        $this->validatePaymentData($paymentData);

        $accommodation = $this->getAccommodation($reservation);
        $roomConfig = $this->getRoomConfiguration($accommodation);
        
        $insuranceInfo = $this->handleInsurance($reservation);
        $amount = $paymentData['amount'] ?? $this->calculatePaymentAmount($reservation, $roomConfig, $insuranceInfo['skip_insurance']);
        $details = $this->buildPaymentDetails($reservation, $amount, $roomConfig, $insuranceInfo);

        return Payment::create([
            'reservation_id' => $reservation->id,
            'amount' => $amount,
            'status' => $paymentData['status'] ?? 'pending',
            'notes' => $paymentData['notes'] ?? null,
            'details' => $details,
        ]);
    }

    /**
     * Get room configuration details
     */
    private function getRoomConfiguration(Accommodation $accommodation = null): array
    {
        if (!$accommodation) {
            return [
                'type' => 'single',
                'bed_count' => 1,
                'fee' => self::SINGLE_ROOM_ACADEMIC_FEE
            ];
        }

        // Determine room type and bed configuration
        $roomType = $accommodation->type ?? 'room';
        $isDoubleRoom = $roomType === 'double_room' || ($roomType === 'room' && $accommodation->double_room_bed_option);
        
        if ($isDoubleRoom) {
            // For double rooms, check bed count
            $bedCount = $accommodation->bed_count ?? 1;
            
            if ($bedCount >= 2) {
                return [
                    'type' => 'double',
                    'bed_count' => 2,
                    'fee' => self::DOUBLE_ROOM_TWO_BED_ACADEMIC_FEE
                ];
            } else {
                return [
                    'type' => 'double',
                    'bed_count' => 1,
                    'fee' => self::DOUBLE_ROOM_ONE_BED_ACADEMIC_FEE
                ];
            }
        }

        // Default to single room
        return [
            'type' => 'single',
            'bed_count' => 1,
            'fee' => self::SINGLE_ROOM_ACADEMIC_FEE
        ];
    }

    /**
     * Handle insurance logic - carry over existing or create new
     *
     * @param Reservation $reservation
     * @return array Insurance information
     */
    private function handleInsurance(Reservation $reservation): array
    {
        $existingInsurance = $this->getActiveInsurance($reservation->user_id);
        
        if ($existingInsurance) {
            // Mark existing insurance as carried over
            $existingInsurance->update(['status' => self::INSURANCE_CARRIED_OVER]);
            
            // Create new insurance record
            $this->createInsuranceRecord($reservation, self::INSURANCE_ACTIVE);
            
            return [
                'skip_insurance' => true,
                'carried_over' => true,
                'previous_insurance_id' => $existingInsurance->id
            ];
        }

        // Create new insurance record
        $this->createInsuranceRecord($reservation, self::INSURANCE_ACTIVE);
        
        return [
            'skip_insurance' => false,
            'carried_over' => false,
            'previous_insurance_id' => null
        ];
    }

    /**
     * Get active insurance for user
     */
    private function getActiveInsurance(int $userId): ?Insurance
    {
        return Insurance::where('user_id', $userId)
            ->where('status', self::INSURANCE_ACTIVE)
            ->latest('created_at')
            ->first();
    }

    /**
     * Create insurance record
     */
    private function createInsuranceRecord(Reservation $reservation, string $status): Insurance
    {
        return Insurance::create([
            'user_id' => $reservation->user_id,
            'reservation_id' => $reservation->id,
            'status' => $status,
            'amount' => self::INSURANCE_FEE,
            'valid_from' => $reservation->check_in_date ?? now(),
            'valid_until' => $reservation->check_out_date ?? now()->addYear(),
        ]);
    }

    /**
     * Calculate payment amount with insurance logic
     *
     * @param Reservation $reservation
     * @param array $roomConfig
     * @param bool $skipInsurance
     * @return float
     */
    public function calculatePaymentAmount(Reservation $reservation, array $roomConfig = [], bool $skipInsurance = false): float
    {
        $totalAmount = 0;

        if (!$skipInsurance) {
            $totalAmount += self::INSURANCE_FEE;
        }

        // Add accommodation fees
        if ($this->isLongTermReservation($reservation)) {
            $totalAmount += $this->calculateLongTermFee($reservation, $roomConfig);
        } else {
            $totalAmount += $this->calculateShortTermFee($reservation);
        }

        return $totalAmount;
    }

    /**
     * Validate payment data
     */
    private function validatePaymentData(array $paymentData): void
    {
        if (isset($paymentData['amount']) && $paymentData['amount'] < 0) {
            throw new BusinessValidationException('Payment amount cannot be negative.');
        }

        if (isset($paymentData['status']) && !in_array($paymentData['status'], ['pending', 'completed', 'failed', 'cancelled'])) {
            throw new BusinessValidationException('Invalid payment status.');
        }
    }

    /**
     * Validate reservation dates for short-term reservations
     */
    private function validateReservationDates(Reservation $reservation): void
    {
        if ($reservation->period_type === 'calendar') {
            if (!$reservation->check_in_date || !$reservation->check_out_date) {
                throw new BusinessValidationException('Calendar-based reservations must have check-in and check-out dates.');
            }

            $checkIn = Carbon::parse($reservation->check_in_date);
            $checkOut = Carbon::parse($reservation->check_out_date);

            if ($checkOut->lte($checkIn)) {
                throw new BusinessValidationException('Check-out date must be after check-in date.');
            }

            if ($checkIn->isPast()) {
                throw new BusinessValidationException('Check-in date cannot be in the past.');
            }
        } elseif ($reservation->period_type === 'academic') {
            if (!$reservation->academic_term_id) {
                throw new BusinessValidationException('Academic reservations must have a valid academic term ID.');
            }
        } else {
            throw new BusinessValidationException('Invalid period type. Must be either "academic" or "calendar".');
        }
    }

    /**
     * Get accommodation with error handling
     */
    private function getAccommodation(Reservation $reservation): ?Accommodation
    {
        return Accommodation::where('reservation_id', $reservation->id)->first();
    }

    /**
     * Check if reservation is long-term (academic term)
     */
    private function isLongTermReservation(Reservation $reservation): bool
    {
        return $reservation->period_type === 'academic';
    }

    /**
     * Calculate long-term (academic term) fee based on room configuration
     */
    private function calculateLongTermFee(Reservation $reservation, array $roomConfig = []): float
    {
        if (empty($roomConfig)) {
            $accommodation = $this->getAccommodation($reservation);
            $roomConfig = $this->getRoomConfiguration($accommodation);
        }

        return $roomConfig['fee'];
    }

    /**
     * Calculate short-term fee with optimal pricing
     */
    private function calculateShortTermFee(Reservation $reservation): float
    {
        if ($reservation->period_type !== 'calendar' || !$reservation->check_in_date || !$reservation->check_out_date) {
            return 0;
        }

        $checkIn = Carbon::parse($reservation->check_in_date);
        $checkOut = Carbon::parse($reservation->check_out_date);
        $totalDays = $checkIn->diffInDays($checkOut);

        if ($totalDays <= 0) {
            return 0;
        }

        return $this->calculateOptimalFee($totalDays);
    }

    /**
     * Calculate optimal fee breakdown for given days
     */
    private function calculateOptimalFee(int $totalDays): float
    {
        if ($totalDays <= 0) {
            return 0;
        }

        $totalFee = 0;
        $remainingDays = $totalDays;

        // Calculate months (30 days each)
        $months = intval($remainingDays / 30);
        if ($months > 0) {
            $totalFee += $months * self::MONTHLY_FEE;
            $remainingDays -= $months * 30;
        }

        // Calculate weeks (7 days each)
        $weeks = intval($remainingDays / 7);
        if ($weeks > 0) {
            $totalFee += $weeks * self::WEEKLY_FEE;
            $remainingDays -= $weeks * 7;
        }

        // Calculate remaining days
        if ($remainingDays > 0) {
            $totalFee += $remainingDays * self::DAILY_FEE;
        }

        return $totalFee;
    }

    /**
     * Build comprehensive payment details
     */
    public function buildPaymentDetails(Reservation $reservation, float $totalAmount, array $roomConfig = [], array $insuranceInfo = []): array
    {
        $details = [];

        // Add insurance details
        $details = array_merge($details, $this->getInsuranceBreakdown($insuranceInfo));

        // Add accommodation details
        if ($this->isLongTermReservation($reservation)) {
            $details = array_merge($details, $this->getLongTermBreakdown($roomConfig));
        } else {
            $details = array_merge($details, $this->getShortTermBreakdown($reservation));
        }

        // Add total amount
        $details[] = [
            'type' => 'total_amount',
            'amount' => $totalAmount,
            'description' => 'Total Payment Amount',
        ];

        return $details;
    }

    /**
     * Get insurance breakdown details
     */
    private function getInsuranceBreakdown(array $insuranceInfo): array
    {
        $details = [];

        if ($insuranceInfo['carried_over'] ?? false) {
            $details[] = [
                'type' => 'insurance_carried_over',
                'amount' => 0,
                'description' => 'Insurance Fee (Carried Over from Previous Reservation)',
                'previous_insurance_id' => $insuranceInfo['previous_insurance_id'] ?? null,
            ];
        } else {
            $details[] = [
                'type' => 'insurance_fee',
                'amount' => self::INSURANCE_FEE,
                'description' => 'Insurance Fee',
            ];
        }

        return $details;
    }

    /**
     * Get long-term reservation breakdown with new room configuration
     */
    private function getLongTermBreakdown(array $roomConfig = []): array
    {
        if (empty($roomConfig)) {
            $roomConfig = [
                'type' => 'single',
                'bed_count' => 1,
                'fee' => self::SINGLE_ROOM_ACADEMIC_FEE
            ];
        }

        $description = $this->getRoomDescription($roomConfig);

        return [[
            'type' => 'academic_term',
            'amount' => $roomConfig['fee'],
            'description' => $description,
            'room_type' => $roomConfig['type'],
            'bed_count' => $roomConfig['bed_count'],
        ]];
    }

    /**
     * Get room description for breakdown
     */
    private function getRoomDescription(array $roomConfig): string
    {
        $baseDescription = 'Academic Term Fee';
        
        switch ($roomConfig['type']) {
            case 'single':
                return $baseDescription . ' (Single Room)';
            case 'double':
                if ($roomConfig['bed_count'] >= 2) {
                    return $baseDescription . ' (Double Room - Two Beds)';
                } else {
                    return $baseDescription . ' (Double Room - One Bed)';
                }
            default:
                return $baseDescription;
        }
    }

    /**
     * Get short-term reservation breakdown
     */
    private function getShortTermBreakdown(Reservation $reservation): array
    {
        if ($reservation->period_type !== 'calendar' || !$reservation->check_in_date || !$reservation->check_out_date) {
            return [];
        }

        $checkIn = Carbon::parse($reservation->check_in_date);
        $checkOut = Carbon::parse($reservation->check_out_date);
        $totalDays = $checkIn->diffInDays($checkOut);

        if ($totalDays <= 0) {
            return [];
        }

        return $this->getOptimalFeeBreakdown($totalDays);
    }

    /**
     * Get detailed breakdown of optimal fee calculation
     */
    private function getOptimalFeeBreakdown(int $totalDays): array
    {
        $breakdown = [];
        $remainingDays = $totalDays;

        // Months breakdown
        $months = intval($remainingDays / 30);
        if ($months > 0) {
            $breakdown[] = [
                'type' => 'monthly',
                'quantity' => $months,
                'unit_price' => self::MONTHLY_FEE,
                'amount' => $months * self::MONTHLY_FEE,
                'description' => $months . ' month' . ($months > 1 ? 's' : ''),
            ];
            $remainingDays -= $months * 30;
        }

        // Weeks breakdown
        $weeks = intval($remainingDays / 7);
        if ($weeks > 0) {
            $breakdown[] = [
                'type' => 'weekly',
                'quantity' => $weeks,
                'unit_price' => self::WEEKLY_FEE,
                'amount' => $weeks * self::WEEKLY_FEE,
                'description' => $weeks . ' week' . ($weeks > 1 ? 's' : ''),
            ];
            $remainingDays -= $weeks * 7;
        }

        // Days breakdown
        if ($remainingDays > 0) {
            $breakdown[] = [
                'type' => 'daily',
                'quantity' => $remainingDays,
                'unit_price' => self::DAILY_FEE,
                'amount' => $remainingDays * self::DAILY_FEE,
                'description' => $remainingDays . ' day' . ($remainingDays > 1 ? 's' : ''),
            ];
        }

        return $breakdown;
    }

    /**
     * Get payment summary for reporting
     */
    public function getPaymentSummary(Reservation $reservation): array
    {
        $accommodation = $this->getAccommodation($reservation);
        $roomConfig = $this->getRoomConfiguration($accommodation);
        $insuranceInfo = $this->handleInsurance($reservation);
        
        return [
            'reservation_id' => $reservation->id,
            'user_id' => $reservation->user_id,
            'period_type' => $reservation->period_type,
            'is_long_term' => $this->isLongTermReservation($reservation),
            'room_configuration' => $roomConfig,
            'insurance_carried_over' => $insuranceInfo['carried_over'],
            'total_amount' => $this->calculatePaymentAmount($reservation, $roomConfig, $insuranceInfo['skip_insurance']),
            'breakdown' => $this->buildPaymentDetails($reservation, 0, $roomConfig, $insuranceInfo),
        ];
    }

    /**
     * Expire old insurance records (utility method for cleanup)
     */
    public function expireOldInsurance(int $daysOld = 365): int
    {
        return Insurance::where('status', self::INSURANCE_ACTIVE)
            ->where('created_at', '<', now()->subDays($daysOld))
            ->update(['status' => self::INSURANCE_EXPIRED]);
    }
}
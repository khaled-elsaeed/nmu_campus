<?php

namespace App\Services\Reservation\Shared;

use App\Models\Reservation\Reservation;
use App\Models\Reservation\Accommodation;
use App\Models\Payment;
use App\Models\Insurance;
use App\Exceptions\BusinessValidationException;

class PaymentService
{
    // Fee constants - organized by category
    const INSURANCE_FEE = 5000;
    
    // Academic term fees
    const SINGLE_ROOM_ACADEMIC_FEE = 10000;
    const DOUBLE_ROOM_ONE_BED_ACADEMIC_FEE = 8000;
    const DOUBLE_ROOM_TWO_BED_ACADEMIC_FEE = 16000;
    
    // Short term fees
    const MONTHLY_FEE = 2500;
    const WEEKLY_FEE = 1500;
    const DAILY_FEE = 250;

    /**
     * Create payment record with calculated amounts and details
     *
     * @param Reservation $reservation
     * @param string|null $notes
     * @return Payment
     * @throws BusinessValidationException
     */
    public function createPaymentRecord(
        Reservation $reservation,
        string $notes = null
    ): Payment {
        // Validate reservation dates first
        $reservation->validateDates();

        // Get accommodation and room information
        $accommodation = $reservation->accommodation;
        $room = $accommodation?->room;
        
        // Handle insurance logic
        $insuranceInfo = $this->handleInsurance($reservation);

        // Calculate total payment amount
        $calculatedAmount = $this->calculatePaymentAmount(
            $reservation, 
            $room, 
            $insuranceInfo['skip_insurance']
        );

        // Add insurance deficit if exists
        if (isset($insuranceInfo['insurance_deficit']) && $insuranceInfo['insurance_deficit'] > 0) {
            $calculatedAmount += $insuranceInfo['insurance_deficit'];
        }

        // Validate the calculated amount
        $this->validatePaymentData($calculatedAmount);

        // Build detailed payment breakdown
        $details = $this->buildPaymentDetails(
            $reservation, 
            $calculatedAmount, 
            $room, 
            $insuranceInfo
        );

        // Create and return payment record
        return Payment::create([
            'reservation_id' => $reservation->id,
            'amount' => $calculatedAmount,
            'status' => 'pending',
            'notes' => $notes,
            'details' => $details,
        ]);
    }

    /**
     * Handle insurance logic - carry over existing or create new
     *
     * @param Reservation $reservation
     * @return array Insurance information with keys: skip_insurance, carried_over, previous_insurance_id, insurance_deficit
     */
    private function handleInsurance(Reservation $reservation): array
    {
        $existingInsurance = $reservation->insurance;
        $insuranceDeficit = 0;
        $skipInsurance = false;
        $carriedOver = false;
        $previousInsuranceId = null;

        if ($existingInsurance) {
            $previousInsuranceId = $existingInsurance->id;
            $existingAmount = $existingInsurance->amount ?? 0;
            
            if ($existingAmount == self::INSURANCE_FEE) {
                // Exact amount - carry over completely
                $existingInsurance->markAsCarriedOver();
                $skipInsurance = true;
                $carriedOver = true;
            } elseif ($existingAmount > self::INSURANCE_FEE) {
                // Overpaid - refund excess and carry over
                $this->refundExcessInsurance($reservation, $existingInsurance, $existingAmount);
                $existingInsurance->markAsCarriedOver();
                $skipInsurance = true;
                $carriedOver = true;
            } else {
                // Underpaid - calculate deficit and carry over
                $insuranceDeficit = self::INSURANCE_FEE - $existingAmount;
                $existingInsurance->markAsCarriedOver();
                $carriedOver = true;
            }
        }

        // Always create new insurance record for current reservation
        $this->createInsuranceRecord($reservation);

        return [
            'skip_insurance' => $skipInsurance,
            'carried_over' => $carriedOver,
            'previous_insurance_id' => $previousInsuranceId,
            'insurance_deficit' => $insuranceDeficit
        ];
    }


    /**
     * Refund excess insurance amount to user's account balance
     *
     * @param Reservation $reservation
     * @param Insurance $insurance
     * @param float $existingAmount
     */
    private function refundExcessInsurance(
        Reservation $reservation, 
        Insurance $insurance, 
        float $existingAmount
    ): void {
        $user = $reservation->user;
        if ($user && $user->accountBalance) {
            $excessAmount = $existingAmount - self::INSURANCE_FEE;
            $user->accountBalance->balance += $excessAmount;
            $user->accountBalance->save();
        }
    }

    /**
     * Create new insurance record for reservation
     *
     * @param Reservation $reservation
     * @return Insurance
     */
    private function createInsuranceRecord(Reservation $reservation): Insurance
    {
        return Insurance::create([
            'reservation_id' => $reservation->id,
            'status' => 'inactive',
            'amount' => self::INSURANCE_FEE,
        ]);
    }

    /**
     * Calculate total payment amount including insurance and accommodation fees
     *
     * @param Reservation $reservation
     * @param mixed $room
     * @param bool $skipInsurance
     * @return float
     */
    public function calculatePaymentAmount(
        Reservation $reservation, 
        $room, 
        bool $skipInsurance = false
    ): float {
        $totalAmount = 0;

        // Add insurance fee if not skipped
        if (!$skipInsurance) {
            $totalAmount += self::INSURANCE_FEE;
        }

        // Add accommodation fees based on reservation type
        if ($reservation->isLongTerm()) {
            $totalAmount += $this->getRoomAcademicTermFees($room, $reservation->accommodation);
        } else {
            $totalAmount += $reservation->calculateShortTermFee(
                self::MONTHLY_FEE, 
                self::WEEKLY_FEE, 
                self::DAILY_FEE
            );
        }

        return $totalAmount;
    }

    /**
     * Calculate academic term fees based on room type and bed count
     *
     * @param mixed $room
     * @param Accommodation $accommodation
     * @return float
     * @throws BusinessValidationException
     */
    private function getRoomAcademicTermFees($room, Accommodation $accommodation): float
    {
        if ($accommodation->type !== 'room') {
            return 0; // Or handle other accommodation types
        }

        if (!$room) {
            throw new BusinessValidationException('Room information is required for academic term reservations.');
        }

        $isDouble = $room->type === 'double';
        $bedCount = $accommodation->bed_count ?? 1;
        
        if ($isDouble) {
            return $bedCount >= 2 
                ? self::DOUBLE_ROOM_TWO_BED_ACADEMIC_FEE 
                : self::DOUBLE_ROOM_ONE_BED_ACADEMIC_FEE;
        }
        
        return self::SINGLE_ROOM_ACADEMIC_FEE;
    }

    /**
     * Validate payment data
     *
     * @param float|null $amount
     * @throws BusinessValidationException
     */
    private function validatePaymentData(?float $amount): void
    {
        if ($amount !== null && $amount < 0) {
            throw new BusinessValidationException(__('Payment amount cannot be negative.'));
        }
    }

    /**
     * Build comprehensive payment details breakdown
     *
     * @param Reservation $reservation
     * @param float $totalAmount
     * @param mixed $room
     * @param array $insuranceInfo
     * @return array
     */
    public function buildPaymentDetails(
        Reservation $reservation, 
        float $totalAmount, 
        $room = null, 
        array $insuranceInfo = []
    ): array {
        $details = [];

        // Add insurance breakdown
        $details = array_merge($details, $this->getInsuranceBreakdown($insuranceInfo));

        // Add accommodation breakdown
        if ($reservation->isLongTerm()) {
            $roomConfig = $this->getRoomConfig($room, $reservation->accommodation);
            $details = array_merge($details, $this->getLongTermBreakdown($roomConfig));
        } else {
            $details = array_merge($details, $reservation->getShortTermBreakdown());
        }

        // Add insurance deficit if exists
        if (isset($insuranceInfo['insurance_deficit']) && $insuranceInfo['insurance_deficit'] > 0) {
            $details[] = [
                'type' => 'insurance_deficit',
                'amount' => $insuranceInfo['insurance_deficit'],
                'description' => 'Additional Insurance Fee Required',
            ];
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
     *
     * @param array $insuranceInfo
     * @return array
     */
    private function getInsuranceBreakdown(array $insuranceInfo): array
    {
        $details = [];

        if ($insuranceInfo['carried_over'] ?? false) {
            if (($insuranceInfo['insurance_deficit'] ?? 0) > 0) {
                $details[] = [
                    'type' => 'insurance_partial_carried_over',
                    'amount' => 0,
                    'description' => 'Insurance Fee (Partially Carried Over)',
                    'previous_insurance_id' => $insuranceInfo['previous_insurance_id'] ?? null,
                ];
            } else {
                $details[] = [
                    'type' => 'insurance_carried_over',
                    'amount' => 0,
                    'description' => 'Insurance Fee (Carried Over from Previous Reservation)',
                    'previous_insurance_id' => $insuranceInfo['previous_insurance_id'] ?? null,
                ];
            }
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
     * Get room configuration details
     *
     * @param mixed $room
     * @param Accommodation $accommodation
     * @return array
     */
    private function getRoomConfig($room, Accommodation $accommodation): array
    {
        return [
            'room_type' => $room->type ?? 'unknown',
            'bed_count' => $accommodation->bed_count ?? 1,
            'accommodation_type' => $accommodation->type,
        ];
    }

    /**
     * Get long term accommodation breakdown
     *
     * @param array $roomConfig
     * @return array
     */
    private function getLongTermBreakdown(array $roomConfig): array
    {
        $details = [];
        
        if ($roomConfig['accommodation_type'] === 'room') {
            $isDouble = $roomConfig['room_type'] === 'double';
            $bedCount = $roomConfig['bed_count'];
            
            if ($isDouble) {
                $feeType = $bedCount >= 2 ? 'double_room_two_bed' : 'double_room_one_bed';
                $amount = $bedCount >= 2 
                    ? self::DOUBLE_ROOM_TWO_BED_ACADEMIC_FEE 
                    : self::DOUBLE_ROOM_ONE_BED_ACADEMIC_FEE;
                $description = $bedCount >= 2 
                    ? 'Double Room (2 Beds) - Academic Term'
                    : 'Double Room (1 Bed) - Academic Term';
            } else {
                $feeType = 'single_room';
                $amount = self::SINGLE_ROOM_ACADEMIC_FEE;
                $description = 'Single Room - Academic Term';
            }
            
            $details[] = [
                'type' => $feeType,
                'amount' => $amount,
                'description' => $description,
            ];
        }
        
        return $details;
    }
}
<?php

namespace App\Services\Reservation;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\Apartment;
use App\Models\Accommodation;
use App\Models\Academic\AcademicTerm;
use App\Models\Payment;
use App\Models\Equipment;
use App\Models\ReservationEquipment;
use App\Exceptions\BusinessValidationException;
use Carbon\Carbon;

class CreateReservationService
{
    // Payment constants
    const INSURANCE_FEE = 5000;
    const ACADEMIC_TERM_FEE = 10000;
    const MONTHLY_FEE = 2500;
    const WEEKLY_FEE = 1500;
    const DAILY_FEE = 250;

    // Reservation types
    const LONG_TERM = 'long';
    const SHORT_TERM = 'short';

    /**
     * Create a new reservation (main entry point).
     *
     * @param array $data
     * @return Reservation
     */
    public function create(array $data): Reservation
    {
        $this->checkForDuplicateReservation($data);
        
        $reservation = $this->createReservationRecord($data);
        $this->handleAccommodationCreation($data, $reservation->id);
        
        $this->createPaymentRecord($reservation, $data['payment'] ?? []);
        $this->assignEquipmentIfProvided($reservation, $data['equipment'] ?? []);

        return $reservation->load(['equipment', 'equipmentTracking']);
    }

    // ================================
    // VALIDATION METHODS
    // ================================

    /**
     * Check for duplicate reservation for the same user and accommodation/academic term.
     */
    private function checkForDuplicateReservation(array $data): void
    {
        $userId = $data['user_id'];
        $academicTermId = $data['academic_term_id'] ?? null;
        $checkInDate = $data['check_in_date'] ?? null;
        $checkOutDate = $data['check_out_date'] ?? null;



        $userReservationQuery = Reservation::where('user_id', $userId)
            ->where('status', '!=', 'cancelled');

        if ($academicTermId) {
            $userReservationQuery->where('academic_term_id', $academicTermId);
        } elseif ($checkInDate && $checkOutDate) {
            $userReservationQuery->where(function ($q) use ($checkInDate, $checkOutDate) {
                $q->whereBetween('check_in_date', [$checkInDate, $checkOutDate])
                  ->orWhereBetween('check_out_date', [$checkInDate, $checkOutDate])
                  ->orWhere(function ($subQ) use ($checkInDate, $checkOutDate) {
                      $subQ->where('check_in_date', '<=', $checkInDate)
                           ->where('check_out_date', '>=', $checkOutDate);
                  });
            });
        }

        $userReservation = $userReservationQuery->first();

        if ($userReservation) {
            throw new BusinessValidationException('A reservation already exists for this user with overlapping dates or academic term.');
        }
    }

    // ================================
    // RESERVATION CREATION METHODS
    // ================================

    /**
     * Create the actual reservation record.
     */
    private function createReservationRecord(array $data): Reservation
    {
        return Reservation::create([
            'user_id' => $data['user_id'],
            'academic_term_id' => $data['academic_term_id'] ?? null,
            'check_in_date' => $data['check_in_date'] ?? null,
            'check_out_date' => $data['check_out_date'] ?? null,
            'status' => $data['status'] ?? 'pending',
            'active' => $data['active'] ?? true,
            'notes' => $data['notes'] ?? null,
        ]);
    }


    // ================================
    // ACCOMMODATION METHODS
    // ================================

    /**
     * Handle accommodation creation based on type.
     */
    private function handleAccommodationCreation(array $data, int $reservationId): void
    {
        $type = $data['accommodation_type'];
        $accommodationId = $data['accommodation_id'];
        $description = $data['description'] ?? null;
        $doubleRoomBedOption = $type === 'room' ? ($data['double_room_bed_option'] ?? null) : null;

        match ($type) {
            'room' => $this->createAccommodationForRoom($accommodationId, $description, $doubleRoomBedOption, $reservationId),
            'apartment' => $this->createAccommodationForApartment($accommodationId, $description, $reservationId),
            default => throw new BusinessValidationException('Invalid accommodation type.')
        };
    }

    /**
     * Create accommodation for a room.
     */
    private function createAccommodationForRoom(int $roomId, ?string $description = null, ?string $doubleRoomBedOption = null, ?int $reservationId = null): int
    {
        $room = Room::find($roomId);

        if (!$room) {
            throw new BusinessValidationException('Room not found.');
        }

        if ($room->purpose !== 'housing') {
            throw new BusinessValidationException('Room is not designated for housing.');
        }

        if ($room->occupancy_status === 'occupied' || $room->available_capacity === 0) {
            throw new BusinessValidationException('Room is fully occupied or has no available capacity.');
        }

        if ($doubleRoomBedOption) {
            if ($room->available_capacity !== 2 || $room->occupancy_status === 'occupied') {
                throw new BusinessValidationException('Selected room does not have 2 available beds or is occupied.');
            }
        }

        $accommodation = Accommodation::create([
            'type' => 'room',
            'description' => $description ?? "Accommodation for Room {$room->number}",
            'accommodatable_type' => Room::class,
            'accommodatable_id' => $roomId,
            'double_room_bed_option' => $doubleRoomBedOption,
            'reservation_id' => $reservationId,
        ]);

        // Update room occupancy
        if ($doubleRoomBedOption) {
            $room->current_occupancy = $room->capacity;
            $room->available_capacity = 0;
            $room->occupancy_status = 'occupied';
            $room->save();
        } else {
            $this->updateRoomOccupancy($room);
        }

        return $accommodation->id;
    }

    /**
     * Update room occupancy after reservation.
     */
    private function updateRoomOccupancy(Room $room): void
    {
        $room->available_capacity = $room->available_capacity - 1;
        $room->current_occupancy = $room->current_occupancy + 1;

        if ($room->current_occupancy === $room->capacity) {
            $room->occupancy_status = 'occupied';
        }

        $room->save();
    }

    /**
     * Create accommodation for an apartment.
     */
    private function createAccommodationForApartment(int $apartmentId, ?string $description = null, ?int $reservationId = null): int
    {
        $apartment = Apartment::find($apartmentId);
        
        if (!$apartment) {
            throw new BusinessValidationException('Selected apartment does not exist.');
        }
        
        if (!$apartment->active) {
            throw new BusinessValidationException('Selected apartment is not active.');
        }

        // Check if accommodation already exists for this apartment
        $existingAccommodation = Accommodation::where('accommodatable_type', Apartment::class)
            ->where('accommodatable_id', $apartmentId)
            ->first();
            
        if ($existingAccommodation) {
            return $existingAccommodation->id;
        }

        $accommodation = Accommodation::create([
            'type' => 'apartment',
            'description' => $description ?? "Accommodation for Apartment {$apartment->number}",
            'accommodatable_type' => Apartment::class,
            'accommodatable_id' => $apartmentId,
            'reservation_id' => $reservationId,
        ]);

        return $accommodation->id;
    }

    // ================================
    // PAYMENT CALCULATION METHODS
    // ================================

    /**
     * Create payment record for the reservation.
     */
    private function createPaymentRecord(Reservation $reservation, array $paymentData): Payment
    {
        // Determine if doubleRoomBedOption is set for this reservation's accommodation
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

    /**
     * Calculate total payment amount based on reservation type.
     */
    private function calculatePaymentAmount(Reservation $reservation, $doubleRoomBedOption = false): float
    {
        $totalAmount = self::INSURANCE_FEE; // Always include insurance fee

        if ($this->isLongTermReservation($reservation)) {
            $totalAmount += $this->calculateLongTermFee($doubleRoomBedOption);
        } else {
            $totalAmount += $this->calculateShortTermFee($reservation);
        }

        return $totalAmount;
    }

    /**
     * Check if reservation is long-term (has academic term).
     */
    private function isLongTermReservation(Reservation $reservation): bool
    {
        return !is_null($reservation->academic_term_id);
    }

    /**
     * Calculate fee for long-term reservation (academic term).
     */
    private function calculateLongTermFee($doubleRoomBedOption = false): float
    {
        // If double room bed option, fee is 16,000
        if ($doubleRoomBedOption) {
            return 16000;
        }
        return self::ACADEMIC_TERM_FEE;
    }

    /**
     * Calculate fee for short-term reservation based on dates.
     */
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

    /**
     * Calculate the most cost-effective fee combination for given days.
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

        // Calculate weeks (7 days each) for remaining days
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

    // ================================
    // PAYMENT DETAILS METHODS
    // ================================

    /**
     * Build detailed payment breakdown.
     */
    private function buildPaymentDetails(Reservation $reservation, float $totalAmount, $doubleRoomBedOption = false): array
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

    /**
     * Get breakdown for long-term reservation.
     */
    private function getLongTermBreakdown($doubleRoomBedOption = false): array
    {
        return [[
            'type' => 'academic_term',
            'amount' => $doubleRoomBedOption ? 16000 : self::ACADEMIC_TERM_FEE,
            'description' => 'Academic Term Fee' . ($doubleRoomBedOption ? ' (Double Room Bed Option)' : ''),
        ]];
    }

    /**
     * Get breakdown for short-term reservation.
     */
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

    /**
     * Get detailed breakdown of optimal fee calculation.
     */
    private function getOptimalFeeBreakdown(int $totalDays): array
    {
        $breakdown = [];
        $remainingDays = $totalDays;

        // Months calculation
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

        // Weeks calculation
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

        // Days calculation
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

    // ================================
    // EQUIPMENT METHODS
    // ================================

    /**
     * Assign equipment to reservation if provided.
     */
    private function assignEquipmentIfProvided(Reservation $reservation, array $equipmentData): void
    {
        if (empty($equipmentData)) {
            return;
        }

        $this->assignEquipmentToReservation($reservation, $equipmentData);
    }

    /**
     * Assign equipment to the reservation.
     */
    private function assignEquipmentToReservation(Reservation $reservation, array $equipmentData): void
    {
        foreach ($equipmentData as $equipmentItem) {
            $equipmentId = $this->extractEquipmentId($equipmentItem);
            $quantity = $this->extractEquipmentQuantity($equipmentItem);

            $this->validateEquipment($equipmentId, $quantity);
            $this->createReservationEquipment($reservation->id, $equipmentId, $quantity);
        }
    }

    /**
     * Extract equipment ID from item data.
     */
    private function extractEquipmentId($equipmentItem): int
    {
        if (is_array($equipmentItem)) {
            return $equipmentItem['equipment_id'] ?? $equipmentItem['id'];
        }
        
        return $equipmentItem;
    }

    /**
     * Extract equipment quantity from item data.
     */
    private function extractEquipmentQuantity($equipmentItem): int
    {
        if (is_array($equipmentItem)) {
            return $equipmentItem['quantity'] ?? 1;
        }
        
        return 1;
    }

    /**
     * Validate equipment exists and quantity is valid.
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

    /**
     * Create reservation equipment record.
     */
    private function createReservationEquipment(int $reservationId, int $equipmentId, int $quantity): void
    {
        ReservationEquipment::create([
            'reservation_id' => $reservationId,
            'equipment_id' => $equipmentId,
            'quantity' => $quantity,
            'overall_status' => 'pending',
        ]);
    }
}
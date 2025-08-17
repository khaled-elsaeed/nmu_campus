<?php

namespace App\Services\Reservation\Shared;

use App\Models\Reservation\Reservation;

class ReservationCreator
{
    /**
     * Create reservation record with individual parameters
     *
     * @param int $userId
     * @param int|null $academicTermId
     * @param string|null $checkInDate
     * @param string|null $checkOutDate
     * @param string $status
     * @param bool $active
     * @param string|null $notes
     * @param string $periodType
     * @return Reservation
     */
    public function create(
        int $userId,
        ?int $academicTermId = null,
        ?string $checkInDate = null,
        ?string $checkOutDate = null,
        string $status = 'pending',
        bool $active = false,
        ?string $notes = null,
        string $periodType
    ): Reservation {
        return Reservation::create([
            'user_id' => $userId,
            'academic_term_id' => $academicTermId,
            'check_in_date' => $checkInDate,
            'check_out_date' => $checkOutDate,
            'status' => $status,
            'active' => $active,
            'notes' => $notes,
            'period_type' => $periodType,
        ]);
    }
}
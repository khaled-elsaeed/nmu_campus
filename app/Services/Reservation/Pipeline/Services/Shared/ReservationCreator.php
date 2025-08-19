<?php

namespace App\Services\Reservation\Pipeline\Services\Shared;

use App\Models\Reservation\Reservation;
use Illuminate\Support\Str;

class ReservationCreator
{
    /**
     * Create a new reservation.
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
        string $periodType = 'academic'
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
            'reservation_number' => $this->generateReservationNumber(),
        ]);
    }

    /**
     * Generate a unique reservation number.
     *
     * @return string
     */
    private function generateReservationNumber(): string
    {
        do {
            $number = 'RES-' . date('Y') . '-' . strtoupper(Str::random(8));
        } while (Reservation::where('reservation_number', $number)->exists());

        return $number;
    }
}

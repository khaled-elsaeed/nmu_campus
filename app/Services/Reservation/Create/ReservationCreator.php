<?php

namespace App\Services\Reservation\Create;

use App\Models\Reservation\Reservation;

class ReservationCreator
{
    public function createReservationRecord(array $data): Reservation
    {
        return Reservation::create([
            'user_id' => $data['user_id'],
            'academic_term_id' => $data['academic_term_id'] ?? null,
            'check_in_date' => $data['check_in_date'] ?? null,
            'check_out_date' => $data['check_out_date'] ?? null,
            'status' => $data['status'] ?? 'pending',
            'active' => false,
            'notes' => $data['notes'] ?? null,
            'period_type' => $data['period_type'] ?? 'academic',
        ]);
    }
}

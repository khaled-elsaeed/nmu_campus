<?php

namespace App\Services\Reservation\Pipeline\Pipes\Shared;

use Closure;
use App\Services\Reservation\Pipeline\Services\Shared\ReservationCreator;
use App\Models\Reservation\Reservation;

class CreateReservationRecord
{
    public function __construct(
        protected ReservationCreator $creator
    ) {}

    /**
     * Handle the incoming request.
     *
     * @param array $data
     * @param Closure $next
     * @return mixed
     */
    public function handle(array $data, Closure $next)
    {
        // Create the reservation record
        $reservation = $this->creator->create(
            userId: $data['user_id'],
            academicTermId: $data['academic_term_id'] ?? null,
            checkInDate: $data['check_in_date'] ?? null,
            checkOutDate: $data['check_out_date'] ?? null,
            status: $data['status'] ?? 'pending',
            active: $data['active'] ?? false,
            notes: $data['notes'] ?? null,
            periodType: $data['period_type']
        );

        // Add the created reservation to the data array
        $data['reservation'] = $reservation;
        $data['reservation_id'] = $reservation->id;

        return $next($data);
    }
}

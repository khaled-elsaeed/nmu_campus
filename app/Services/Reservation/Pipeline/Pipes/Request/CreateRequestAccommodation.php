<?php

namespace App\Services\Reservation\Pipeline\Pipes\Request;

use Closure;
use App\Services\Reservation\Pipeline\Services\Shared\AccommodationService;

class CreateRequestAccommodation
{
    public function __construct(
        protected AccommodationService $accommodationService
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
        $accommodationType = $data['accommodation_type'];
        $roomId = $data['room_id'];
        $apartmentId = $data['apartment_id'] ?? null;
        $bedCount = $data['bed_count'] ?? null;
        $notes = $data['notes'] ?? null;

        $accommodation = $this->accommodationService->createAccommodation(
            type: $accommodationType,
            roomId: $accommodationType === 'room' ? $roomId : null,
            apartmentId: $accommodationType === 'apartment' ? $apartmentId : null,
            roomType: $data['room_type'] ?? null,
            bedCount: $bedCount,
            reservationId: $data['reservation_id'],
            description: $notes
        );

        $data['accommodation'] = $accommodation;

        return $next($data);
    }
}

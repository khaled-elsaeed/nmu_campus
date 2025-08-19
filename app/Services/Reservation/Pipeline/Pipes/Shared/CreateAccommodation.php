<?php

namespace App\Services\Reservation\Pipeline\Pipes\Shared;

use Closure;
use App\Services\Reservation\Pipeline\Services\Shared\AccommodationService;

class CreateAccommodation
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
        // Only create accommodation if building_id, apartment_id, and room_id are provided
        if (!empty($data['building_id']) && !empty($data['apartment_id']) && !empty($data['room_id'])) {
            $accommodation = $this->accommodationService->createAccommodation(
                type: 'room',
                roomId: $data['room_id'],
                roomType: $data['room_type'] ?? null,
                bedCount: $data['bed_count'] ?? null,
                reservationId: $data['reservation_id'],
                description: $data['accommodation_description'] ?? null
            );

            $data['accommodation'] = $accommodation;
        } elseif (!empty($data['building_id']) && !empty($data['apartment_id'])) {
            $accommodation = $this->accommodationService->createAccommodation(
                type: 'apartment',
                apartmentId: $data['apartment_id'],
                reservationId: $data['reservation_id'],
                description: $data['accommodation_description'] ?? null
            );

            $data['accommodation'] = $accommodation;
        }

        return $next($data);
    }
}

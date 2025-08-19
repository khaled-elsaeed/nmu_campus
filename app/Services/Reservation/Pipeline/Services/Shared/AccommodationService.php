<?php

namespace App\Services\Reservation\Pipeline\Services\Shared;

use App\Models\Reservation\Accommodation;
use App\Models\Housing\Room;
use App\Models\Housing\Apartment;

class AccommodationService
{
    /**
     * Create accommodation for a reservation.
     *
     * @param string $type
     * @param int|null $roomId
     * @param int|null $apartmentId
     * @param string|null $roomType
     * @param int|null $bedCount
     * @param int $reservationId
     * @param string|null $description
     * @return Accommodation
     */
    public function createAccommodation(
        string $type,
        ?int $roomId = null,
        ?int $apartmentId = null,
        ?string $roomType = null,
        ?int $bedCount = null,
        int $reservationId,
        ?string $description = null
    ): Accommodation {
        $accommodationData = [
            'type' => $type,
            'reservation_id' => $reservationId,
            'description' => $description,
        ];

        if ($type === 'room' && $roomId) {
            $room = Room::findOrFail($roomId);
            $accommodationData['room_id'] = $roomId;
            $accommodationData['apartment_id'] = $room->apartment_id;
            $accommodationData['building_id'] = $room->apartment->building_id;
            $accommodationData['room_type'] = $roomType;
            $accommodationData['bed_count'] = $bedCount;
        } elseif ($type === 'apartment' && $apartmentId) {
            $apartment = Apartment::findOrFail($apartmentId);
            $accommodationData['apartment_id'] = $apartmentId;
            $accommodationData['building_id'] = $apartment->building_id;
        }

        return Accommodation::create($accommodationData);
    }

    /**
     * Get accommodation details.
     *
     * @param int $accommodationId
     * @return Accommodation
     */
    public function getAccommodation(int $accommodationId): Accommodation
    {
        return Accommodation::with(['room.apartment.building', 'apartment.building'])
            ->findOrFail($accommodationId);
    }

    /**
     * Update accommodation.
     *
     * @param int $accommodationId
     * @param array $data
     * @return Accommodation
     */
    public function updateAccommodation(int $accommodationId, array $data): Accommodation
    {
        $accommodation = Accommodation::findOrFail($accommodationId);
        $accommodation->update($data);
        return $accommodation->fresh();
    }
}

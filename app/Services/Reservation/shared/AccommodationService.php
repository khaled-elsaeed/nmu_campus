<?php

namespace App\Services\Reservation\Shared;

use App\Models\Housing\Room;
use App\Models\Housing\Apartment;
use App\Models\Reservation\Accommodation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Exceptions\BusinessValidationException;

class AccommodationService
{
    /**
     * Create accommodation based on type
     *
     * @param string $type
     * @param int|null $roomId
     * @param int|null $apartmentId
     * @param string|null $RoomType
     * @param int|null $bedCount
     * @param int|null $reservationId
     * @param string|null $description
     * @return Accommodation
     */
    public function createAccommodation(
        string $type,
        ?int $roomId = null,
        ?int $apartmentId = null,
        ?string $roomType = null,
        ?int $bedCount = null,
        ?int $reservationId = null,
        ?string $description = null
    ): Accommodation {
        return match ($type) {
            'room' => $this->createRoomAccommodation($roomId, $roomType, $bedCount, $reservationId, $description),
            'apartment' => $this->createApartmentAccommodation($apartmentId, $reservationId, $description),
            default => throw new BusinessValidationException("Invalid accommodation type: {$type}")
        };
    }

    /**
     * Create room accommodation
     *
     * @param int $roomId
     * @param string|null $RoomType
     * @param int|null $bedCount
     * @param int|null $reservationId
     * @param string|null $description
     * @return Accommodation
     * @throws ModelNotFoundException
     */
    public function createRoomAccommodation(
        int $roomId,
        ?string $RoomType = null,
        ?int $bedCount = null,
        ?int $reservationId = null,
        ?string $description = null
    ): Accommodation {

        $room = Room::with(['apartment.building'])
            ->findOrFail($roomId);

        // Validate room availability
        if (!$room->isAvailableForAccommodation($RoomType)) {
            throw new BusinessValidationException("Room {$room->number} is not available for the requested accommodation type.");
        }

        // Create accommodation with pre-loaded data
        $accommodation = Accommodation::create([
            'type' => 'room',
            'description' => $description ,
            'room_id' => $room->id,
            'apartment_id' => $room->apartment_id,
            'bed_count' => $bedCount,
            'reservation_id' => $reservationId,
        ]);

        // Update room occupancy using model method
        if (!$room->addOccupancy($bedCount)) {
            throw new BusinessValidationException("Failed to update room occupancy - insufficient capacity.");
        }

        return $accommodation;
    }

    /**
     * Create apartment accommodation
     *
     * @param int $apartmentId
     * @param int|null $reservationId
     * @param string|null $description
     * @return Accommodation
     * @throws ModelNotFoundException
     */
    public function createApartmentAccommodation(
        int $apartmentId,
        ?int $reservationId = null,
        ?string $description = null
    ): Accommodation {
        $apartment = Apartment::findOrFail($apartmentId);

        return Accommodation::create([
            'type' => 'apartment',
            'description' => $description,
            'apartment_id' => $apartment->id,
            'reservation_id' => $reservationId,
        ]);
    }
}
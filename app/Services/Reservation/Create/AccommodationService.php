<?php

namespace App\Services\Reservation\Create;

use App\Models\Housing\Room;
use App\Models\Housing\Apartment;
use App\Models\Reservation\Accommodation;
use App\Exceptions\BusinessValidationException;

class AccommodationService
{
    public function handleAccommodationCreation(array $data, int $reservationId): void
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
            'description' => $description ?? "Accommodation for Room {$room->number}, Apartment {$room->apartment->number}, Building {$room->apartment->building}",
            'room_id' => $roomId,
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

    private function updateRoomOccupancy(Room $room): void
    {
        $room->available_capacity = $room->available_capacity - 1;
        $room->current_occupancy = $room->current_occupancy + 1;

        if ($room->current_occupancy === $room->capacity) {
            $room->occupancy_status = 'occupied';
        }

        $room->save();
    }

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
        $existingAccommodation = Accommodation::where('apartment_id', $apartmentId)
            ->first();
            
        if ($existingAccommodation) {
            return $existingAccommodation->id;
        }

        $accommodation = Accommodation::create([
            'type' => 'apartment',
            'description' => $description ?? "Accommodation for Apartment {$apartment->number}",
            'apartment_id' => $apartmentId,
            'reservation_id' => $reservationId,
        ]);

        return $accommodation->id;
    }
}

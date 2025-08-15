<?php

namespace App\Services\Reservation\Shared;

use App\Models\Housing\Room;
use App\Models\Housing\Apartment;
use App\Models\Reservation\Accommodation;

class AccommodationService
{
    /**
     * Create accommodation based on type
     *
     * @param array $data
     * @param int|null $reservationId
     * @return Accommodation
     */
    public function createAccommodation(array $data, ?int $reservationId = null): Accommodation
    {
        return match ($data['type']) {
            'room' => $this->createRoomAccommodation($data, $reservationId),
            'apartment' => $this->createApartmentAccommodation($data, $reservationId),
        };
    }

    /**
     * Create room accommodation
     *
     * @param array $data
     * @param int|null $reservationId
     * @return Accommodation
     */
    public function createRoomAccommodation(array $data, ?int $reservationId = null): Accommodation
    {
        $room = Room::findOrFail($data['room_id']);
        
        $accommodation = Accommodation::create([
            'type' => 'room',
            'description' => $data['description'] ?? $this->generateRoomDescription($room),
            'room_id' => $data['room_id'],
            'apartment_id' => $room->apartment_id,
            'building_id' => $room->apartment->building_id,
            'bed_number' => $data['bed_number'] ?? null,
            'reservation_id' => $reservationId,
        ]);

        // Update room occupancy
        $this->updateRoomOccupancy($room, $data['bed_number'] ?? null);

        return $accommodation;
    }

    /**
     * Create apartment accommodation
     *
     * @param array $data
     * @param int|null $reservationId
     * @return Accommodation
     */
    public function createApartmentAccommodation(array $data, ?int $reservationId = null): Accommodation
    {
        $apartment = Apartment::findOrFail($data['apartment_id']);

        return Accommodation::create([
            'type' => 'apartment',
            'description' => $data['description'] ?? $this->generateApartmentDescription($apartment),
            'apartment_id' => $data['apartment_id'],
            'building_id' => $apartment->building_id,
            'reservation_id' => $reservationId,
        ]);
    }

    /**
     * Update room occupancy based on accommodation
     *
     * @param Room $room
     * @param int|null $bedNumber
     */
    public function updateRoomOccupancy(Room $room, ?int $bedNumber = null): void
    {
        $room->increment('current_occupancy');
        $room->decrement('available_capacity');
        
        if ($room->current_occupancy >= $room->capacity) {
            $room->update(['occupancy_status' => 'occupied']);
        } elseif ($room->current_occupancy > 0) {
            $room->update(['occupancy_status' => 'partial']);
        }
    }

    /**
     * Release accommodation and update occupancy
     *
     * @param Accommodation $accommodation
     */
    public function releaseAccommodation(Accommodation $accommodation): void
    {
        if ($accommodation->type === 'room' && $accommodation->room) {
            $this->decrementRoomOccupancy($accommodation->room, $accommodation->bed_number);
        }
        
        $accommodation->delete();
    }

    /**
     * Decrement room occupancy when releasing accommodation
     *
     * @param Room $room
     * @param int|null $bedNumber
     */
    public function decrementRoomOccupancy(Room $room, ?int $bedNumber = null): void
    {
        if ($bedNumber && $room->type === 'double') {
            $bedField = "bed_{$bedNumber}_occupied";
            
            if ($room->hasAttribute($bedField)) {
                $room->{$bedField} = false;
            }
            
            $occupiedBeds = collect(['bed_1_occupied', 'bed_2_occupied'])
                ->filter(fn($field) => $room->hasAttribute($field) && $room->{$field})
                ->count();
                
            $room->current_occupancy = $occupiedBeds;
            $room->available_capacity = $room->capacity - $occupiedBeds;
        } else {
            $room->decrement('current_occupancy');
            $room->increment('available_capacity');
        }
        
        $room->occupancy_status = $room->current_occupancy === 0 ? 'available' 
            : ($room->current_occupancy >= $room->capacity ? 'occupied' : 'partial');
            
        $room->save();
    }

    /**
     * Transfer accommodation from one reservation to another
     *
     * @param Accommodation $accommodation
     * @param int $newReservationId
     * @return Accommodation
     */
    public function transferAccommodation(Accommodation $accommodation, int $newReservationId): Accommodation
    {
        $accommodation->update(['reservation_id' => $newReservationId]);
        return $accommodation->fresh();
    }

    /**
     * Get available capacity for accommodation
     *
     * @param string $type
     * @param int $accommodationId
     * @return int
     */
    public function getAvailableCapacity(string $type, int $accommodationId): int
    {
        return match ($type) {
            'room' => Room::find($accommodationId)?->available_capacity ?? 0,
            'apartment' => $this->getApartmentAvailableCapacity($accommodationId),
        };
    }

    /**
     * Calculate apartment available capacity
     *
     * @param int $apartmentId
     * @return int
     */
    private function getApartmentAvailableCapacity(int $apartmentId): int
    {
        $apartment = Apartment::find($apartmentId);
        if (!$apartment) return 0;
        
        return $apartment->rooms()
            ->where('purpose', 'housing')
            ->where('active', true)
            ->sum('available_capacity');
    }

    /**
     * Generate room description
     *
     * @param Room $room
     * @return string
     */
    private function generateRoomDescription(Room $room): string
    {
        return "Room {$room->number}, Apartment {$room->apartment->number}, Building {$room->apartment->building->name}";
    }

    /**
     * Generate apartment description
     *
     * @param Apartment $apartment
     * @return string
     */
    private function generateApartmentDescription(Apartment $apartment): string
    {
        return "Apartment {$apartment->number}, Building {$apartment->building->name}";
    }
}
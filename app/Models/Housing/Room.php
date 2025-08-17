<?php

namespace App\Models\Housing;

use App\Models\Reservation\Accommodation;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'apartment_id',
        'number',
        'type',
        'capacity',
        'current_occupancy',
        'available_capacity',
        'purpose',
        'occupancy_status',
        'description',
        'active',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<string>
     */
    protected $appends = ['gender'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    // Relationships

    /**
     * Get the apartment that owns this room.
     */
    public function apartment(): BelongsTo
    {
        return $this->belongsTo(Apartment::class);
    }

    /**
     * Get the accommodations (reservations) for this room.
     */
    public function accommodations(): HasMany
    {
        return $this->hasMany(Accommodation::class);
    }

    // Accessors

    /**
     * Get the gender restriction for the room.
     */
    protected function gender(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->apartment?->building?->gender_restriction,
        );
    }

    /**
     * Get the occupancy status for the room.
     */
    protected function occupancyStatus(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->available_capacity === 0) {
                    return 'full';
                } elseif ($this->current_occupancy === 0) {
                    return 'empty';
                } else {
                    return 'partially occupied';
                }
            }
        );
    }

    // Methods

    /**
     * Check if the room is available for accommodation.
     *
     * @param string|null $requiredFor The type of accommodation ('single', 'double', or null for any)
     * @return bool
     */
    public function isAvailableForAccommodation(string $requiredFor = null): bool
    {
        if (!$this->active || $this->available_capacity <= 0 || $this->purpose !== 'housing') {
            return false;
        }

        if (in_array($this->occupancy_status, ['maintenance', 'occupied', 'reserved'])) {
            return false;
        }

        if ($requiredFor === null) {
            return true;
        }

        switch ($requiredFor) {
            case 'double':
                return $this->available_capacity >= 2 && $this->type === 'double';
            case 'single':
                return $this->available_capacity >= 1;
            default:
                return $this->available_capacity >= 1;
        }
    }

    /**
     * Update room occupancy when removing accommodation
     *
     * @param int $bedCount Number of occupants to remove (default 1)
     * @return bool
     */
    public function removeOccupancy(int $bedCount = 1): bool
    {

        if ($this->current_occupancy >= $bedCount) {
            $this->decrement('current_occupancy', $bedCount);
            $this->increment('available_capacity', $bedCount);
            $this->updateOccupancyStatus();
            return true;
        }

        return false; // Not enough current occupancy to remove
    }

    /**
     * Update room occupancy when adding accommodation
     *
     * @param int $bedCount Number of occupants to add (default 1)
     * @return bool
     */
    public function addOccupancy(int $bedCount = 1): bool
    {

        if ($this->available_capacity >= $bedCount) {
            $this->increment('current_occupancy', $bedCount);
            $this->decrement('available_capacity', $bedCount);
            $this->updateOccupancyStatus();
            return true;
        }

        return false; 
    }


    /**
     * Update room occupancy status based on current occupancy
     *
     * @return void
     */
    private function updateOccupancyStatus(): void
    {
        $status = match (true) {
            $this->current_occupancy === 0 => 'available',
            $this->current_occupancy >= $this->capacity => 'occupied',
            default => 'partially_occupied'
        };

        $this->update(['occupancy_status' => $status]);
    }

    /**
     * Get current occupancy percentage
     *
     * @return float
     */
    public function getOccupancyPercentage(): float
    {
        return $this->capacity > 0 ? ($this->current_occupancy / $this->capacity) * 100 : 0;
    }

    /**
     * Check if room is full
     *
     * @return bool
     */
    public function isFull(): bool
    {
        return $this->available_capacity === 0;
    }

    /**
     * Check if room is empty
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->current_occupancy === 0;
    }
}

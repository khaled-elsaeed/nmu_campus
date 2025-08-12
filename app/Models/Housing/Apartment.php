<?php

namespace App\Models\Housing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Reservation\Accommodation;
use Illuminate\Database\Eloquent\Casts\Attribute;
 

class Apartment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'building_id',
        'number',
        'total_rooms',
        'active',
    ];


    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }


    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = ['current_occupancy', 'available_capacity'];


    /**
     * Get the gender restriction for the room.
     *
     * @return Attribute
     */
    protected function gender(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->building?->gender_restriction,
        );
    }

    /**
     * The current occupancy of the building.
     */
    public function currentOccupancy(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->rooms()->sum('current_occupancy');
            }
        );
    }

    /**
     * Get the available capacity of the building.
     */
    public function availableCapacity(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->rooms()->sum('available_capacity');
            }
        );
    }


    /**
     * Get the building for the apartment.
     *
     * @return BelongsTo
     */
    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    /**
     * Get the rooms for the apartment.
     *
     * @return HasMany
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    /**
     * Get the accommodations (reservations) that include this apartment.
     *
     * @return HasMany
     */
    public function accommodations(): HasMany
    {
        return $this->hasMany(Accommodation::class);
    }
}

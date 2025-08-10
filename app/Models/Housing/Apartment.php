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



    protected function formattedName(): Attribute
    {
        return Attribute::make(
            get: fn() => __('general.apartment', ['number' => $this->number])
        );
    }

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

    protected function formattedGender(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => __('general.' . $this->building?->gender_restriction)
        );
    }

    /**
     * The current occupancy of the apartment.
     */
    public function currentOccupancy(): Attribute
    {
        return Attribute::make(
            get: fn() => formatNumber($this->rooms()->sum('current_occupancy'))
        );
    }

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


    /**
     * Get the location information for the apartment.
     *
     * @return array<string, string|null>
     */
    public function location(): array
    {
        return [
            'number' => $this->number,
            'building_number' => $this->building->number ?? null,
        ];
    }
}

<?php

namespace App\Models\Housing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Casts\Attribute;


class Building extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'number',
        'total_apartments',
        'total_rooms',
        'gender_restriction',
        'active',
        'has_double_rooms',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'has_double_rooms' => 'boolean',
        ];
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = ['current_occupancy', 'available_capacity'];

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
     * Check if building is full
     */
    public function isFull(): bool
    {
        return $this->available_capacity === 0;
    }


    /**
     * Get the apartments for the building.
     */
    public function apartments(): HasMany
    {
        return $this->hasMany(Apartment::class);
    }

    /**
     * Get the rooms for the building through apartments.
     */
    public function rooms(): HasManyThrough
    {
        return $this->hasManyThrough(Room::class, Apartment::class);
    }

};
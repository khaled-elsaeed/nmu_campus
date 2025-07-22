<?php

namespace App\Models\Housing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

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
     * Get the apartments for the building.
     *
     * @return HasMany
     */
    public function apartments(): HasMany
    {
        return $this->hasMany(Apartment::class);
    }

    /**
     * Get the rooms for the building through apartments.
     *
     * @return HasManyThrough
     */
    public function rooms(): HasManyThrough
    {
        return $this->hasManyThrough(Room::class, Apartment::class);
    }
}

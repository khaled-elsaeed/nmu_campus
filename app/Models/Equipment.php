<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name_en',
        'name_ar',
        'category_en',
        'category_ar',
        'description_en',
        'description_ar',
    ];

    /**
     * Get the reservation equipment assignments for this equipment.
     *
     * @return HasMany
     */
    public function reservationEquipment(): HasMany
    {
        return $this->hasMany(ReservationEquipment::class);
    }

    /**
     * Get the reservations that include this equipment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function reservations(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(Reservation::class, ReservationEquipment::class);
    }
}

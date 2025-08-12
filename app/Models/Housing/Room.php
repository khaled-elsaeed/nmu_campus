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
    protected $appends = ['gender','occupancy_status'];

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
}
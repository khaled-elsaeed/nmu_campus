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
    protected $appends = [
        'gender',
        'formatted_gender',
        'formatted_name',
    ];

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
     * Get the formatted gender text for the room.
     */
    protected function formattedGender(): Attribute
    {
        return Attribute::make(
            get: fn($value) => match ($this->gender) {
                'male' => __('general.male'),
                'female' => __('general.female'),
                default => __('general.unknown'),
            },
        );
    }

    /**
     * Get the formatted name for the room.
     */
    protected function formattedName(): Attribute
    {
        return Attribute::make(
            get: fn($value) => __('general.room', ['number' => $this->number]),
        );
    }

    // Methods

    /**
     * Get the location information for the room.
     *
     * @return array<string, string|null>
     */
    public function getLocationAttribute(): array
    {
        return [
            'number' => $this->number,
            'apartment_number' => $this->apartment?->number,
            'building_number' => $this->apartment?->building?->number,
        ];
    }
}
<?php

namespace App\Models\Housing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Reservation\Accommodation;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Room extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
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
     * @var list<string>
     */
    protected $appends = ['gender'];

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
     * Get the gender restriction for the room.
     *
     * @return Attribute
     */
    protected function gender(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->apartment?->building?->gender_restriction,
        );
    }

    /**
     * Get the apartment for the room.
     *
     * @return BelongsTo
     */
    public function apartment(): BelongsTo
    {
        return $this->belongsTo(Apartment::class);
    }

    /**
     * Get the accommodations (reservations) that include this room.
     *
     * @return HasMany
     */
    public function accommodations(): HasMany
    {
        return $this->hasMany(Accommodation::class);
    }

    /**
     * Get the location information for the room.
     *
     * @return array<string, string|null>
     */
    public function location(): array
    {
        return [
            'number' => $this->number,
            'apartment_number' => $this->apartment->number ?? null,
            'building_number' => $this->apartment->building->number ?? null,
        ];
    }
}

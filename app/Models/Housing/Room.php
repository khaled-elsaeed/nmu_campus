<?php

namespace App\Models\Housing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\Housing\Accommodation;

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
     * @return MorphMany
     */
    public function accommodations(): MorphMany
    {
        return $this->morphMany(Accommodation::class, 'accommodatable');
    }
}

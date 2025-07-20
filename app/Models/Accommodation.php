<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Accommodation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'type',
        'description',
        'accommodatable_type',
        'accommodatable_id',
        'double_room_bed_option',
        'reservation_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // No casts needed for current fields
        ];
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = ['name'];

    /**
     * Get the accommodatable model (room or apartment).
     *
     * @return MorphTo
     */
    public function accommodatable(): MorphTo
    {
        return $this->morphTo();
    }


    /**
     * Get the reservations for this accommodation.
     *
     * @return HasMany
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Get the reservation for this accommodation.
     */
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    /**
     * Get the accommodation's name based on its type and accommodatable.
     *
     * @return Attribute
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if (!$this->accommodatable) {
                    return 'Unknown Accommodation';
                }

                if ($this->accommodatable_type === Room::class) {
                    $room = $this->accommodatable;
                    return "Room {$room->number}";
                } elseif ($this->accommodatable_type === Apartment::class) {
                    $apartment = $this->accommodatable;
                    return "Apartment {$apartment->number}";
                }

                return 'Unknown Type';
            }
        );
    }

    /**
     * Get the accommodation's display name with full hierarchy.
     *
     * @return Attribute
     */
    protected function detail(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if (!$this->accommodatable) {
                    return 'Unknown Accommodation';
                }

                if ($this->accommodatable_type === Room::class) {
                    $room = $this->accommodatable;
                    $apartment = $room->apartment;
                    $building = $apartment->building;
                    return "Room {$room->number} (Apartment {$apartment->number}, Building {$building->number})";
                } elseif ($this->accommodatable_type === Apartment::class) {
                    $apartment = $this->accommodatable;
                    $building = $apartment->building;
                    return "Apartment {$apartment->number} (Building {$building->number})";
                }

                return 'Unknown Type';
            }
        );
    }


  

    /**
     * Get the building information for this accommodation.
     *
     * @return Building|null
     */
    public function getBuilding(): ?Building
    {
        if ($this->accommodatable_type === Room::class) {
            return $this->accommodatable->apartment->building;
        } elseif ($this->accommodatable_type === Apartment::class) {
            return $this->accommodatable->building;
        }

        return null;
    }

    /**
     * Get the apartment information for this accommodation.
     *
     * @return Apartment|null
     */
    public function getApartment(): ?Apartment
    {
        if ($this->accommodatable_type === Room::class) {
            return $this->accommodatable->apartment;
        } elseif ($this->accommodatable_type === Apartment::class) {
            return $this->accommodatable;
        }

        return null;
    }

    /**
     * Get the room information for this accommodation.
     *
     * @return Room|null
     */
    public function getRoom(): ?Room
    {
        if ($this->accommodatable_type === Room::class) {
            return $this->accommodatable;
        }

        return null;
    }

}

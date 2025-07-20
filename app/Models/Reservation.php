<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Reservation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'reservation_number',
        'user_id',
        'academic_term_id',
        'check_in_date',
        'check_out_date',
        'status',
        'active',
        'notes',
        'confirmed_at',
        'checked_in_at',
        'checked_out_at',
        'cancelled_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'check_in_date' => 'date',
            'check_out_date' => 'date',
            'active' => 'boolean',
            'confirmed_at' => 'datetime',
            'checked_in_at' => 'datetime',
            'checked_out_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the reservation.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the accommodation (room or apartment) for the reservation.
     *
     * @return BelongsTo
     */
    public function accommodation()
    {
        return $this->hasOne(Accommodation::class);
    }

    /**
     * Get the academic term for the reservation.
     *
     * @return BelongsTo
     */
    public function academicTerm(): BelongsTo
    {
        return $this->belongsTo(AcademicTerm::class);
    }

    /**
     * Get the payments for the reservation.
     *
     * @return HasMany
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the equipment tracking for the reservation.
     *
     * @return HasMany
     */
    public function equipmentTracking(): HasMany
    {
        return $this->hasMany(ReservationEquipment::class);
    }

    /**
     * Get the equipment for the reservation through the reservation_equipment pivot table.
     */
    public function equipment()
    {
        return $this->belongsToMany(Equipment::class, 'reservation_equipment')
            ->withPivot([
                'quantity',
                'overall_status',
                'received_status',
                'received_notes',
                'received_at',
                'received_by',
                'returned_status',
                'returned_notes',
                'returned_at',
                'returned_by',
            ])
            ->withTimestamps();
    }
}

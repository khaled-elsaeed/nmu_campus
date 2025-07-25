<?php

namespace App\Models\Reservation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User;
use App\Models\Academic\AcademicTerm;
use App\Models\EquipmentCheckout;

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
        'period_type',
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
            'activated_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($reservation) {
            if (empty($reservation->reservation_number)) {
                $reservation->reservation_number = static::generateReservationNumber();
            }
        });
    }

    /**
     * Generate unique reservation number.
     */
    public static function generateReservationNumber(): string
    {
        $prefix = 'RES';
        $year = now()->year;
        $month = now()->format('m');

        $lastReservation = static::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->latest('id')
            ->first();

        $sequence = $lastReservation ?
            intval(substr($lastReservation->reservation_number, -4)) + 1 : 1;

        return sprintf('%s-%s%s-%04d', $prefix, $year, $month, $sequence);
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
     * Get the accommodation for the reservation.
     *
     * @return HasOne
     */
    public function accommodation(): HasOne
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
        return $this->hasMany(EquipmentCheckout::class);
    }


    /**
     * Determine if the reservation is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return (bool) $this->active;
    }
}
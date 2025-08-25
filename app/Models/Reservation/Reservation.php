<?php

namespace App\Models\Reservation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User;
use App\Models\Payment;
use App\Models\Academic\AcademicTerm;
use App\Models\EquipmentCheckout;
use Illuminate\Database\Eloquent\Attributes\Scope;


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

    public function insurance(): HasOne
    {
        return $this->hasOne(Insurance::class);
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

    /**
     * Check if user has any active reservations
     *
     * @param int $userId
     * @return bool
     */
    public static function hasActiveReservations(int $userId): bool
    {
        return static::where('user_id', $userId)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->exists();
    }


    /**
     * Get user's conflicting reservations for a date range
     *
     * @param int $userId
     * @param string $checkInDate
     * @param string $checkOutDate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getConflictingReservations(int $userId, string $checkInDate, string $checkOutDate)
    {
        return static::where('user_id', $userId)
            ->where('status', '!=', 'cancelled')
            ->where('check_in_date', '<', $checkOutDate)
            ->where('check_out_date', '>', $checkInDate)
            ->get();
    }

   /**
     * Find conflicting reservation for a user and reservation request
     *
     * @param int $userId
     * @param int|null $academicTermId
     * @param string|null $checkInDate
     * @param string|null $checkOutDate
     * @return static|null
     */
    public static function findConflictingReservation(
        int $userId,
        ?int $academicTermId,
        ?string $checkInDate,
        ?string $checkOutDate
    ): ?static {
        return static::where('user_id', $userId)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($academicTermId, $checkInDate, $checkOutDate) {
                $query
                    // Check for same academic term conflicts
                    ->when($academicTermId, fn($q) => $q->where('academic_term_id', $academicTermId))
                    
                    // Check for direct date overlaps (non-academic reservations)
                    ->when(
                        $checkInDate && $checkOutDate, 
                        fn($q) => $q->orWhere(function ($subQuery) use ($checkInDate, $checkOutDate) {
                            $subQuery->whereNull('academic_term_id')
                                    ->where('check_in_date', '<', $checkOutDate)
                                    ->where('check_out_date', '>', $checkInDate);
                        })
                    )
                    
                    // Check for academic term date overlaps
                    ->when(
                        $checkInDate && $checkOutDate,
                        fn($q) => $q->orWhereHas('academicTerm', function ($termQuery) use ($checkInDate, $checkOutDate) {
                            $termQuery->where('start_date', '<', $checkOutDate)
                                    ->where('end_date', '>', $checkInDate);
                        })
                    );
            })
            ->first();
    }


     /**
     * Scope a query to only include active reservations.
     */
    #[Scope]
    public function active(Builder $query): void
    {
        $query->whereIn('status', ['confirmed', 'checked_in']);
    }

    /**
     * Scope a query to only include non-cancelled reservations.
     */
    #[Scope]
    public function notCancelled(Builder $query): void
    {
        $query->where('status', '!=', 'cancelled');
    }

    /**
     * Scope a query to only include conflicting reservations.
     */
    #[Scope]
    public function conflictingDates(Builder $query, string $checkInDate, string $checkOutDate): void
    {
        $query->where('check_in_date', '<', $checkOutDate)
                ->where('check_out_date', '>', $checkInDate);
    }

    /**
     * Check if reservation is long-term (academic term)
     */
    public function isLongTerm(): bool
    {
        return $this->period_type === 'academic';
    }

    /**
     * Validate reservation dates
     */
    public function validateDates(): void
    {
        if ($this->period_type === 'calendar') {
            if (!$this->check_in_date || !$this->check_out_date) {
                throw new BusinessValidationException(__('Calendar-based reservations must have check-in and check-out dates.'));
            }

            $checkIn = Carbon::parse($this->check_in_date);
            $checkOut = Carbon::parse($this->check_out_date);

            if ($checkOut->lte($checkIn)) {
                throw new BusinessValidationException(__('Check-out date must be after check-in date.'));
            }

            if ($checkIn->isPast()) {
                throw new BusinessValidationException(__('Check-in date cannot be in the past.'));
            }
        } elseif ($this->period_type === 'academic') {
            if (!$this->academic_term_id) {
                throw new BusinessValidationException(__('Academic reservations must have a valid academic term ID.'));
            }
        } else {
            throw new BusinessValidationException(__('Invalid period type. Must be either "academic" or "calendar".'));
        }
    }

    /**
     * Calculate short-term fee with optimal pricing
     */
    public function calculateShortTermFee(double $monthlyFee, double $weeklyFee, double $dailyFee): float
    {
        if ($this->period_type !== 'calendar' || !$this->check_in_date || !$this->check_out_date) {
            return 0;
        }

        $checkIn = Carbon::parse($this->check_in_date);
        $checkOut = Carbon::parse($this->check_out_date);
        $totalDays = $checkIn->diffInDays($checkOut);

        if ($totalDays <= 0) {
            return 0;
        }

        return $this->calculateOptimalFee($monthlyFee, $weeklyFee, $dailyFee, $totalDays);
    }

    /**
     * Calculate optimal fee breakdown for given days
     */
    private function calculateOptimalFee(double $monthlyFee,double $weeklyFee,double $dailyFee,int $totalDays): float
    {
        if ($totalDays <= 0) {
            return 0;
        }

        $totalFee = 0;
        $remainingDays = $totalDays;

        // Calculate months (30 days each)
        $months = intval($remainingDays / 30);
        if ($months > 0) {
            $totalFee += $months * $monthlyFee;
            $remainingDays -= $months * 30;
        }

        // Calculate weeks (7 days each)
        $weeks = intval($remainingDays / 7);
        if ($weeks > 0) {
            $totalFee += $weeks * $weeklyFee;
            $remainingDays -= $weeks * 7;
        }

        // Calculate remaining days
        if ($remainingDays > 0) {
            $totalFee += $remainingDays * $dailyFee;
        }

        return $totalFee;
    }

    /**
     * Get short-term reservation breakdown
     */
    public function getShortTermBreakdown(double $monthlyFee,double $weeklyFee,double $dailyFee): array
    {
        if ($this->period_type !== 'calendar' || !$this->check_in_date || !$this->check_out_date) {
            return [];
        }

        $checkIn = Carbon::parse($this->check_in_date);
        $checkOut = Carbon::parse($this->check_out_date);
        $totalDays = $checkIn->diffInDays($checkOut);

        if ($totalDays <= 0) {
            return [];
        }

        return $this->getOptimalFeeBreakdown($monthlyFee, $weeklyFee, $dailyFee, $totalDays);
    }

    /**
     * Get detailed breakdown of optimal fee calculation
     */
    private function getOptimalFeeBreakdown(double $monthlyFee,double $weeklyFee,double $dailyFee, int $totalDays): array
    {
        $breakdown = [];
        $remainingDays = $totalDays;

        // Months breakdown
        $months = intval($remainingDays / 30);
        if ($months > 0) {
            $breakdown[] = [
                'type' => 'monthly',
                'quantity' => $months,
                'unit_price' => $monthlyFee,
                'amount' => $months * $monthlyFee,
                'description' => $months . ' month' . ($months > 1 ? 's' : ''),
            ];
            $remainingDays -= $months * 30;
        }

        // Weeks breakdown
        $weeks = intval($remainingDays / 7);
        if ($weeks > 0) {
            $breakdown[] = [
                'type' => 'weekly',
                'quantity' => $weeks,
                'unit_price' => $weeklyFee,
                'amount' => $weeks * $weeklyFee,
                'description' => $weeks . ' week' . ($weeks > 1 ? 's' : ''),
            ];
            $remainingDays -= $weeks * 7;
        }

        // Days breakdown
        if ($remainingDays > 0) {
            $breakdown[] = [
                'type' => 'daily',
                'quantity' => $remainingDays,
                'unit_price' => $dailyFee,
                'amount' => $remainingDays * $dailyFee,
                'description' => $remainingDays . ' day' . ($remainingDays > 1 ? 's' : ''),
            ];
        }

        return $breakdown;
    }

}
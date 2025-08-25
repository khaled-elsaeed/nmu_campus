<?php

namespace App\Models\Reservation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\Sibling;
use App\Models\Academic\AcademicTerm;

class ReservationRequest extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'request_number',
        'user_id',
        'academic_term_id',
        'accommodation_type', 
        'room_type',
        'sibling_id',
        'bed_count',
        'check_in_date',
        'check_out_date',
        'status',
        'resident_notes',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
        'approved_at',
        'rejected_at',
        'rejection_reason',
        'reservation_id',
        'period_type',
        'stay_with_sibling'
        ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'requested_check_in_date' => 'date',
            'requested_check_out_date' => 'date',
            'reviewed_at' => 'datetime',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($request) {
            if (empty($request->request_number)) {
                $request->request_number = static::generateRequestNumber();
            }
        });
    }

    /**
     * Generate unique request number.
     */
    public static function generateRequestNumber(): string
    {
        $prefix = 'REQ';
        $year = now()->year;
        $month = now()->format('m');
        
        $lastRequest = static::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->latest('id')
            ->first();
        
        $sequence = $lastRequest ? 
            intval(substr($lastRequest->request_number, -4)) + 1 : 1;
        
        return sprintf('%s-%s%s-%04d', $prefix, $year, $month, $sequence);
    }

    /**
     * Get the user who made the request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the sibling which user requested to stay with.
     */
    public function sibling(): BelongsTo
    {
        return $this->belongsTo(Sibling::class);
    }

    /**
     * Get the academic term for the request.
     */
    public function academicTerm(): BelongsTo
    {
        return $this->belongsTo(AcademicTerm::class);
    }

    /**
     * Get the admin who reviewed the request.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the created reservation (if approved).
     */
    public function createdReservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class, 'created_reservation_id');
    }

}
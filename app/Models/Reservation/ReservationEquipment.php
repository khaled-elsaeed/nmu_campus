<?php

namespace App\Models\Reservation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Equipment;
use App\Models\User;

class ReservationEquipment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'reservation_id',
        'equipment_id',
        'quantity',
        'received_status',
        'received_notes',
        'received_at',
        'received_by',
        'returned_status',
        'returned_notes',
        'returned_at',
        'returned_by',
        'overall_status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'received_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    /**
     * Get the reservation that owns the equipment tracking.
     */
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    /**
     * Get the equipment being tracked.
     */
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * Get the user who received the equipment.
     */
    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /**
     * Get the user who returned the equipment.
     */
    public function returnedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    /**
     * Scope to get equipment by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('overall_status', $status);
    }

    /**
     * Scope to get equipment by received status.
     */
    public function scopeByReceivedStatus($query, $status)
    {
        return $query->where('received_status', $status);
    }

    /**
     * Scope to get equipment by returned status.
     */
    public function scopeByReturnedStatus($query, $status)
    {
        return $query->where('returned_status', $status);
    }

    /**
     * Check if equipment is received.
     */
    public function isReceived(): bool
    {
        return $this->received_at !== null;
    }

    /**
     * Check if equipment is returned.
     */
    public function isReturned(): bool
    {
        return $this->returned_at !== null;
    }

    /**
     * Check if equipment is damaged when received.
     */
    public function isDamagedWhenReceived(): bool
    {
        return $this->received_status === 'damaged';
    }

    /**
     * Check if equipment is missing when received.
     */
    public function isMissingWhenReceived(): bool
    {
        return $this->received_status === 'missing';
    }

    /**
     * Check if equipment is damaged when returned.
     */
    public function isDamagedWhenReturned(): bool
    {
        return $this->returned_status === 'damaged';
    }

    /**
     * Check if equipment is missing when returned.
     */
    public function isMissingWhenReturned(): bool
    {
        return $this->returned_status === 'missing';
    }
} 
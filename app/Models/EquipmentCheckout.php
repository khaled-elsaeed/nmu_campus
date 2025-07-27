<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Reservation\Reservation;

class EquipmentCheckout extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'reviewed_by',
        'overall_status',
        'given_at',
        'returned_at',
    ];

    protected $casts = [
        'given_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function equipmentDetails(): HasMany
    {
        return $this->hasMany(EquipmentCheckoutDetail::class);
    }
}


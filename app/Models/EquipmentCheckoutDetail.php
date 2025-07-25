<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentCheckoutDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_checkout_id',
        'equipment_id',
        'quantity_given',
        'given_status',
        'given_notes',
        'quantity_returned',
        'returned_status',
        'returned_notes',
    ];

    public function equipmentCheckout(): BelongsTo
    {
        return $this->belongsTo(EquipmentCheckout::class);
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }
}
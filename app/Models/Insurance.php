<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insurance extends Model
{
    use HasFactory;

    protected $table = 'insurances';

    protected $fillable = [
        'reservation_id',
        'amount',
        'status',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
} 
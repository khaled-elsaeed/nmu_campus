<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name_en',
        'name_ar',
        'category_en',
        'category_ar',
        'description_en',
        'description_ar',
        'is_shared', 
        'price_per_quantity',
    ];



    public function checkoutDetails(): HasMany
    {
        return $this->hasMany(EquipmentCheckoutDetail::class);
    }
}

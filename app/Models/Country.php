<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'name_en',
        'name_ar',
        'nationality_en',
        'nationality_ar',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = ['name', 'nationality'];

    /**
     * Get the country's name depending on locale.
     *
     * @return Attribute
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) =>
                $attributes['name_' . app()->getLocale()] ??
                $attributes['name_en'] ??
                $attributes['name_ar'] ??
                null
        );
    }

    /**
     * Get the country's nationality depending on locale.
     *
     * @return Attribute
     */
    protected function nationality(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) =>
                $attributes['nationality_' . app()->getLocale()] ??
                $attributes['nationality_en'] ??
                $attributes['nationality_ar'] ??
                null
        );
    }

    /**
     * Get the governorates for the country.
     *
     * @return HasMany
     */
    public function governorates(): HasMany
    {
        return $this->hasMany(Governorate::class);
    }
} 
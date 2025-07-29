<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Resident\Student;

class Nationality extends Model
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
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = ['name'];

    /**
     * Get the nationality's name depending on locale.
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
     * Get the students for the nationality.
     *
     * @return HasMany
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }
}
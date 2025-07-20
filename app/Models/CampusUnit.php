<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CampusUnit extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name_en',
        'name_ar',
        'description',
        'type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type' => 'string',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = ['name'];

    /**
     * Get the campus unit's name depending on locale.
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
     * Get the staff for the campus unit.
     *
     * @return HasMany
     */
    public function staff(): HasMany
    {
        return $this->morphMany(Staff::class, 'unit');
    }

    /**
     * Get the campus unit type options.
     *
     * @return array
     */
    public static function getTypeOptions(): array
    {
        return [
            'management' => 'Management',
            'maintenance' => 'Maintenance',
            'clinic' => 'Clinic',
            'security' => 'Security',
        ];
    }
}

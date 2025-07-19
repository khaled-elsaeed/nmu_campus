<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicTerm extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'season',
        'year',
        'semester_number',
        'start_date',
        'end_date',
        'active',
        'current',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = ['name'];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date'      => 'date',
            'end_date'        => 'date',
            'active'          => 'boolean',
            'current'         => 'boolean',
            'semester_number' => 'integer',
        ];
    }

    /**
     * Get the academic term's name (season + year).
     *
     * @return Attribute
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) =>
                ucfirst($attributes['season'] ?? '') . ' ' . ($attributes['year'] ?? '')
        );
    }

    /**
     * Get the reservations for the academic term.
     *
     * @return HasMany
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'academic_term_id');
    }
}

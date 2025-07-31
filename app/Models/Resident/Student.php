<?php

namespace App\Models\Resident;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Academic\Faculty;
use App\Models\Academic\Program;
use App\Models\User;
use App\Models\Governorate;
use App\Models\City;
use App\Models\Nationality;

class Student extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'name_en',
        'name_ar',
        'academic_id',
        'national_id',
        'academic_email',
        'phone',
        'date_of_birth',
        'level',
        'faculty_id',
        'program_id',
        'nationality_id',
        'governorate_id',
        'city_id',
        'address',
        'is_profile_complete',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = ['name', 'date_of_birth'];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_profile_complete' => 'boolean',
            'date_of_birth' => 'date:Y-m-d',
        ];
    }

    /**
     * Get the student's name depending on locale.
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
     * Get the student's date of birth in Y-m-d format.
     *
     * @return Attribute
     */
    protected function dateOfBirth(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) =>
                isset($attributes['date_of_birth']) && $attributes['date_of_birth']
                    ? date('Y-m-d', strtotime($attributes['date_of_birth']))
                    : null
        );
    }


    /**
     * Get the user for the student.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the faculty for the student.
     *
     * @return BelongsTo
     */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * Get the program for the student.
     *
     * @return BelongsTo
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the nationality for the student.
     *
     * @return BelongsTo
     */
    public function nationality(): BelongsTo
    {
        return $this->belongsTo(Nationality::class);
    }

    /**
     * Get the governorate for the student.
     *
     * @return BelongsTo
     */
    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class);
    }

    /**
     * Get the city for the student.
     *
     * @return BelongsTo
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}

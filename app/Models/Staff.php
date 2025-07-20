<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Staff extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'staff_category_id',
        'notes',
        'unit_id',
        'unit_type',
        'national_id',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = ['name'];

    /**
     * Get the staff's type name depending on unit type.
     *
     * @return Attribute
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if ($this->isFacultyStaff()) {
                    return 'Faculty';
                }
                if ($this->isDepartmentStaff()) {
                    return 'Department';
                }
                if ($this->isCampusStaff()) {
                    return 'Campus Unit';
                }
                return 'Unassigned';
            }
        );
    }


    /**
     * Get the user for the staff member.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the staff category for the staff member.
     */
    public function staffCategory(): BelongsTo
    {
        return $this->belongsTo(StaffCategory::class, 'staff_category_id');
    }

    /**
     * Get the unit (faculty, department, or campus unit) for the staff member.
     */
    public function unit(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the faculty for the staff member (if unit is a faculty).
     */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class, 'unit_id')
            ->where('unit_type', Faculty::class);
    }

    /**
     * Get the department for the staff member (if unit is a department).
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'unit_id')
            ->where('unit_type', Department::class);
    }

    /**
     * Get the campus unit for the staff member (if unit is a campus unit).
     */
    public function campusUnit(): BelongsTo
    {
        return $this->belongsTo(CampusUnit::class, 'unit_id')
            ->where('unit_type', CampusUnit::class);
    }

    /**
     * Determine if the staff is faculty staff.
     */
    public function isFacultyStaff(): bool
    {
        return $this->unit_type === Faculty::class;
    }

    /**
     * Determine if the staff is department staff.
     */
    public function isDepartmentStaff(): bool
    {
        return $this->unit_type === Department::class;
    }

    /**
     * Determine if the staff is campus staff.
     */
    public function isCampusStaff(): bool
    {
        return $this->unit_type === CampusUnit::class;
    }

}

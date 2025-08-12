<?php

namespace App\Models\Resident;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\User;
use App\Models\Academic\Faculty;
use App\Models\Department;
use App\Models\CampusUnit;

class Staff extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'unit_type',
        'notes',
        'faculty_id',
        'department_id',
        'campus_unit_id',
        'national_id',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = ['unit_name'];


    public function unitName(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                // Use loaded relationships if available, else fallback to null
                if ($this->faculty) {
                    return $this->faculty->name ?? null;
                }
                if ($this->department) {
                    return $this->department->name ?? null;
                }
                if ($this->campusUnit) {
                    return $this->campusUnit->name ?? null;
                }
                return null;
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
     * Get the faculty for the staff member (if unit is a faculty).
     */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * Get the department for the staff member (if unit is a department).
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the campus unit for the staff member (if unit is a campus unit).
     */
    public function campusUnit(): BelongsTo
    {
        return $this->belongsTo(CampusUnit::class);
    }

    /**
     * Determine if the staff is faculty staff.
     */
    public function isFacultyStaff(): bool
    {
        return $this->unit_type === 'faculty';
    }

    /**
     * Determine if the staff is department staff.
     */
    public function isDepartmentStaff(): bool
    {
        return $this->unit_type === 'department';
    }

    /**
     * Determine if the staff is campus staff.
     */
    public function isCampusStaff(): bool
    {
        return $this->unit_type === 'campus';
    }

}

<?php

namespace App\Models\Resident;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\User;
use App\Models\StaffCategory;
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
    protected $appends = ['work_unit'];

    /**
     * Get the staff's work unit details (name and type).
     *
     * @return Attribute
     */
    protected function WorkUnit(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if ($this->isFacultyStaff() && $this->faculty) {
                    return [
                        'id' => $this->faculty->id ?? null,
                        'name' => $this->faculty->name ?? 'Faculty',
                        'type' => 'faculty',
                    ];
                }
                if ($this->isDepartmentStaff() && $this->department) {
                    return [
                        'id' => $this->department->id ?? null,
                        'name' => $this->department->name ?? 'Department',
                        'type' => 'administrative',
                    ];
                }
                if ($this->isCampusStaff() && $this->campusUnit) {
                    return [
                        'id' => $this->campusUnit->id ?? null,
                        'name' => $this->campusUnit->name ?? 'Campus Unit',
                        'type' => 'campus',
                    ];
                }
                return [
                    'id' => null,
                    'name' => 'Unassigned',
                    'type' => null,
                ];
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
        return $this->belongsTo(Faculty::class, 'unit_id');
    }

    /**
     * Get the department for the staff member (if unit is a department).
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'unit_id');
    }

    /**
     * Get the campus unit for the staff member (if unit is a campus unit).
     */
    public function campusUnit(): BelongsTo
    {
        return $this->belongsTo(CampusUnit::class, 'unit_id');
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

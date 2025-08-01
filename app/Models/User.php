<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Resident\Student;
use App\Models\Resident\Staff;
use App\Models\Reservation\Reservation;
use App\Models\StudentArchive;
use App\Models\StudentParent;
use App\Models\Sibling;
use App\Models\EmergencyContact;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name_en',
        'name_ar',
        'gender',
        'email',
        'email_verified_at',
        'password',
        'force_change_password',
        'last_login',
        'remember_token',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = ['name'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'      => 'datetime',
            'profile_complete'       => 'boolean',
            'force_change_password'  => 'boolean',
            'last_login'             => 'datetime',
            'password'               => 'hashed',
        ];
    }

    /**
     * Get the user's name depending on locale.
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
     * Get the user's last login formatted as a readable string.
     *
     * @return Attribute
     */
    protected function lastLogin(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) =>
                isset($attributes['last_login']) && $attributes['last_login']
                    ? formatDate($attributes['last_login'])
                    : null
        );
    }

    /**
     * Get the student profile associated with the user.
     *
     * @return HasOne
     */
    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Get the staff profile associated with the user.
     *
     * @return HasOne
     */
    public function staff(): HasOne
    {
        return $this->hasOne(Staff::class);
    }

    /**
     * Get the reservations associated with the user.
     *
     * @return HasMany
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Get the student archive associated with the user.
     *
     * @return HasOne
     */
    public function studentArchive(): HasOne
    {
        return $this->hasOne(StudentArchive::class);
    }

    /**
     * Get the parent information associated with the user.
     *
     * @return HasOne
     */
    public function parent(): HasOne
    {
        return $this->hasOne(StudentParent::class);
    }

    /**
     * Get the sibling information associated with the user.
     *
     * @return HasOne
     */
    public function sibling(): HasOne
    {
        return $this->hasOne(Sibling::class);
    }

    /**
     * Get the emergency contact associated with the user.
     *
     * @return HasOne
     */
    public function emergencyContact(): HasOne
    {
        return $this->hasOne(EmergencyContact::class);
    }

    /**
     * Get the user's profile relation, returning either staff or student relation depending on which exists.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne|null
     */
    public function profile()
    {
        if ($this->staff()->exists()) {
            return $this->staff();
        }
        if ($this->student()->exists()) {
            return $this->student();
        }
        return null;
    }

    /**
     * Determine if the user has verified their email address.
     *
     * @return bool
     */
    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Mark the given user's email as verified.
     *
     * @return bool
     */
    public function markEmailAsVerified(): bool
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }
}

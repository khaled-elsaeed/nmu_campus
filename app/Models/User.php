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
        'is_banned',
        'last_login',
        'last_password_changed_at',
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
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'force_change_password' => true,
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'         => 'datetime',
            'profile_complete'          => 'boolean',
            'force_change_password'     => 'boolean',
            'last_login'                => 'datetime',
            'last_password_changed_at'  => 'datetime',
            'password'                  => 'hashed',
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
     * Get the user's last password change formatted as a readable string.
     *
     * @return Attribute
     */
    protected function lastPasswordChanged(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) =>
                isset($attributes['last_password_changed_at']) && $attributes['last_password_changed_at']
                    ? formatDate($attributes['last_password_changed_at'])
                    : null
        );
    }

    /**
     * Get the user's profile type (student - staff) formatted as a readable string.
     *
     * @return Attribute
     */
    protected function profileType(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) =>
                $this->profile() ? class_basename($this->profile()->getModel()) : null
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
     * Get all bans associated with the user.
     *
     * @return HasMany
     */
    public function bans(): HasMany
    {
        return $this->hasMany(UserBan::class);
    }

    /**
     * OPTION 1: Keep as relationship but with proper constraints
     * Get the current active ban for the user.
     *
     * @return HasOne
     */
    public function currentBan(): HasOne
    {
        return $this->hasOne(UserBan::class)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at') // Permanent bans
                      ->orWhere('expires_at', '>', now()); // Non-expired temporary bans
            })
            ->latest('banned_at');
    }

    /**
     * OPTION 2: Use a method instead (RECOMMENDED)
     * Get the current active ban for the user.
     *
     * @return UserBan|null
     */
    public function getCurrentBan(): ?UserBan
    {
        return $this->bans()
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at') // Permanent bans
                      ->orWhere('expires_at', '>', now()); // Non-expired temporary bans
            })
            ->latest('banned_at')
            ->first();
    }

    /**
     * OPTION 3: Alternative method using the UserBan model's isActive() logic
     * Get the current active ban for the user.
     *
     * @return UserBan|null
     */
    public function getActiveBan(): ?UserBan
    {
        $activeBans = $this->bans()
            ->where('is_active', true)
            ->latest('banned_at')
            ->get();

        // Filter using the model's isActive() method to handle expiry logic
        return $activeBans->first(function ($ban) {
            return $ban->isActive();
        });
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
     * Check if the user's profile is complete.
     * Only applies to student profiles.
     *
     * @return bool
     */
    public function isProfileComplete(): bool
    {
        // Only students have profile completion requirements
        if ($this->student()->exists()) {
            return $this->student->isProfileComplete();
        }
        
        return true;
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

    /**
     * Determine if the user needs to change their password.
     *
     * @return bool
     */
    public function shouldForcePasswordChange(): bool
    {
        return (bool) $this->force_change_password;
    }

    /**
     * Mark the user as needing to change their password.
     *
     * @return bool
     */
    public function markForPasswordChange(): bool
    {
        return $this->forceFill([
            'force_change_password' => true,
        ])->save();
    }

    /**
     * Mark the user as no longer needing to change their password.
     *
     * @return bool
     */
    public function clearForcePasswordChange(): bool
    {
        return $this->forceFill([
            'force_change_password' => false,
        ])->save();
    }

    /**
     * Mark when the user's password was changed.
     *
     * @return bool
     */
    public function markPasswordChanged(): bool
    {
        return $this->forceFill([
            'last_password_changed_at' => $this->freshTimestamp(),
            'force_change_password' => false,
        ])->save();
    }

    /**
     * Check if the user is currently banned.
     * Updated to use the new method approach.
     *
     * @return bool
     */
    public function isBanned(): bool
    {
        $ban = $this->getCurrentBan(); // Using the method instead
        return $ban !== null;
    }

    /**
     * Legacy method - kept for backward compatibility
     * But now uses the new getCurrentBan() method
     *
     * @return UserBan|null
     */
    public function getActiveBanLegacy(): ?UserBan
    {
        return $this->getCurrentBan();
    }
}
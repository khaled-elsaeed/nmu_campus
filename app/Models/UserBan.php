<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Scope;

class UserBan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'national_id',
        'banned_by',
        'reason',
        'banned_at',
        'expires_at',
        'unbanned_at',
        'unbanned_by',
        'unban_reason',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'banned_at'   => 'datetime',
            'expires_at'  => 'datetime',
            'unbanned_at' => 'datetime',
            'is_active'   => 'boolean',
        ];
    }

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'is_active' => true,
    ];

    /**
     * Get the banned user.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who created the ban.
     *
     * @return BelongsTo
     */
    public function bannedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'banned_by');
    }

    /**
     * Get the user who removed the ban.
     *
     * @return BelongsTo
     */
    public function unbannedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'unbanned_by');
    }

    /**
     * Get the ban duration formatted as a readable string.
     *
     * @return Attribute
     */
    protected function bannedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) =>
                isset($attributes['banned_at']) && $attributes['banned_at']
                    ? formatDate($attributes['banned_at'])
                    : null
        );
    }

    /**
     * Get the ban expiry date formatted as a readable string.
     *
     * @return Attribute
     */
    protected function expiresAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) =>
                isset($attributes['expires_at']) && $attributes['expires_at']
                    ? formatDate($attributes['expires_at'])
                    : null
        );
    }

    /**
     * Get the unban date formatted as a readable string.
     *
     * @return Attribute
     */
    protected function unbannedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) =>
                isset($attributes['unbanned_at']) && $attributes['unbanned_at']
                    ? formatDate($attributes['unbanned_at'])
                    : null
        );
    }

    /**
     * Check if the ban is currently active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        // If expires_at is null, it's a permanent ban
        if (is_null($this->expires_at)) {
            return true;
        }

        // Check if temporary ban has expired
        return $this->expires_at->isFuture();
    }

    /**
     * Check if the ban is permanent.
     *
     * @return bool
     */
    public function isPermanent(): bool
    {
        return is_null($this->expires_at) && $this->is_active;
    }

    /**
     * Check if the ban is temporary.
     *
     * @return bool
     */
    public function isTemporary(): bool
    {
        return !is_null($this->expires_at) && $this->is_active;
    }

    /**
     * Check if the ban has expired.
     *
     * @return bool
     */
    public function hasExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }


    /**
     * Mark the ban as inactive (unban).
     *
     * @param int|null $unbannedBy
     * @param string|null $reason
     * @return bool
     */
    public function unban(int $unbannedBy = null, string $reason = null): bool
    {
        return $this->update([
            'is_active' => false,
            'unbanned_at' => now(),
            'unbanned_by' => $unbannedBy,
            'unban_reason' => $reason,
        ]);
    }

    /**
     * Check if a user is banned by their national ID.
     *
     * @param string $nationalId
     * @return bool
     */
    public static function isBannedByNationalId(string $nationalId): bool
    {
        return static::where('national_id', $nationalId)
            ->where('is_active', true)
            ->exists();
    }
    
   /**
     * Scope a query to only include active users.
     */
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', 1);
    }
}
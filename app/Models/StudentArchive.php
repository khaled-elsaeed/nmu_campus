<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

use App\Models\User;

class StudentArchive extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'external_id',
        'name_ar',
        'name_en',
        'email',
        'national_id',
        'mobile',
        'whatsapp',
        'birthdate',
        'gender',
        'nationality_name',
        'govern',
        'city',
        'street',
        'parent_name',
        'parent_mobile',
        'parent_email',
        'parent_country_name',
        'certificate_type_name',
        'cert_country_name',
        'cert_year_name',
        'brother',
        'brother_name',
        'brother_faculty',
        'brother_faculty_name',
        'brother_level',
        'candidated_faculty_name',
        'actual_score',
        'actual_percent',
        'is_deleted',
        'deleted_at',
        'synced_at',
        'last_updated_at',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = ['name', 'birthdate', 'brother'];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birthdate' => 'date:Y-m-d',
            'actual_score' => 'decimal:2',
            'actual_percent' => 'decimal:2',
            'is_deleted' => 'boolean',
            'deleted_at' => 'datetime',
            'synced_at' => 'datetime',
            'last_updated_at' => 'datetime',
        ];
    }

    /**
     * Get the student archive's name depending on locale.
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
     * Get the student archive's birthdate in Y-m-d format.
     *
     * @return Attribute
     */
    protected function birthdate(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => 
                $attributes['birthdate'] ? date('Y-m-d', strtotime($attributes['birthdate'])) : null
        );
    }

    /**
     * Get the student archive's gender as 'male' or 'female'.
     *
     * @return Attribute
     */
    protected function gender(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => match (strtolower($attributes['gender'] ?? '')) {
                'ذكر', 'male' => 'male',
                'أنثى', 'female' => 'female',
                default => null,
            }
        );
    }

    /**
     * Get the student archive's brother as boolean.
     *
     * @return Attribute
     */
    protected function brother(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => match (strtolower(trim($attributes['brother'] ?? ''))) {
                'نعم' => true,
                'لا' => false,
                default => null,
            }
        );
    }

    /**
     * Get the user for the student archive.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include active records.
     *
     * @param Builder $query
     * @return void
     */
    #[Scope]
    public function active(Builder $query): void
    {
        $query->where('is_deleted', false);
    }

    /**
     * Scope a query to only include recently updated records.
     *
     * @param Builder $query
     * @param int $hours
     */
    #[Scope]
    public function recentlyUpdated(Builder $query, int $hours = 24): void
    {
        $query->where('synced_at', '>=', now()->subHours($hours));
    }
} 
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Accommodation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'type',
        'description',
        'accommodatable_id',
        'accommodatable_type',
    ];

    /**
     * Get the parent accommodatable model (room or apartment).
     *
     * @return MorphTo
     */
    public function accommodatable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The equipment associated with the accommodation.
     *
     * @return BelongsToMany
     */
    public function equipment(): BelongsToMany
    {
        return $this->belongsToMany(Equipment::class, 'accommodation_equipment');
    }
}

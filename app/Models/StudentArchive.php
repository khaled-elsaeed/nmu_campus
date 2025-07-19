<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentArchive extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'whatsapp',
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
    ];

    /**
     * Get the user for the student archive.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
} 
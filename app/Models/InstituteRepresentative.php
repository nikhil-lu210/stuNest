<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstituteRepresentative extends Model
{
    protected $fillable = [
        'institute_id',
        'institute_location_id',
        'user_id',
    ];

    public function institute(): BelongsTo
    {
        return $this->belongsTo(Institute::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(InstituteLocation::class, 'institute_location_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

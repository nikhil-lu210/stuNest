<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstituteLocation extends Model
{
    protected $fillable = [
        'institute_id',
        'name',
        'address_line_1',
        'city',
        'postcode',
        'country',
        'is_primary',
        'sort_order',
    ];

    protected static function booted(): void
    {
        static::creating(function (InstituteLocation $location) {
            if (empty($location->country)) {
                $location->country = 'GB';
            }
        });
    }

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function institute(): BelongsTo
    {
        return $this->belongsTo(Institute::class);
    }

    public function representatives(): HasMany
    {
        return $this->hasMany(InstituteRepresentative::class);
    }
}

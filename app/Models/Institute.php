<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Institute extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email_code',
        'slug',
    ];

    protected static function booted(): void
    {
        static::creating(function (Institute $institute) {
            if (empty($institute->slug)) {
                $institute->slug = self::uniqueSlugFromName($institute->name);
            }
            $institute->email_code = self::normalizeEmailCode($institute->email_code);
        });

        static::updating(function (Institute $institute) {
            if ($institute->isDirty('email_code')) {
                $institute->email_code = self::normalizeEmailCode($institute->email_code);
            }
        });
    }

    public static function normalizeEmailCode(string $value): string
    {
        $value = trim($value);
        if ($value !== '' && ! str_starts_with($value, '@')) {
            $value = '@'.$value;
        }

        return $value;
    }

    public static function uniqueSlugFromName(string $name): string
    {
        $base = Str::slug($name) ?: 'institute';
        $slug = $base;
        $i = 1;
        while (static::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }

    public function locations(): HasMany
    {
        return $this->hasMany(InstituteLocation::class)->orderBy('sort_order')->orderBy('id');
    }

    public function representatives(): HasMany
    {
        return $this->hasMany(InstituteRepresentative::class);
    }

    public function getRouteKeyName(): string
    {
        return 'id';
    }
}

<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use JsonException;

/**
 * ISO 3166-1 alpha-2 countries from resources/data/countries.json (static list for student nationality).
 */
final class StudentCountryList
{
    /** @var list<array{code: string, name: string}>|null */
    private static ?array $cached = null;

    /**
     * @return list<array{code: string, name: string}>
     */
    public static function all(): array
    {
        if (self::$cached !== null) {
            return self::$cached;
        }

        $path = resource_path('data/countries.json');
        if (! File::exists($path)) {
            self::$cached = [];

            return self::$cached;
        }

        try {
            /** @var list<array{code: string, name: string}> $decoded */
            $decoded = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);
            self::$cached = $decoded;
        } catch (JsonException) {
            self::$cached = [];
        }

        return self::$cached;
    }

    /**
     * @return list<string>
     */
    public static function codes(): array
    {
        return array_values(array_unique(array_column(self::all(), 'code')));
    }

    public static function nameForCode(?string $code): ?string
    {
        if ($code === null || $code === '') {
            return null;
        }

        foreach (self::all() as $row) {
            if ($row['code'] === $code) {
                return $row['name'];
            }
        }

        return null;
    }
}

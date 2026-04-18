<?php

namespace App\Support;

/**
 * Opaque, non-guessable listing URLs: encodes the numeric property id with HMAC so raw IDs are not exposed.
 */
final class ListingPublicId
{
    public static function encode(int $id): string
    {
        if ($id < 1 || $id > 4294967295) {
            throw new \InvalidArgumentException('Invalid property id.');
        }

        $payload = pack('N', $id);
        $sig = substr(hash_hmac('sha256', $payload, static::key(), true), 0, 4);

        return rtrim(strtr(base64_encode($payload.$sig), '+/', '-_'), '=');
    }

    public static function decode(string $ref): ?int
    {
        $ref = trim($ref);
        if ($ref === '') {
            return null;
        }

        $raw = static::base64UrlDecode($ref);
        if ($raw === null || strlen($raw) !== 8) {
            return null;
        }

        $payload = substr($raw, 0, 4);
        $sig = substr($raw, 4, 4);
        $expected = substr(hash_hmac('sha256', $payload, static::key(), true), 0, 4);
        if (! hash_equals($expected, $sig)) {
            return null;
        }

        $unpacked = unpack('N', $payload);
        $id = (int) ($unpacked[1] ?? 0);

        return $id > 0 ? $id : null;
    }

    private static function key(): string
    {
        return (string) config('app.key');
    }

    private static function base64UrlDecode(string $data): ?string
    {
        $b64 = strtr($data, '-_', '+/');
        $pad = strlen($b64) % 4;
        if ($pad) {
            $b64 .= str_repeat('=', 4 - $pad);
        }
        $raw = base64_decode($b64, true);

        return $raw === false ? null : $raw;
    }
}

<?php

namespace App\Services;

/**
 * Normalizes Google Maps URLs and extracts coordinates when present in the link.
 * Resolves short share links (e.g. maps.app.goo.gl) by following redirects to the final Maps URL.
 */
class GoogleMapsUrlNormalizer
{
    /**
     * @return array{url: string, latitude: string|null, longitude: string|null}
     */
    public function normalize(string $url): array
    {
        $url = trim($url);
        if ($url === '') {
            return ['url' => $url, 'latitude' => null, 'longitude' => null];
        }

        $original = $url;

        if ($this->isGoogleMapsShortUrl($url)) {
            $expanded = $this->fetchEffectiveUrl($url);
            if ($expanded === null || ! $this->isGoogleMapsUrl($expanded)) {
                return ['url' => $original, 'latitude' => null, 'longitude' => null];
            }
            $url = $expanded;
        }

        if (! $this->isGoogleMapsUrl($url)) {
            return ['url' => $original, 'latitude' => null, 'longitude' => null];
        }

        $lat = null;
        $lng = null;

        // Precise place coordinates: !3dLAT!4dLNG (prefer over @ viewport)
        if (preg_match('/!3d(-?\d+\.?\d*)!4d(-?\d+\.?\d*)/', $url, $m)) {
            $lat = $m[1];
            $lng = $m[2];
        }

        // Use # delimiters: a / inside [...] would otherwise close a /-delimited pattern.
        if ($lat === null && preg_match('#@(-?\d+\.?\d*),(-?\d+\.?\d*)(?:,|$|[/?])#', $url, $m)) {
            $lat = $m[1];
            $lng = $m[2];
        }

        if ($lat === null && preg_match('#[?&](?:ll|q)=(-?\d+\.?\d*),(-?\d+\.?\d*)#', $url, $m)) {
            $lat = $m[1];
            $lng = $m[2];
        }

        $canonical = $url;
        if ($lat !== null && $lng !== null) {
            $canonical = 'https://www.google.com/maps?q=' . $lat . ',' . $lng;
        }

        return [
            'url' => $canonical,
            'latitude' => $lat,
            'longitude' => $lng,
        ];
    }

    private function isGoogleMapsUrl(string $url): bool
    {
        if (! str_contains(strtolower($url), 'google')) {
            return false;
        }

        return (bool) preg_match('#google\.[^/]+/maps#i', $url)
            || (bool) preg_match('#maps\.google\.#i', $url);
    }

    /**
     * Short share links that redirect to a full Google Maps URL.
     */
    private function isGoogleMapsShortUrl(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);
        if (! is_string($host) || $host === '') {
            return false;
        }

        $host = strtolower($host);

        return $host === 'maps.app.goo.gl'
            || $host === 'goo.gl'
            || $host === 'www.goo.gl'
            || str_ends_with($host, '.goo.gl')
            || $host === 'g.co';
    }

    /**
     * Follow redirects to the final URL (HTTPS only). Used to resolve short Maps links.
     */
    private function fetchEffectiveUrl(string $url): ?string
    {
        if (! $this->isGoogleMapsShortUrl($url)) {
            return $url;
        }

        $ch = curl_init($url);
        if ($ch === false) {
            return null;
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
            CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; StuNest/1.0)',
            CURLOPT_WRITEFUNCTION => static function ($ch, $data) {
                return strlen($data);
            },
        ]);

        curl_exec($ch);
        $effective = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $errno = curl_errno($ch);
        curl_close($ch);

        if ($errno !== 0 || ! is_string($effective) || $effective === '') {
            return null;
        }

        return $effective;
    }
}

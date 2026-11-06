<?php

namespace App\Services;

use App\Models\SiteSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PrayerTimesService
{
    /**
     * @return array{timings: list<array{key: string, name: string, azan: string, iqamah: string, icon: string, active: bool}>, meta: array{city: string, country: string, date: string, hijri: string, current: string|null}, error: string|null}
     */
    public function forToday(?string $city = null, ?string $country = null, ?string $timezone = null): array
    {
        $settings = SiteSetting::current();
        $city = $city ?: $settings->prayer_city;
        $country = $country ?: $settings->prayer_country;
        $timezone = $timezone ?: ($settings->prayer_timezone ?: config('app.timezone', 'UTC'));
        $cacheKey = sprintf('prayer_times.%s.%s.%s', $city, $country, now($timezone)->toDateString());

        try {
            $payload = Cache::remember(
                $cacheKey,
                now()->addHours(6),
                fn () => $this->fetchFromApi($city, $country, $settings)
            );

            $timings = $this->formatTimings($payload['timings'], $settings);
            $current = $this->resolveCurrentPrayer($payload['timings'], $timezone);

            return [
                'timings' => $this->markActive($timings, $current),
                'meta' => [
                    'city' => $city,
                    'country' => $country,
                    'date' => $payload['date']['readable'] ?? now($timezone)->format('d M Y'),
                    'hijri' => $payload['date']['hijri']['date'] ?? '',
                    'current' => $current,
                ],
                'error' => null,
            ];
        } catch (\Throwable $e) {
            Log::warning('Prayer times fetch failed: '.$e->getMessage());

            return [
                'timings' => $this->fallbackTimings(),
                'meta' => [
                    'city' => $city,
                    'country' => $country,
                    'date' => now($timezone)->format('d M Y'),
                    'hijri' => '',
                    'current' => null,
                ],
                'error' => 'Unable to refresh prayer times right now.',
            ];
        }
    }

    /**
     * @return array{timings: array<string, string>, date: array<string, mixed>}
     */
    protected function fetchFromApi(string $city, string $country, SiteSetting $settings): array
    {
        $response = Http::timeout(10)
            ->retry(2, 200)
            ->get('https://api.aladhan.com/v1/timingsByCity', [
                'city' => $city,
                'country' => $country,
                'method' => $settings->prayer_method,
                'school' => 1,
            ]);

        if (! $response->successful() || ($response->json('code') !== 200)) {
            throw new \RuntimeException('Aladhan API returned an error.');
        }

        $data = $response->json('data');

        return [
            'timings' => $data['timings'] ?? [],
            'date' => $data['date'] ?? [],
        ];
    }

    /**
     * Pick the closest configured city for a GPS point.
     *
     * @return array<string, mixed>|null
     */
    public function nearestConfiguredCity(float $lat, float $lng): ?array
    {
        $best = null;
        $bestDistance = INF;

        foreach (config('site.prayer.cities', []) as $row) {
            $cityLat = isset($row['lat']) ? (float) $row['lat'] : null;
            $cityLng = isset($row['lng']) ? (float) $row['lng'] : null;
            if ($cityLat === null || $cityLng === null || empty($row['city'])) {
                continue;
            }

            $distance = $this->haversineKm($lat, $lng, $cityLat, $cityLng);
            if ($distance < $bestDistance) {
                $bestDistance = $distance;
                $best = $row;
                $best['distance_km'] = round($distance, 1);
            }
        }

        return $best;
    }

    /**
     * Resolve a city from coordinates, preferring reverse-geocoded name when it matches catalog.
     *
     * @return array<string, mixed>|null
     */
    public function resolveCityFromCoordinates(float $lat, float $lng): ?array
    {
        $named = $this->matchCatalogFromReverseGeocode($lat, $lng);
        if ($named) {
            return $named;
        }

        return $this->nearestConfiguredCity($lat, $lng);
    }

    /**
     * Fallback when browser GPS is blocked: approximate via public IP.
     *
     * @return array<string, mixed>|null
     */
    public function resolveCityFromIp(?string $ip): ?array
    {
        if (! $ip || in_array($ip, ['127.0.0.1', '::1'], true)) {
            return null;
        }

        try {
            $response = Http::timeout(5)
                ->get("https://ipapi.co/{$ip}/json/");

            if (! $response->successful()) {
                return null;
            }

            $lat = $response->json('latitude');
            $lng = $response->json('longitude');
            $cityName = (string) ($response->json('city') ?? '');

            if ($cityName !== '') {
                $matched = $this->matchCatalogByName($cityName);
                if ($matched) {
                    return $matched;
                }
            }

            if (is_numeric($lat) && is_numeric($lng)) {
                return $this->resolveCityFromCoordinates((float) $lat, (float) $lng);
            }
        } catch (\Throwable $e) {
            Log::info('IP city lookup failed: '.$e->getMessage());
        }

        return null;
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function matchCatalogFromReverseGeocode(float $lat, float $lng): ?array
    {
        try {
            $response = Http::timeout(5)
                ->withHeaders([
                    'User-Agent' => 'JamiaPirShahaniPrayer/1.0 (local prayer locator)',
                    'Accept-Language' => 'en',
                ])
                ->get('https://nominatim.openstreetmap.org/reverse', [
                    'lat' => $lat,
                    'lon' => $lng,
                    'format' => 'jsonv2',
                    'addressdetails' => 1,
                    'zoom' => 12,
                ]);

            if (! $response->successful()) {
                return null;
            }

            $address = $response->json('address') ?? [];
            $candidates = array_filter([
                $address['city'] ?? null,
                $address['town'] ?? null,
                $address['municipality'] ?? null,
                $address['county'] ?? null,
                $address['state_district'] ?? null,
                $address['suburb'] ?? null,
            ]);

            foreach ($candidates as $candidate) {
                $matched = $this->matchCatalogByName((string) $candidate);
                if ($matched) {
                    return $matched;
                }
            }
        } catch (\Throwable $e) {
            Log::info('Reverse geocode failed: '.$e->getMessage());
        }

        return null;
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function matchCatalogByName(string $name): ?array
    {
        $needle = mb_strtolower(trim($name));
        if ($needle === '') {
            return null;
        }

        // Normalize common twin-city / district labels.
        $needle = str_replace([' district', ' division', ' tehsil'], '', $needle);

        foreach (config('site.prayer.cities', []) as $row) {
            $city = mb_strtolower((string) ($row['city'] ?? ''));
            $cityUr = (string) ($row['city_ur'] ?? '');
            if ($city !== '' && (str_contains($needle, $city) || str_contains($city, $needle))) {
                return $row;
            }
            if ($cityUr !== '' && str_contains($name, $cityUr)) {
                return $row;
            }
        }

        return null;
    }

    protected function haversineKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earth = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return $earth * (2 * atan2(sqrt($a), sqrt(1 - $a)));
    }

    /**
     * @param  array<string, string>  $apiTimings
     * @return list<array{key: string, name: string, azan: string, iqamah: string|null, icon: string, active: bool, show_iqamah: bool}>
     */
    protected function formatTimings(array $apiTimings, SiteSetting $settings): array
    {
        $map = [
            ['key' => 'Fajr', 'name' => 'فجر', 'api' => 'Fajr', 'icon' => 'fajr'],
            ['key' => 'Ishraq', 'name' => 'اشراق', 'api' => null, 'icon' => 'ishraq'],
            ['key' => 'Chasht', 'name' => 'چاشت', 'api' => null, 'icon' => 'chasht'],
            ['key' => 'Dhuhr', 'name' => 'ظہر', 'api' => 'Dhuhr', 'icon' => 'zuhr'],
            ['key' => 'Asr', 'name' => 'عصر', 'api' => 'Asr', 'icon' => 'asr'],
            ['key' => 'Maghrib', 'name' => 'مغرب', 'api' => 'Maghrib', 'icon' => 'maghrib'],
            ['key' => 'Isha', 'name' => 'عشاء', 'api' => 'Isha', 'icon' => 'isha'],
            ['key' => 'Jummah', 'name' => 'جمعہ', 'api' => 'Dhuhr', 'icon' => 'jummah'],
        ];

        $offsets = [
            'Fajr' => $settings->iqamah_fajr,
            'Dhuhr' => $settings->iqamah_dhuhr,
            'Asr' => $settings->iqamah_asr,
            'Maghrib' => $settings->iqamah_maghrib,
            'Isha' => $settings->iqamah_isha,
            'Jummah' => $settings->iqamah_jummah,
        ];

        $derived = $this->derivedSunnahTimings($apiTimings);

        $rows = [];
        foreach ($map as $row) {
            if (in_array($row['key'], ['Ishraq', 'Chasht'], true)) {
                $azanRaw = $derived[$row['key']] ?? null;
                if ($azanRaw === null) {
                    continue;
                }

                $rows[] = [
                    'key' => $row['key'],
                    'name' => $row['name'],
                    'azan' => $this->formatClock($azanRaw),
                    'iqamah' => null,
                    'icon' => $row['icon'],
                    'active' => false,
                    'show_iqamah' => false,
                ];

                continue;
            }

            $azanRaw = $apiTimings[$row['api']] ?? '--:--';
            $offset = (int) ($offsets[$row['key']] ?? 15);

            $rows[] = [
                'key' => $row['key'],
                'name' => $row['name'],
                'azan' => $this->formatClock($azanRaw),
                'iqamah' => $this->addMinutes($azanRaw, $offset),
                'icon' => $row['icon'],
                'active' => false,
                'show_iqamah' => true,
            ];
        }

        return $rows;
    }

    /**
     * Ishraq / Chasht are not Aladhan fields; derive from Sunrise (+ Dhuhr for Chasht).
     *
     * @param  array<string, string>  $apiTimings
     * @return array{Ishraq?: string, Chasht?: string}
     */
    protected function derivedSunnahTimings(array $apiTimings): array
    {
        $sunrise = $apiTimings['Sunrise'] ?? null;
        if (! is_string($sunrise) || strlen($sunrise) < 5) {
            return [];
        }

        // Ishraq: ~20 minutes after sunrise (common Pakistani/Hanafi practice).
        $ishraq = $this->shiftRawTime($sunrise, 20);
        $out = [];
        if ($ishraq !== null) {
            $out['Ishraq'] = $ishraq;
        }

        // Chasht (Duha): ~1/4 of the way from sunrise to Dhuhr.
        $dhuhr = $apiTimings['Dhuhr'] ?? null;
        if (is_string($dhuhr) && strlen($dhuhr) >= 5) {
            try {
                $sunriseAt = Carbon::createFromFormat('H:i', substr($sunrise, 0, 5));
                $dhuhrAt = Carbon::createFromFormat('H:i', substr($dhuhr, 0, 5));
                $minutes = (int) max(0, $sunriseAt->diffInMinutes($dhuhrAt, false));
                $chashtOffset = (int) max(45, (int) round($minutes / 4));
                $chasht = $this->shiftRawTime($sunrise, $chashtOffset);
                if ($chasht !== null) {
                    $out['Chasht'] = $chasht;
                }
            } catch (\Throwable) {
                // keep Ishraq only
            }
        }

        return $out;
    }

    protected function shiftRawTime(string $time, int $minutes): ?string
    {
        try {
            return Carbon::createFromFormat('H:i', substr($time, 0, 5))
                ->addMinutes($minutes)
                ->format('H:i');
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Lightweight schedule for the header countdown widget.
     *
     * @return array{timezone: string, prayers: list<array{key: string, name: string, at: string}>}
     */
    public function countdownSchedule(?string $city = null, ?string $country = null, ?string $timezone = null): array
    {
        $settings = SiteSetting::current();
        $timezone = $timezone ?: ($settings->prayer_timezone ?: config('app.timezone', 'UTC'));
        $payload = $this->forToday($city, $country, $timezone);

        $prayers = [];
        foreach ($payload['timings'] as $row) {
            // Jummah shares Dhuhr; Ishraq/Chasht are optional nawaafil windows.
            if (in_array($row['key'], ['Jummah', 'Ishraq', 'Chasht'], true)) {
                continue;
            }

            if ($row['azan'] === '--:--') {
                continue;
            }

            try {
                $at = Carbon::createFromFormat(
                    'Y-m-d g:i A',
                    now($timezone)->toDateString().' '.$row['azan'],
                    $timezone
                );
            } catch (\Throwable) {
                continue;
            }

            $prayers[] = [
                'key' => $row['key'],
                'name' => $row['name'],
                'at' => $at->toIso8601String(),
            ];
        }

        return [
            'timezone' => $timezone,
            'prayers' => $prayers,
        ];
    }

    protected function resolveCurrentPrayer(array $apiTimings, string $timezone): ?string
    {
        $now = now($timezone);

        try {
            $fajr = $this->todayAt($apiTimings['Fajr'] ?? '', $now);
            $dhuhr = $this->todayAt($apiTimings['Dhuhr'] ?? '', $now);
            $asr = $this->todayAt($apiTimings['Asr'] ?? '', $now);
            $maghrib = $this->todayAt($apiTimings['Maghrib'] ?? '', $now);
            $isha = $this->todayAt($apiTimings['Isha'] ?? '', $now);
        } catch (\Throwable) {
            return null;
        }

        if ($now->lt($fajr) || $now->gte($isha)) {
            return 'Isha';
        }

        if ($now->gte($maghrib)) {
            return 'Maghrib';
        }

        if ($now->gte($asr)) {
            return 'Asr';
        }

        if ($now->gte($dhuhr)) {
            return $now->isFriday() ? 'Jummah' : 'Dhuhr';
        }

        return 'Fajr';
    }

    /**
     * @param  list<array{key: string, name: string, azan: string, iqamah: string, icon: string, active: bool}>  $timings
     * @return list<array{key: string, name: string, azan: string, iqamah: string, icon: string, active: bool}>
     */
    protected function markActive(array $timings, ?string $current): array
    {
        return array_map(function (array $row) use ($current) {
            $row['active'] = $current !== null && $row['key'] === $current;

            return $row;
        }, $timings);
    }

    protected function todayAt(string $time, Carbon $now): Carbon
    {
        return Carbon::createFromFormat(
            'Y-m-d H:i',
            $now->toDateString().' '.substr($time, 0, 5),
            $now->getTimezone()
        );
    }

    /**
     * @return list<array{key: string, name: string, azan: string, iqamah: string|null, icon: string, active: bool, show_iqamah: bool}>
     */
    protected function fallbackTimings(): array
    {
        return [
            ['key' => 'Fajr', 'name' => 'فجر', 'azan' => '--:--', 'iqamah' => '--:--', 'icon' => 'fajr', 'active' => false, 'show_iqamah' => true],
            ['key' => 'Dhuhr', 'name' => 'ظہر', 'azan' => '--:--', 'iqamah' => '--:--', 'icon' => 'zuhr', 'active' => false, 'show_iqamah' => true],
            ['key' => 'Asr', 'name' => 'عصر', 'azan' => '--:--', 'iqamah' => '--:--', 'icon' => 'asr', 'active' => false, 'show_iqamah' => true],
            ['key' => 'Maghrib', 'name' => 'مغرب', 'azan' => '--:--', 'iqamah' => '--:--', 'icon' => 'maghrib', 'active' => false, 'show_iqamah' => true],
            ['key' => 'Isha', 'name' => 'عشاء', 'azan' => '--:--', 'iqamah' => '--:--', 'icon' => 'isha', 'active' => false, 'show_iqamah' => true],
            ['key' => 'Jummah', 'name' => 'جمعہ', 'azan' => '--:--', 'iqamah' => '--:--', 'icon' => 'jummah', 'active' => false, 'show_iqamah' => true],
        ];
    }

    protected function formatClock(string $time): string
    {
        try {
            return Carbon::createFromFormat('H:i', substr($time, 0, 5))->format('g:i A');
        } catch (\Throwable) {
            return $time;
        }
    }

    protected function addMinutes(string $time, int $minutes): string
    {
        try {
            return Carbon::createFromFormat('H:i', substr($time, 0, 5))
                ->addMinutes($minutes)
                ->format('g:i A');
        } catch (\Throwable) {
            return '--:--';
        }
    }
}

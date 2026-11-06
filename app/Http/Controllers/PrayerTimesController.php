<?php

namespace App\Http\Controllers;

use App\Services\PrayerTimesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PrayerTimesController extends Controller
{
    public function __invoke(Request $request, PrayerTimesService $prayerTimes): JsonResponse
    {
        $cities = collect(config('site.prayer.cities', []));
        $selected = null;
        $resolvedVia = 'city';

        if ($request->filled(['lat', 'lng'])) {
            $selected = $prayerTimes->resolveCityFromCoordinates(
                (float) $request->query('lat'),
                (float) $request->query('lng'),
            );
            $resolvedVia = 'gps';
        } elseif ($request->boolean('locate')) {
            $selected = $prayerTimes->resolveCityFromIp($request->ip());
            $resolvedVia = 'ip';
        } else {
            $city = (string) $request->query('city', '');
            $selected = $cities->first(
                fn (array $row) => strcasecmp($row['city'], $city) === 0
            );
        }

        if (! $selected) {
            return response()->json([
                'message' => 'City not found.',
            ], 422);
        }

        $timezone = $selected['timezone'] ?? config('site.prayer.timezone');

        $payload = $prayerTimes->forToday(
            $selected['city'],
            $selected['country'],
            $timezone,
        );

        return response()->json([
            'timings' => $payload['timings'],
            'meta' => array_merge($payload['meta'], [
                'city' => $selected['city'],
                'city_ur' => $selected['city_ur'],
                'country' => $selected['country'],
                'country_ur' => $selected['country_ur'],
                'timezone' => $timezone,
                'resolved_via' => $resolvedVia,
                'distance_km' => $selected['distance_km'] ?? null,
            ]),
            'countdown' => $prayerTimes->countdownSchedule(
                $selected['city'],
                $selected['country'],
                $timezone,
            ),
            'error' => $payload['error'],
        ]);
    }
}

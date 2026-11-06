@php
    $cities = $prayerCities ?? config('site.prayer.cities', []);
    $defaultCity = $site['prayer']['city'] ?? ($cities[0]['city'] ?? 'Chakwal');
    $selectedCity = collect($cities)->firstWhere('city', $defaultCity) ?? ($cities[0] ?? null);
    $cityUr = $selectedCity['city_ur'] ?? ($site['prayer']['city_ur'] ?? $defaultCity);
    $countryUr = $selectedCity['country_ur'] ?? ($site['prayer']['country_ur'] ?? 'پاکستان');
    $prayerCitiesJson = collect($cities)->map(function ($city) {
        return [
            'city' => $city['city'] ?? '',
            'city_ur' => $city['city_ur'] ?? '',
            'lat' => $city['lat'] ?? null,
            'lng' => $city['lng'] ?? null,
        ];
    })->values();
@endphp

@if (! empty($site['show_prayer_section'] ?? true))
<section id="prayer-timing"
         class="prayer-section relative overflow-hidden bg-[#f7f4ee] py-14 sm:py-16"
         data-prayer-section
         data-prayer-endpoint="{{ route('api.prayer-times') }}"
         data-default-city="{{ $defaultCity }}"
         data-prayer-cities='@json($prayerCitiesJson)'
         data-auto-locate="1">
    <div class="pointer-events-none absolute inset-0 opacity-[0.35]" aria-hidden="true"
         style="background-image: radial-gradient(circle at 12% 18%, rgba(11,107,94,0.12), transparent 34%), radial-gradient(circle at 88% 8%, rgba(232,164,92,0.14), transparent 28%);"></div>
    <div class="page-cage" aria-hidden="true"></div>

    <div class="page-shell relative z-[1]">
        <div class="prayer-panel reveal">
            <div class="prayer-panel__head">
                <div class="prayer-panel__intro min-w-0 flex-1">
                    <p class="prayer-panel__kicker">{{ $site['prayer_kicker'] ?: 'آج کے اوقات' }}</p>
                    <h2 class="font-display prayer-panel__title">{{ $site['prayer_title'] ?: 'اوقاتِ نماز' }}</h2>

                    <div class="prayer-meta" data-prayer-meta>
                        <span class="prayer-chip prayer-chip--teal" data-prayer-location>{{ $cityUr }}، {{ $countryUr }}</span>
                        @if (! empty($prayerMeta['date']))
                            <span class="prayer-chip" data-prayer-date-wrap>
                                <span data-prayer-date>{{ $prayerMeta['date'] }}</span>
                            </span>
                        @endif
                        @if (! empty($prayerMeta['hijri']))
                            <span class="prayer-chip" data-prayer-hijri-wrap>
                                <span data-prayer-hijri>{{ $prayerMeta['hijri'] }}</span> ہجری
                            </span>
                        @endif
                        <span class="prayer-chip">حنفی</span>
                    </div>

                    <p class="prayer-panel__error" data-prayer-error @if (! $prayerError) hidden @endif>
                        اوقاتِ نماز ابھی دستیاب نہیں۔
                    </p>
                </div>

                <div class="prayer-city" data-prayer-city-picker>
                    <div class="prayer-city__toolbar">
                        <span class="prayer-city__label">مقام تبدیل کریں</span>
                        <button
                            type="button"
                            class="prayer-city__locate"
                            data-prayer-locate
                            aria-label="میرا مقام استعمال کریں"
                            title="میرا مقام"
                        >
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="12" cy="12" r="3"/>
                                <path d="M12 2v2.5"/>
                                <path d="M12 19.5V22"/>
                                <path d="M2 12h2.5"/>
                                <path d="M19.5 12H22"/>
                                <path d="M12 8a4 4 0 1 1 0 8 4 4 0 0 1 0-8z"/>
                            </svg>
                            <span>میرا مقام</span>
                        </button>
                    </div>
                    <button
                        type="button"
                        class="prayer-city__btn"
                        data-prayer-city-btn
                        aria-haspopup="listbox"
                        aria-expanded="false"
                    >
                        <span class="prayer-city__pin" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 21s-6-5.3-6-10a6 6 0 1 1 12 0c0 4.7-6 10-6 10z"/>
                                <circle cx="12" cy="11" r="2.2"/>
                            </svg>
                        </span>
                        <span class="prayer-city__value" data-prayer-city-label>{{ $cityUr }}</span>
                        <span class="prayer-city__chevron" aria-hidden="true">
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06z" clip-rule="evenodd"/>
                            </svg>
                        </span>
                    </button>

                    <div class="prayer-city__menu" data-prayer-city-menu hidden role="listbox" aria-label="شہر منتخب کریں">
                        @foreach ($cities as $city)
                            <button
                                type="button"
                                class="prayer-city__option {{ ($city['city'] ?? '') === $defaultCity ? 'is-active' : '' }}"
                                role="option"
                                data-prayer-city-option
                                data-city="{{ $city['city'] }}"
                                data-city-ur="{{ $city['city_ur'] }}"
                                data-country-ur="{{ $city['country_ur'] }}"
                                aria-selected="{{ ($city['city'] ?? '') === $defaultCity ? 'true' : 'false' }}"
                            >
                                {{ $city['city_ur'] }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="prayer-grid" data-prayer-grid>
                @foreach ($prayerTimings as $prayer)
                    <article
                        data-prayer-card
                        data-prayer-key="{{ $prayer['key'] }}"
                        data-prayer-name="{{ $prayer['name'] }}"
                        @class([
                            'prayer-card',
                            'prayer-card--active' => $prayer['active'],
                        ])
                    >
                        <div class="prayer-card__top">
                            <div class="prayer-card__icon">
                                @include('partials.icons.prayer', ['icon' => $prayer['icon']])
                            </div>
                            <span class="prayer-card__live" data-prayer-badge @if (! $prayer['active']) hidden @endif>جاری</span>
                        </div>

                        <h3 class="prayer-card__name font-display">{{ $prayer['name'] }}</h3>
                        <p class="prayer-card__azan" dir="ltr" data-prayer-azan>{{ $prayer['azan'] }}</p>
                        @if ($prayer['show_iqamah'] ?? true)
                            <p class="prayer-card__iqamah">
                                <span>اقامت</span>
                                <span dir="ltr" data-prayer-iqamah>{{ $prayer['iqamah'] }}</span>
                            </p>
                        @else
                            <p class="prayer-card__iqamah prayer-card__iqamah--sunnah">نفلی</p>
                        @endif
                    </article>
                @endforeach
            </div>

            <div class="prayer-skeleton" data-prayer-skeleton aria-hidden="true">
                @foreach ($prayerTimings as $prayer)
                    <div class="prayer-skeleton__card">
                        <span class="prayer-skeleton__dot"></span>
                        <span class="prayer-skeleton__line"></span>
                        <span class="prayer-skeleton__block"></span>
                        <span class="prayer-skeleton__line prayer-skeleton__line--sm"></span>
                    </div>
                @endforeach
            </div>

            <p class="prayer-loading" data-prayer-loading hidden>اوقاتِ نماز لوڈ ہو رہے ہیں…</p>
        </div>
    </div>
</section>
@endif

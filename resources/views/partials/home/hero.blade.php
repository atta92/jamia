@php
    $hasHadith = ! empty($site['show_daily_hadith']) && $dailyHadith;
    $heroImage = ! empty($site['hero_image'])
        ? asset('storage/'.$site['hero_image'])
        : 'https://images.unsplash.com/photo-1564769625905-50e93615e769?auto=format&fit=crop&w=2000&q=80';
    $heroFaiza = ($faizaENazars ?? collect())->take(4)->values();
@endphp

<section class="hero-stage relative w-full bg-[#062e29]">
    <div class="absolute inset-0 overflow-hidden" aria-hidden="true">
        <img
            src="{{ $heroImage }}"
            alt=""
            class="hero-bg-zoom absolute inset-0 h-full w-full object-cover object-[center_30%]"
        >
        <div class="absolute inset-0 bg-[#062e29]/70"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-[#062e29]/95 via-[#0b6b5e]/75 to-[#062e29]/55"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-[#062e29] via-transparent to-[#062e29]/50"></div>
    </div>

    <div class="hero-stage__inner page-shell relative grid grid-cols-1 gap-6 lg:grid-cols-12 lg:items-start lg:gap-8" style="direction: ltr;">
        @if ($hasHadith)
            @php
                $sanadText = $dailyHadith->urdu_narrator ?: $dailyHadith->english_narrator;
                $hadithDate = now();
                $urduWeekdays = ['اتوار', 'پیر', 'منگل', 'بدھ', 'جمعرات', 'جمعہ', 'ہفتہ'];
                $urduMonths = ['جنوری', 'فروری', 'مارچ', 'اپریل', 'مئی', 'جون', 'جولائی', 'اگست', 'ستمبر', 'اکتوبر', 'نومبر', 'دسمبر'];
                $hadithWeekday = $urduWeekdays[(int) $hadithDate->dayOfWeek];
                $hadithMonth = $urduMonths[(int) $hadithDate->month - 1];
                $hadithDay = $hadithDate->day;
                $hadithYear = $hadithDate->year;
                $hadithHijri = ! empty($prayerMeta['hijri'] ?? null) ? $prayerMeta['hijri'] : null;
            @endphp
            <aside class="hero-stage__hadith order-2 lg:order-1 lg:col-span-5" style="direction: rtl;">
                <div id="daily-hadith-card" class="hero-hadith-card hero-bounce flex flex-col overflow-hidden rounded-[1.5rem] bg-[#F7F4EE] shadow-[0_20px_50px_rgba(0,0,0,0.35)]" style="--hero-bounce-delay: 0.18s">
                    {{-- Calendar tear-off header --}}
                    <div class="relative shrink-0 bg-[#0b6b5e] px-4 pb-3 pt-2.5 text-white sm:px-5">
                        <div class="mb-2 flex justify-center gap-8" aria-hidden="true">
                            <span class="h-2 w-2 rounded-full bg-[#062e29]/55 ring-2 ring-white/25"></span>
                            <span class="h-2 w-2 rounded-full bg-[#062e29]/55 ring-2 ring-white/25"></span>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="hero-hadith-date">
                                <span class="hero-hadith-date__month">{{ $hadithMonth }}</span>
                                <span class="hero-hadith-date__day">{{ $hadithDay }}</span>
                                <span class="hero-hadith-date__weekday">{{ $hadithWeekday }}</span>
                            </div>

                            <div class="min-w-0 flex-1 text-right">
                                <p class="font-display text-2xl font-bold leading-tight text-white">
                                    {{ $site['hero_hadith_heading'] ?: 'آج کی حدیثِ پاک' }}
                                </p>
                                @if ($hadithHijri)
                                    <p class="mt-1.5 text-sm text-white/80 sm:text-base">
                                        <span class="font-counter" dir="ltr">{{ $hadithHijri }}</span>
                                        ہجری
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div class="absolute inset-x-0 bottom-0 h-1 bg-[#e8a45c]" aria-hidden="true"></div>
                    </div>

                    <div class="mb-2 shrink-0 px-4 pt-3 sm:px-5">
                        @if ($dailyHadith->book)
                            <p class="text-center text-sm text-[#6b6860]">
                                {{ $dailyHadith->book->book_name }}
                                @if ($dailyHadith->hadith_number)
                                    · حدیث نمبر {{ $dailyHadith->hadith_number }}
                                @endif
                            </p>
                        @endif

                        <div id="hadith-tabs" class="mt-2.5 grid grid-cols-3 gap-1 rounded-xl bg-[#0b6b5e]/10 p-1" role="tablist" aria-label="حدیث کے حصے">
                            <button
                                type="button"
                                role="tab"
                                id="hadith-tab-urdu"
                                data-hadith-tab="urdu"
                                aria-selected="true"
                                aria-controls="hadith-panel-urdu"
                                class="hadith-tab rounded-lg px-2 py-1.5 text-xs font-semibold transition aria-selected:bg-[#0b6b5e] aria-selected:text-white aria-selected:shadow-sm text-[#0b6b5e] sm:text-sm"
                            >
                                اردو
                            </button>
                            <button
                                type="button"
                                role="tab"
                                id="hadith-tab-arabic"
                                data-hadith-tab="arabic"
                                aria-selected="false"
                                aria-controls="hadith-panel-arabic"
                                class="hadith-tab rounded-lg px-2 py-1.5 text-xs font-semibold transition aria-selected:bg-[#0b6b5e] aria-selected:text-white aria-selected:shadow-sm text-[#0b6b5e] sm:text-sm"
                            >
                                عربی
                            </button>
                            <button
                                type="button"
                                role="tab"
                                id="hadith-tab-sanad"
                                data-hadith-tab="sanad"
                                aria-selected="false"
                                aria-controls="hadith-panel-sanad"
                                class="hadith-tab rounded-lg px-2 py-1.5 text-xs font-semibold transition aria-selected:bg-[#0b6b5e] aria-selected:text-white aria-selected:shadow-sm text-[#0b6b5e] sm:text-sm"
                            >
                                سند
                            </button>
                        </div>
                    </div>

                    <button
                        type="button"
                        id="hadith-scroll-up"
                        class="mx-auto mb-1 flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[#0b6b5e]/10 text-[#0b6b5e] transition hover:bg-[#0b6b5e]/20 disabled:cursor-default disabled:opacity-30"
                        aria-label="اوپر دیکھیں"
                    >
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="m6 15 6-6 6 6"/>
                        </svg>
                    </button>

                    <div
                        id="hadith-scroll-body"
                        class="min-h-0 flex-1 overflow-y-auto px-4 pe-5 sm:px-5 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
                    >
                        <div
                            id="hadith-panel-urdu"
                            role="tabpanel"
                            aria-labelledby="hadith-tab-urdu"
                            data-hadith-panel="urdu"
                            class="hadith-panel"
                        >
                            @if ($dailyHadith->hadith_urdu)
                                <p class="font-urdu-bold text-base leading-[1.9] text-[#1c1b19] sm:text-lg">
                                    {{ $dailyHadith->hadith_urdu }}
                                </p>
                            @else
                                <p class="text-sm text-[#6b6860]">اردو متن دستیاب نہیں۔</p>
                            @endif
                        </div>

                        <div
                            id="hadith-panel-arabic"
                            role="tabpanel"
                            aria-labelledby="hadith-tab-arabic"
                            data-hadith-panel="arabic"
                            class="hadith-panel hidden"
                            hidden
                        >
                            @if ($dailyHadith->hadith_arabic)
                                <p class="text-base leading-[1.9] text-[#0b6b5e] sm:text-lg" dir="rtl" lang="ar">
                                    {{ $dailyHadith->hadith_arabic }}
                                </p>
                            @else
                                <p class="text-sm text-[#6b6860]">عربی متن دستیاب نہیں۔</p>
                            @endif
                        </div>

                        <div
                            id="hadith-panel-sanad"
                            role="tabpanel"
                            aria-labelledby="hadith-tab-sanad"
                            data-hadith-panel="sanad"
                            class="hadith-panel hidden"
                            hidden
                        >
                            @if ($sanadText)
                                <p class="font-urdu-bold text-base leading-[1.9] text-[#1c1b19] sm:text-lg">
                                    {{ $sanadText }}
                                </p>
                            @else
                                <p class="text-sm text-[#6b6860]">سند دستیاب نہیں۔</p>
                            @endif
                        </div>
                    </div>

                    <button
                        type="button"
                        id="hadith-scroll-down"
                        class="mx-auto mb-3 mt-1 flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[#0b6b5e]/10 text-[#0b6b5e] transition hover:bg-[#0b6b5e]/20 disabled:cursor-default disabled:opacity-30"
                        aria-label="نیچے دیکھیں"
                    >
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="m6 9 6 6 6-6"/>
                        </svg>
                    </button>
                </div>
            </aside>
        @endif

        <div class="hero-stage__copy order-1 text-white {{ $hasHadith ? 'lg:order-2 lg:col-span-7' : 'lg:col-span-10 lg:col-start-2' }}" style="direction: rtl;">
            <div class="hero-stage__copy-top">
                <p class="hero-bounce text-sm text-[#e8a45c] sm:text-base" style="--hero-bounce-delay: 0.05s">
                    {{ $site['tagline'] }}
                </p>

                <h1 class="mt-1.5">
                    @if (filled($site['hero_line'] ?? null))
                        <span class="hero-bounce font-display block text-lg text-[#e8a45c] sm:text-xl lg:text-3xl" style="--hero-bounce-delay: 0.12s">
                            {{ $site['hero_line'] }}
                        </span>
                    @endif
                    <span class="font-display hero-main-title hero-bounce mt-1 block font-bold leading-snug text-white drop-shadow-md" style="--hero-bounce-delay: 0.2s">
                        {{ $site['hero_title'] ?: (($site['name'] ?? '').' کا مختصر تعارف') }}
                    </span>
                    <span class="hero-location hero-bounce" style="--hero-bounce-delay: 0.32s">
                        <svg class="hero-location__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 21s-6-5.3-6-10a6 6 0 1 1 12 0c0 4.7-6 10-6 10z"/>
                            <circle cx="12" cy="11" r="2.2"/>
                        </svg>
                        <span class="hero-location__text">
                            {{ $site['location_label'] }}، {{ $site['prayer']['city_ur'] ?? $site['prayer']['city'] }}
                        </span>
                    </span>
                </h1>

                <p class="font-urdu hero-stage__lead hero-bounce mt-3 max-w-lg text-sm leading-relaxed text-white/80 sm:text-base sm:leading-loose lg:text-lg" style="--hero-bounce-delay: 0.42s">
                    {{ $site['short_description'] ?? '' }}
                </p>
            </div>

            @if ($heroFaiza->isNotEmpty())
                <div class="hero-faiza">
                    <p class="hero-faiza__kicker hero-bounce" style="--hero-bounce-delay: 0.52s">{{ $site['faiza_kicker'] ?: 'فیضانِ نظر' }}</p>
                    <div class="hero-faiza__grid">
                        @foreach ($heroFaiza as $person)
                            <a href="{{ route('about') }}#faizan-e-nazar" class="hero-faiza__card hero-bounce" style="--hero-bounce-delay: {{ 0.6 + ($loop->index * 0.1) }}s" aria-label="{{ $person->name }}">
                                <span class="hero-faiza__media">
                                    @if ($person->photoUrl())
                                        <img
                                            src="{{ $person->photoUrl() }}"
                                            alt="{{ $person->name }}"
                                            width="160"
                                            height="160"
                                            loading="eager"
                                        >
                                    @else
                                        <span class="hero-faiza__placeholder" aria-hidden="true"></span>
                                    @endif
                                    @if (filled($person->title))
                                        <span class="hero-faiza__overlay">
                                            <span class="hero-faiza__title">{{ $person->title }}</span>
                                        </span>
                                    @endif
                                </span>
                                <span class="hero-faiza__name font-display">{{ $person->name }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

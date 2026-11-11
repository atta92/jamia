@extends('layouts.public')

@section('title', 'ہمارے بارے میں - '.($site['name'] ?? config('site.name')))
@section('meta_description', 'دارالعلوم پیر شہانی کا تعارف، مشن، اساتذہ کرام اور فیضانِ نظر - عبادت، علم اور خدمت کا مرکز۔')

@section('content')
    @php
        $teachers = $teachers ?? collect();
        $faizaENazars = $faizaENazars ?? collect();
        $aboutHeading = filled($site['about_heading'] ?? null)
            ? $site['about_heading']
            : (($site['name'] ?? 'جامعہ').' کا تعارف');
        $aboutParagraphs = collect(preg_split("/\r\n|\n|\r/", (string) ($site['about_text'] ?? '')) ?: [])
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values();
    @endphp

    @include('partials.page-hero', [
        'kicker' => 'تعارف',
        'title' => 'ہمارے بارے میں',
        'lead' => $site['tagline'] ?? null,
        'crumbs' => [
            ['label' => 'صفحہ اول', 'url' => route('home')],
            ['label' => 'ہمارے بارے میں', 'url' => null],
        ],
    ])

    {{-- 1. تفصیل --}}
    <section class="about-block about-block--white page-fade-section py-14 sm:py-16" aria-label="تعارف">
        @include('partials.page-fade-icons')
        <div class="page-shell">
            <div class="about-block__header">
                <p class="about-block__kicker">تعارف</p>
                <h2 class="about-block__title font-display">{{ $aboutHeading }}</h2>
            </div>

            <div class="about-block__copy">
                @if ($aboutParagraphs->isNotEmpty())
                    <div class="about-block__text">
                        @foreach ($aboutParagraphs as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @endforeach
                    </div>
                @else
                    <p class="about-block__text about-block__text--muted">
                        {{ $site['short_description'] ?? 'جامعہ کے بارے میں تفصیل جلد شامل کی جائے گی۔' }}
                    </p>
                @endif

                <div class="about-block__actions">
                    <a href="{{ route('contact') }}" class="about-block__btn about-block__btn--solid">
                        رابطہ کریں
                    </a>
                    @if ($teachers->isNotEmpty())
                        <a href="#teachers" class="about-block__btn about-block__btn--ghost">
                            اساتذہ کرام
                        </a>
                    @endif
                    @if ($faizaENazars->isNotEmpty())
                        <a href="#faizan-e-nazar" class="about-block__btn about-block__btn--ghost">
                            فیضانِ نظر
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- 2. اساتذہ کرام --}}
    @if ($teachers->isNotEmpty())
        <section id="teachers" class="about-block about-block--cream page-fade-section py-14 sm:py-20" aria-label="اساتذہ کرام">
            @include('partials.page-fade-icons')
            <div class="page-shell">
                <div class="about-block__header">
                    <p class="about-block__kicker">تعلیم و تربیت</p>
                    <h2 class="about-block__title font-display">اساتذہ کرام</h2>
                    <p class="about-block__lead">
                        وہ اساتذہ جن کی علمی و اخلاقی رہنمائی سے طلبہ استفادہ کرتے ہیں۔
                    </p>
                </div>

                <div class="about-people__grid">
                    @foreach ($teachers as $teacher)
                        <article class="about-people__card">
                            <div class="about-people__media">
                                @if ($teacher->photoUrl())
                                    <img src="{{ $teacher->photoUrl() }}" alt="{{ $teacher->name }}" loading="lazy" width="400" height="400">
                                @else
                                    <div class="about-people__placeholder" aria-hidden="true"></div>
                                @endif
                                @if (filled($teacher->title))
                                    <span class="about-people__overlay">
                                        <span class="about-people__role">{{ $teacher->title }}</span>
                                    </span>
                                @endif
                            </div>
                            <h3 class="about-people__name font-display">{{ $teacher->name }}</h3>
                            @if (filled($teacher->description))
                                <p class="about-people__desc">{{ $teacher->description }}</p>
                            @endif
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- 3. فیضانِ نظر --}}
    @if ($faizaENazars->isNotEmpty())
        <section id="faizan-e-nazar" class="about-block about-block--white page-fade-section py-14 sm:py-20" aria-label="فیضانِ نظر">
            @include('partials.page-fade-icons')
            <div class="page-shell">
                <div class="about-block__header">
                    <p class="about-block__kicker">فیضانِ نظر</p>
                    <h2 class="about-block__title font-display">فیضانِ نظر</h2>
                    <p class="about-block__lead">
                        جن بزرگوں اور اہلِ علم کی نظرِ کرم سے جامعہ مستفید ہوا۔
                    </p>
                </div>

                <div class="about-people__grid">
                    @foreach ($faizaENazars as $person)
                        <article class="about-people__card">
                            <div class="about-people__media">
                                @if ($person->photoUrl())
                                    <img src="{{ $person->photoUrl() }}" alt="{{ $person->name }}" loading="lazy" width="400" height="400">
                                @else
                                    <div class="about-people__placeholder" aria-hidden="true"></div>
                                @endif
                                @if (filled($person->title))
                                    <span class="about-people__overlay">
                                        <span class="about-people__role">{{ $person->title }}</span>
                                    </span>
                                @endif
                            </div>
                            <h3 class="about-people__name font-display">{{ $person->name }}</h3>
                            @if (filled($person->description))
                                <p class="about-people__desc">{{ $person->description }}</p>
                            @endif
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection

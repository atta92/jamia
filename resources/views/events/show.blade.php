@extends('layouts.public')

@section('title', $event->title.' — '.($site['name'] ?? config('site.name')))

@section('content')
<section class="evlp-hero">
    <div class="evlp-hero__media" aria-hidden="true">
        @if ($event->coverUrl())
            <img src="{{ $event->coverUrl() }}" alt="">
        @endif
    </div>
    <div class="evlp-hero__overlay" aria-hidden="true"></div>

    <div class="page-shell evlp-hero__content">
        <span class="evlp-hero__badge">آنے والا پروگرام</span>

        <p class="evlp-hero__meta">
            @if ($event->dateLabel())
                <span>★ {{ $event->dateLabel() }}</span>
            @endif
            @if ($event->location)
                <span>★ {{ $event->location }}</span>
            @endif
        </p>

        <h1 class="font-display evlp-hero__title">{{ $event->title }}</h1>

        @if ($event->excerpt)
            <p class="evlp-hero__lead">{{ $event->excerpt }}</p>
        @endif

        <div class="evlp-hero__actions">
            <a href="#event-details" class="evlp-hero__btn">تفصیلات دیکھیں</a>
            <a href="{{ route('contact') }}" class="evlp-hero__btn evlp-hero__btn--ghost">رابطہ کریں</a>
        </div>
    </div>
</section>

<section class="evlp-overview page-fade-section" aria-label="خلاصہ پروگرام">
    @include('partials.page-fade-icons')
    <div class="page-shell">
        <div class="evlp-overview__grid">
            @if ($event->dateLabel() || $event->location)
                <div class="evlp-overview__item">
                    <h2>کب اور کہاں؟</h2>
                    <p>
                        @if ($event->dateLabel())
                            <strong>{{ $event->dateLabel() }}</strong>
                        @endif
                        @if ($event->dateLabel() && $event->location)
                            <br>
                        @endif
                        @if ($event->location)
                            {{ $event->location }}
                        @endif
                    </p>
                </div>
            @endif

            @if ($event->audience)
                <div class="evlp-overview__item">
                    <h2>کس کے لیے؟</h2>
                    <p>{{ $event->audience }}</p>
                </div>
            @endif

            @if ($event->highlights)
                <div class="evlp-overview__item">
                    <h2>کیا توقع رکھیں؟</h2>
                    <p>{{ $event->highlights }}</p>
                </div>
            @endif
        </div>
    </div>
</section>

<section id="event-details" class="evlp-about">
    <div class="page-shell evlp-about__layout">
        <div class="evlp-about__copy">
            <p class="evlp-about__kicker">اس پروگرام کے بارے میں</p>

            @if ($event->body)
                <div class="blog-content text-lg leading-loose text-ink">
                    {!! $event->body !!}
                </div>
            @else
                <p class="text-lg leading-loose text-muted">اس پروگرام کی مکمل تفصیلات جلد شامل کی جائیں گی۔</p>
            @endif

            <div class="evlp-about__actions">
                <a href="{{ route('contact') }}" class="evlp-hero__btn evlp-hero__btn--solid">مزید معلومات کے لیے رابطہ</a>
                <a href="{{ route('events.index') }}" class="evlp-about__back">
                    <span>تمام پروگرام</span>
                    @include('partials.icons.chevron-start')
                </a>
            </div>
        </div>

        <aside class="evlp-about__aside">
            <dl class="evlp-about__facts">
                @if ($event->dateLabel())
                    <div>
                        <dt>تاریخ</dt>
                        <dd>{{ $event->dateLabel() }}</dd>
                    </div>
                @endif
                @if ($event->location)
                    <div>
                        <dt>مقام</dt>
                        <dd>{{ $event->location }}</dd>
                    </div>
                @endif
                @if ($event->audience)
                    <div>
                        <dt>شرکاء</dt>
                        <dd>{{ $event->audience }}</dd>
                    </div>
                @endif
                @if ($event->highlights)
                    <div>
                        <dt>خصوصیات</dt>
                        <dd>{{ $event->highlights }}</dd>
                    </div>
                @endif
            </dl>
        </aside>
    </div>
</section>

@if ($related->isNotEmpty())
<section class="evlp-related">
    <div class="page-shell">
        <div class="home-events__head">
            <h2 class="font-display">دیگر پروگرام</h2>
            <span class="home-events__rule" aria-hidden="true"></span>
        </div>

        <div class="home-events__list">
            @foreach ($related as $item)
                <article class="home-event-card">
                    <div class="home-event-card__content">
                        <div class="home-event-card__meta">
                            @if ($item->dateLabel())
                                <span class="home-event-card__date">{{ $item->dateLabel() }}</span>
                            @endif
                            @if ($item->location)
                                <span class="home-event-card__loc">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M12 21s7-5.33 7-11a7 7 0 10-14 0c0 5.67 7 11 7 11z" stroke="currentColor" stroke-width="1.8"/>
                                        <circle cx="12" cy="10" r="2.5" stroke="currentColor" stroke-width="1.8"/>
                                    </svg>
                                    {{ $item->location }}
                                </span>
                            @endif
                        </div>
                        <h3 class="font-display">
                            <a href="{{ route('events.show', $item->slug) }}">{{ $item->title }}</a>
                        </h3>
                        <a class="home-event-card__cta" href="{{ route('events.show', $item->slug) }}">تفصیل دیکھیں</a>
                    </div>
                    <a href="{{ route('events.show', $item->slug) }}" class="home-event-card__media" tabindex="-1" aria-hidden="true">
                        @if ($item->coverUrl())
                            <img src="{{ $item->coverUrl() }}" alt="" loading="lazy">
                        @else
                            <div class="home-event-card__placeholder"></div>
                        @endif
                    </a>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection

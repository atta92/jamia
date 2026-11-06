@extends('layouts.public')

@section('title', 'پروگرامات — '.($site['name'] ?? config('site.name')))

@section('content')
<header class="page-hero">
    <div class="page-hero__cage" aria-hidden="true"></div>
    <div class="page-shell relative">
        <nav class="page-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">صفحہ اول</a>
            <span aria-hidden="true">/</span>
            <span>پروگرامات</span>
        </nav>
        <p class="page-kicker">پروگرامات</p>
        <h1 class="page-hero__title">آنے والے پروگرام</h1>
        <p class="page-hero__lead">جامعہ کے تعلیمی، دعوتی اور تربیتی پروگراموں کی معلومات۔</p>
    </div>
</header>

<section class="home-events home-events--page page-fade-section">
    @include('partials.page-fade-icons')
    <div class="page-shell">
        @if ($events->isEmpty())
            <div class="rounded-2xl border border-teal-800/10 bg-cream-50 px-6 py-16 text-center">
                <p class="font-display text-2xl font-bold text-teal-800">ابھی کوئی پروگرام درج نہیں</p>
                <p class="mt-3 text-base text-muted">جلد نئے پروگرام شامل کیے جائیں گے۔</p>
            </div>
        @else
            <div class="home-events__list">
                @foreach ($events as $event)
                    <article class="home-event-card">
                        <div class="home-event-card__content">
                            <div class="home-event-card__meta">
                                @if ($event->dateLabel())
                                    <span class="home-event-card__date">{{ $event->dateLabel() }}</span>
                                @endif
                                @if ($event->location)
                                    <span class="home-event-card__loc">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M12 21s7-5.33 7-11a7 7 0 10-14 0c0 5.67 7 11 7 11z" stroke="currentColor" stroke-width="1.8"/>
                                            <circle cx="12" cy="10" r="2.5" stroke="currentColor" stroke-width="1.8"/>
                                        </svg>
                                        {{ $event->location }}
                                    </span>
                                @endif
                            </div>

                            <h2 class="font-display">
                                <a href="{{ route('events.show', $event->slug) }}">{{ $event->title }}</a>
                            </h2>

                            @if ($event->excerpt)
                                <p>{{ \Illuminate\Support\Str::limit($event->excerpt, 160) }}</p>
                            @endif

                            <a class="home-event-card__cta" href="{{ route('events.show', $event->slug) }}">
                                تفصیل دیکھیں
                            </a>
                        </div>

                        <a href="{{ route('events.show', $event->slug) }}" class="home-event-card__media" tabindex="-1" aria-hidden="true">
                            @if ($event->coverUrl())
                                <img src="{{ $event->coverUrl() }}" alt="" loading="lazy">
                            @else
                                <div class="home-event-card__placeholder"></div>
                            @endif
                        </a>
                    </article>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $events->links() }}
            </div>
        @endif
    </div>
</section>
@endsection

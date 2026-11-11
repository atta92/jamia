@extends('layouts.public')

@php
    $pageTitle = $hifz->page_title ?: ($hifz->title ?: 'مدرسہ حفظ القرآن');
    $kicker = $hifz->kicker ?: 'حفظ القرآن';
    $lead = $hifz->page_lead ?: $hifz->excerpt;
    $body = trim((string) $hifz->body);
    $paragraphs = collect(preg_split("/\r\n\r\n|\n\n|\r\r/", $body) ?: [])
        ->map(fn ($p) => trim($p))
        ->filter()
        ->values();
    $counters = $hifz->counters();
    $aboutHeading = $hifz->about_heading ?: 'مدرسہ حفظ القرآن کے بارے میں';
    $activitiesHeading = $hifz->activities_heading ?: 'مدرسہ کی سرگرمیاں';
    $contactCta = $hifz->contact_cta_label ?: 'مزید معلومات کے لیے رابطہ';
@endphp

@section('title', $pageTitle.' - '.($site['name'] ?? config('site.name')))
@section('meta_description', \Illuminate\Support\Str::of($lead ?: ($site['short_description'] ?? ''))->stripTags()->squish()->limit(160)->toString() ?: 'مدرسہ حفظ القرآن - دارالعلوم پیر شہانی میں حفظِ قرآن کا خصوصی شعبہ۔')
@section('content')
@include('partials.page-hero', [
    'kicker' => $kicker,
    'title' => $pageTitle,
    'lead' => $lead,
    'crumbs' => [
        ['label' => 'صفحہ اول', 'url' => route('home')],
        ['label' => $pageTitle, 'url' => null],
    ],
])

<div class="hifz-page">
    <div class="page-cage" aria-hidden="true"></div>

    @if (count($counters))
    <section class="hifz-stats" aria-label="اعداد و شمار">
        <div class="page-shell">
            <div class="hifz-stats__grid">
                @foreach ($counters as $counter)
                    <div class="hifz-stats__item">
                        <span class="hifz-stats__value font-counter" dir="ltr">{{ $counter['value'] }}</span>
                        @if ($counter['label'] !== '')
                            <span class="hifz-stats__label">{{ $counter['label'] }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <section id="hifz-details" class="evlp-about">
        <div class="page-shell hifz-about">
            <div class="evlp-about__copy hifz-about__copy">
                <p class="evlp-about__kicker">{{ $aboutHeading }}</p>

                @forelse ($paragraphs as $paragraph)
                    <p class="text-lg leading-loose text-ink font-urdu">{{ $paragraph }}</p>
                @empty
                    <p class="text-lg leading-loose text-muted">تفصیلات جلد شامل کی جائیں گی۔</p>
                @endforelse

                <div class="evlp-about__actions hifz-about__actions">
                    <a href="{{ route('contact') }}" class="evlp-hero__btn evlp-hero__btn--solid">{{ $contactCta }}</a>
                    <a href="{{ route('home') }}#hifz" class="evlp-about__back">
                        <span>صفحہ اول</span>
                        @include('partials.icons.chevron-start')
                    </a>
                </div>
            </div>
        </div>
    </section>

    @if ($activities->isNotEmpty())
    @php
        $marqueeItems = $activities->concat($activities)->concat($activities)->values();
    @endphp
    <section class="evlp-related hifz-activities" aria-label="سرگرمیاں">
        <div class="page-shell">
            <div class="home-events__head">
                <h2 class="font-display">{{ $activitiesHeading }}</h2>
                <span class="home-events__rule" aria-hidden="true"></span>
            </div>
        </div>

        <div class="hifz-marquee" dir="ltr">
            <div class="hifz-marquee__track">
                @foreach ([1, 2] as $loopPass)
                    @foreach ($marqueeItems as $activity)
                        <figure class="hifz-marquee__item">
                            <span class="hifz-marquee__media">
                                <img
                                    src="{{ $activity->imageUrl() }}"
                                    alt="{{ $loopPass === 1 ? ($activity->title ?: 'سرگرمی') : '' }}"
                                    loading="eager"
                                >
                            </span>
                            @if ($activity->title || $activity->caption)
                                <figcaption class="hifz-marquee__caption">
                                    @if ($activity->title)
                                        <span class="hifz-marquee__title font-display">{{ $activity->title }}</span>
                                    @endif
                                    @if ($activity->caption)
                                        <span class="hifz-marquee__text">{{ $activity->caption }}</span>
                                    @endif
                                </figcaption>
                            @endif
                        </figure>
                    @endforeach
                @endforeach
            </div>
        </div>
    </section>
    @endif
</div>
@endsection

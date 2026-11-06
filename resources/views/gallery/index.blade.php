@extends('layouts.public')

@section('title', ($type === 'video' ? 'ویڈیو یادگار' : 'تصویری یادگار') . ' — ' . ($site['name'] ?? config('site.name')))

@section('content')
@php
    $isPhoto = $type === 'photo';
    $countLabel = $isPhoto
        ? $items->count().' تصاویر'
        : $items->count().' ویڈیوز';
    $pageTitle = $isPhoto ? 'تصویری یادگار' : 'ویڈیو یادگار';
@endphp

<header class="page-hero">
    <div class="page-hero__cage" aria-hidden="true"></div>

    <div class="page-shell relative">
        <nav class="page-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">صفحہ اول</a>
            <span aria-hidden="true">/</span>
            <span>{{ $pageTitle }}</span>
        </nav>

        <p class="page-kicker">یادگار</p>
        <h1 class="page-hero__title">{{ $pageTitle }}</h1>
        <p class="page-hero__lead">
            {{ $isPhoto
                ? 'جامعہ کی محافل، تعلیم، عبادت اور اجتماعات کے منتخب مناظر۔'
                : 'جامعہ کی ویڈیوز — تقریبات، دروس اور یادگار لمحات۔' }}
        </p>

        <div class="gallery-switch">
            <a href="{{ route('gallery.photos') }}" @class(['is-active' => $isPhoto])>تصاویر</a>
            <a href="{{ route('gallery.videos') }}" @class(['is-active' => ! $isPhoto])>ویڈیوز</a>
        </div>
    </div>
</header>

<section class="pgallery-section page-fade-section" data-photo-gallery>
    @include('partials.page-fade-icons')
    <div class="page-shell">
        @if ($items->isEmpty())
            <div class="rounded-2xl border border-teal-800/10 bg-cream-50 px-6 py-16 text-center shadow-sm">
                <p class="font-display text-2xl font-bold text-teal-800">ابھی کوئی آئٹم نہیں</p>
                <p class="mt-3 text-base text-muted">جلد نئی {{ $isPhoto ? 'تصاویر' : 'ویڈیوز' }} شامل کی جائیں گی۔</p>
            </div>
        @else
            <div class="pgallery-toolbar">
                <div class="pgallery-toolbar__meta">
                    <span class="pgallery-toolbar__count">{{ $countLabel }}</span>
                    <span class="pgallery-toolbar__sep" aria-hidden="true">·</span>
                    <span class="pgallery-toolbar__note">سال کے مطابق دیکھیں</span>
                </div>

                <div class="gallery-year-filters" data-gallery-year-filters role="tablist" aria-label="فلٹر">
                    <button type="button" class="gallery-chip is-active" data-year-filter="all" aria-pressed="true">سب</button>
                    @foreach ($years as $year)
                        <button type="button" class="gallery-chip" data-year-filter="{{ $year }}" aria-pressed="false">{{ $year }}</button>
                    @endforeach
                    @if ($hasHighlights)
                        <button type="button" class="gallery-chip" data-year-filter="highlights" aria-pressed="false">نمایاں</button>
                    @endif
                </div>
            </div>

            <div class="gallery-year-list">
                @foreach ($grouped as $yearKey => $groupItems)
                    @php
                        $blockTitle = $yearKey === 'highlights' ? 'نمایاں' : $yearKey;
                    @endphp
                    <div
                        class="gallery-year-block"
                        data-year-block="{{ $yearKey }}"
                    >
                        <div class="gallery-year-block__head">
                            <h2 class="gallery-year-block__title font-display">{{ $blockTitle }}</h2>
                            <span class="gallery-year-block__count">
                                {{ $groupItems->count() }} {{ $isPhoto ? 'تصاویر' : 'ویڈیوز' }}
                            </span>
                        </div>

                        <div class="pgallery">
                            @foreach ($groupItems as $item)
                                <button
                                    type="button"
                                    class="pgallery__item"
                                    data-gallery-item
                                    data-src="{{ $item->imageUrl() }}"
                                    data-title="{{ $item->title }}"
                                    data-type="{{ $item->type }}"
                                    data-video="{{ $item->video_url }}"
                                    data-category="{{ $item->category }}"
                                    data-year="{{ $item->year }}"
                                    aria-label="{{ $item->title ?: 'تصویر دیکھیں' }}"
                                >
                                    <img
                                        src="{{ $item->imageUrl() }}"
                                        alt="{{ $item->title ?: 'یادگار' }}"
                                        loading="lazy"
                                    >
                                    <span class="pgallery__veil" aria-hidden="true"></span>
                                    <span class="pgallery__meta">
                                        @if ($item->title)
                                            <span class="pgallery__title">{{ $item->title }}</span>
                                        @endif
                                        <span class="pgallery__open">View</span>
                                    </span>
                                    @if ($item->isVideo())
                                        <span class="gallery-showcase__play pgallery__play" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                                <path d="M8 5.14v13.72L19 12 8 5.14z"/>
                                            </svg>
                                        </span>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

<div id="gallery-lightbox" class="plightbox" hidden aria-hidden="true" data-lightbox>
    <button type="button" class="plightbox__backdrop" data-lightbox-close aria-label="بند کریں"></button>

    <button type="button" class="plightbox__close" data-lightbox-close aria-label="بند کریں">✕</button>

    <button type="button" class="plightbox__nav plightbox__nav--prev" data-lightbox-prev aria-label="پچھلی تصویر">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M15 6l-6 6 6 6" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"></path>
        </svg>
    </button>

    <button type="button" class="plightbox__nav plightbox__nav--next" data-lightbox-next aria-label="اگلی تصویر">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"></path>
        </svg>
    </button>

    <div class="plightbox__dialog" role="dialog" aria-modal="true" aria-label="تصویر دیکھیں">
        <figure class="plightbox__figure">
            <img src="" alt="" data-lightbox-image>
            <figcaption class="plightbox__caption">
                <strong data-lightbox-title></strong>
                <span data-lightbox-desc></span>
            </figcaption>
        </figure>
        <div class="plightbox__counter" data-lightbox-counter></div>
    </div>
</div>
@endsection

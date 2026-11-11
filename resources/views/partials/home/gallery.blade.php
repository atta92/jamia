@php
    $items = $galleryItems ?? collect();
    $years = $items->pluck('year')->filter()->unique()->sortDesc()->values();
@endphp

@if ((! empty($site['show_gallery_section'] ?? true)) && $items->isNotEmpty())
@php
    $galleryKicker = $site['gallery_kicker'] ?: 'یادگار';
    $galleryTitle = $site['gallery_title'] ?: 'علم، تربیت اور کامیابی کے مناظر';
    $galleryLead = $site['gallery_lead'] ?: 'جامعہ کی زندگی، تعلیم، عبادت اور اجتماعات کے منتخب مناظر - علم، ایمان اور خدمت کے سفر کی جھلک۔';
    $galleryCta = $site['gallery_cta'] ?: 'مزید دیکھیں';
@endphp
<section id="gallery" class="gallery-showcase relative overflow-hidden bg-cream-50 py-16 sm:py-20" aria-label="{{ $galleryKicker }}">
    <div class="page-cage" aria-hidden="true"></div>

    <div class="page-shell relative z-[1]">
        <div class="mx-auto max-w-3xl text-center reveal">
            <p class="text-base font-medium text-gold-600">{{ $galleryKicker }}</p>
            <h2 class="font-display mt-3 text-4xl font-bold leading-loose text-ink">
                {{ $galleryTitle }}
            </h2>
            @if ($galleryLead !== '')
                <p class="mx-auto mt-5 max-w-2xl text-lg leading-loose text-muted">
                    {{ $galleryLead }}
                </p>
            @endif
        </div>

        <div class="mt-8 flex flex-col items-center justify-between gap-4 sm:flex-row">
            <div id="gallery-filters" class="flex flex-wrap items-center justify-center gap-2" role="tablist" aria-label="گیلری سال">
                <button
                    type="button"
                    class="gallery-chip is-active"
                    data-gallery-filter="all"
                    aria-pressed="true"
                >
                    سب
                </button>
                @foreach ($years as $year)
                    <button
                        type="button"
                        class="gallery-chip"
                        data-gallery-filter="{{ $year }}"
                        aria-pressed="false"
                    >
                        {{ $year }}
                    </button>
                @endforeach
            </div>

            <a href="{{ route('gallery.photos') }}" class="inline-flex items-center gap-2 text-base font-semibold text-teal-800 transition hover:text-gold-600">
                <span>{{ $galleryCta }}</span>
                @include('partials.icons.chevron-start')
            </a>
        </div>
    </div>

    <div class="gallery-showcase__stage mt-10 reveal" data-gallery-stage>
        <div class="gallery-showcase__fade gallery-showcase__fade--start" aria-hidden="true"></div>
        <div class="gallery-showcase__fade gallery-showcase__fade--end" aria-hidden="true"></div>

        <div class="gallery-showcase__rail" data-gallery-rail>
            @foreach ($items as $index => $item)
                <button
                    type="button"
                    class="gallery-showcase__slide"
                    data-gallery-slide
                    data-year="{{ $item->year ?? '' }}"
                    data-type="{{ $item->type }}"
                    data-index="{{ $index }}"
                    aria-label="{{ $item->title ?: 'گیلری تصویر' }}"
                >
                    <img
                        src="{{ $item->imageUrl() }}"
                        alt="{{ $item->title ?: 'گیلری تصویر' }}"
                        width="640"
                        height="400"
                        loading="{{ $index < 3 ? 'eager' : 'lazy' }}"
                    >
                    @if ($item->isVideo())
                        <span class="gallery-showcase__play" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                <path d="M8 5.14v13.72L19 12 8 5.14z"/>
                            </svg>
                        </span>
                    @endif
                </button>
            @endforeach
        </div>

        <div class="gallery-showcase__nav">
            <button
                type="button"
                class="gallery-nav-btn"
                data-gallery-prev
                aria-label="پچھلی تصویر"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/>
                </svg>
            </button>
            <button
                type="button"
                class="gallery-nav-btn"
                data-gallery-next
                aria-label="اگلی تصویر"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/>
                </svg>
            </button>
        </div>
    </div>
</section>
@endif

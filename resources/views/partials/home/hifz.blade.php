@php
    /** @var \App\Models\HifzSetting|null $hifz */
    $hifz = $hifz ?? null;
    if (! $hifz || ! $hifz->show_on_home) {
        return;
    }

    $title = $hifz->title ?: 'مدرسہ حفظ القرآن';
    $kicker = $hifz->kicker ?: 'حفظ القرآن';
    $excerpt = trim((string) $hifz->excerpt);
    $image = $hifz->imageUrl();
    $cta = $hifz->cta_label ?: 'مزید دیکھیں';
    $counters = $hifz->counters();
@endphp

<section id="hifz" class="home-hifz" aria-label="{{ $title }}">
    <div class="page-cage" aria-hidden="true"></div>

    <div class="page-shell relative z-[1]">
        <div class="home-hifz__panel reveal">
            <div class="home-hifz__content">
                <p class="home-hifz__kicker">{{ $kicker }}</p>
                <h2 class="home-hifz__title font-display">{{ $title }}</h2>
                @if ($excerpt !== '')
                    <p class="home-hifz__excerpt font-urdu">{{ $excerpt }}</p>
                @endif

                @if (count($counters))
                    <div class="home-hifz__counters" role="list">
                        @foreach ($counters as $counter)
                            <div class="home-hifz__counter" role="listitem">
                                <span class="home-hifz__counter-value font-counter">{{ $counter['value'] }}</span>
                                @if ($counter['label'] !== '')
                                    <span class="home-hifz__counter-label">{{ $counter['label'] }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                <a href="{{ route('hifz.show') }}" class="home-hifz__cta">
                    <span>{{ $cta }}</span>
                    @include('partials.icons.chevron-start')
                </a>
            </div>

            <div class="home-hifz__media" aria-hidden="true">
                @if ($image)
                    <img src="{{ $image }}" alt="" loading="lazy">
                @endif
            </div>
        </div>
    </div>
</section>

@php
    $events = $upcomingEvents ?? collect();
@endphp

@if ($events->isNotEmpty())
<section id="events" class="home-programme" aria-label="آنے والے پروگرام" data-programme-slider>
    <div class="page-cage" aria-hidden="true"></div>

    <div class="page-shell relative z-[1]">
        <div class="home-programme__head reveal">
            <p class="home-programme__kicker">پروگرامات</p>
            <a href="{{ route('events.index') }}" class="home-programme__all">
                تمام دیکھیں
                <span aria-hidden="true">←</span>
            </a>
        </div>

        <div class="home-programme__viewport reveal">
            @foreach ($events as $index => $event)
                <article
                    class="home-programme__slide{{ $index === 0 ? ' is-active' : '' }}"
                    data-programme-slide
                    @if ($index !== 0) hidden @endif
                >
                    <div class="home-programme__card">
                        <div class="home-programme__body">
                            <p class="home-programme__badge">{{ $event->durationLabel() }}</p>

                            <h3 class="home-programme__title font-display">
                                <a href="{{ route('events.show', $event->slug) }}">{{ $event->title }}</a>
                            </h3>

                            @if ($event->audience)
                                <p class="home-programme__meta font-urdu">{{ $event->audience }}</p>
                            @elseif ($event->location || $event->dateLabel())
                                <p class="home-programme__meta font-urdu">
                                    {{ collect([$event->location ?: null, $event->dateLabel() ?: null])->filter()->implode(' · ') }}
                                </p>
                            @endif

                            @if ($event->excerpt)
                                <p class="home-programme__excerpt font-urdu">
                                    {{ \Illuminate\Support\Str::limit($event->excerpt, 160) }}
                                </p>
                            @endif

                            <a href="{{ route('events.show', $event->slug) }}" class="home-programme__cta">
                                تفصیل دیکھیں
                                <span aria-hidden="true">←</span>
                            </a>
                        </div>

                        <a href="{{ route('events.show', $event->slug) }}" class="home-programme__media" tabindex="-1" aria-hidden="true">
                            @if ($event->coverUrl())
                                <img src="{{ $event->coverUrl() }}" alt="" loading="lazy">
                            @else
                                <span class="home-programme__placeholder"></span>
                            @endif
                        </a>
                    </div>
                </article>
            @endforeach
        </div>

        @if ($events->count() > 1)
            <div class="home-programme__dots" role="tablist" aria-label="پروگرام سلائیڈز">
                @foreach ($events as $index => $event)
                    <button
                        type="button"
                        class="home-programme__dot{{ $index === 0 ? ' is-active' : '' }}"
                        data-programme-dot="{{ $index }}"
                        role="tab"
                        aria-label="پروگرام {{ $index + 1 }}"
                        aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                    ></button>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endif

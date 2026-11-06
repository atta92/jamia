@php
    $people = $faizaENazars ?? collect();
    // Duplicate so the snake loop stays filled even with few people.
    $snakePeople = $people->isEmpty()
        ? collect()
        : $people->concat($people)->concat($people)->concat($people)->values();
@endphp

@if ($people->isNotEmpty())
<section id="faizan-e-nazar" class="faiza-nazar relative overflow-hidden bg-white py-14 sm:py-20" aria-label="فیضانِ نظر">
    <div class="page-cage" aria-hidden="true"></div>

    <div class="page-shell relative z-[1]">
        <div class="mx-auto max-w-3xl text-center reveal">
            <p class="text-base font-medium text-gold-600">فیضانِ نظر</p>
            <h2 class="font-display mt-3 text-4xl font-bold leading-loose text-ink">
                فیضانِ نظر
            </h2>
            <p class="mx-auto mt-5 max-w-2xl text-lg leading-loose text-muted">
                جن بزرگوں اور اہلِ علم کی نظرِ کرم سے جامعہ مستفید ہوا۔
            </p>
        </div>
    </div>

    <div class="faiza-snake mt-10" dir="ltr" aria-hidden="true">
        <div class="faiza-snake__row faiza-snake__row--forward">
            <div class="faiza-snake__track">
                @foreach ([1, 2] as $loopPass)
                    @foreach ($snakePeople as $person)
                        <figure class="faiza-snake__item">
                            @if ($person->photoUrl())
                                <img src="{{ $person->photoUrl() }}" alt="" loading="eager" width="160" height="160">
                            @else
                                <span class="faiza-snake__placeholder"></span>
                            @endif
                            <figcaption class="faiza-snake__caption">
                                @if (filled($person->title))
                                    <span class="faiza-snake__role">{{ $person->title }}</span>
                                @endif
                                <span class="faiza-snake__name font-display">{{ $person->name }}</span>
                            </figcaption>
                        </figure>
                    @endforeach
                @endforeach
            </div>
        </div>

        <div class="faiza-snake__row faiza-snake__row--reverse mt-3">
            <div class="faiza-snake__track">
                @foreach ([1, 2] as $loopPass)
                    @foreach ($snakePeople->reverse() as $person)
                        <figure class="faiza-snake__item">
                            @if ($person->photoUrl())
                                <img src="{{ $person->photoUrl() }}" alt="" loading="eager" width="160" height="160">
                            @else
                                <span class="faiza-snake__placeholder"></span>
                            @endif
                            <figcaption class="faiza-snake__caption">
                                @if (filled($person->title))
                                    <span class="faiza-snake__role">{{ $person->title }}</span>
                                @endif
                                <span class="faiza-snake__name font-display">{{ $person->name }}</span>
                            </figcaption>
                        </figure>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>

    {{-- Accessible static list for screen readers / SEO --}}
    <ul class="sr-only">
        @foreach ($people as $person)
            <li>
                {{ $person->name }}
                @if (filled($person->title)) — {{ $person->title }} @endif
                @if (filled($person->description)) — {{ $person->description }} @endif
            </li>
        @endforeach
    </ul>
</section>
@endif

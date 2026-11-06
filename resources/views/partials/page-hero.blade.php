@php
    /** @var string $title */
    /** @var string|null $kicker */
    /** @var string|null $lead */
    /** @var array<int, array{label: string, url: string}> $crumbs */
    $crumbs = $crumbs ?? [];
    $kicker = $kicker ?? null;
    $lead = $lead ?? null;
@endphp

<header class="page-hero">
    <div class="page-hero__cage" aria-hidden="true"></div>

    <div class="page-shell relative">
        @if (count($crumbs))
            <nav class="page-breadcrumb" aria-label="Breadcrumb">
                @foreach ($crumbs as $index => $crumb)
                    @if ($index > 0)
                        <span aria-hidden="true">/</span>
                    @endif
                    @if (! empty($crumb['url']))
                        <a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
                    @else
                        <span>{{ $crumb['label'] }}</span>
                    @endif
                @endforeach
            </nav>
        @endif

        @if ($kicker)
            <p class="page-kicker">{{ $kicker }}</p>
        @endif

        <h1 class="page-hero__title">{{ $title }}</h1>

        @if ($lead)
            <p class="page-hero__lead">{{ $lead }}</p>
        @endif

        @isset($slot)
            <div class="page-hero__actions">
                {{ $slot }}
            </div>
        @endisset

        @if (! empty($actions ?? null))
            <div class="page-hero__actions">
                {!! $actions !!}
            </div>
        @endif
    </div>
</header>

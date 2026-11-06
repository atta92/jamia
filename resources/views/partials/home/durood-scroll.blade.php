@php
    $showDurood = (bool) ($site['show_durood_section'] ?? true);
    $duroodKicker = trim((string) ($site['durood_kicker'] ?? 'درود پڑھیے'));
    $duroodText = trim((string) ($site['durood_text'] ?? 'صلّی اللہ علیہ وآلہ وسلّم'));
    $duroodFazilat = trim((string) ($site['durood_fazilat'] ?? ''));
@endphp

@if ($showDurood && ($duroodKicker !== '' || $duroodText !== '' || $duroodFazilat !== ''))
    <section class="durood-scroll" data-durood-scroll aria-label="درود شریف">
        <div class="durood-scroll__sticky">
            <div class="durood-scroll__pattern" aria-hidden="true"></div>

            @if ($duroodKicker !== '')
                <p class="durood-scroll__kicker">{{ $duroodKicker }}</p>
            @endif

            @if ($duroodText !== '')
                <div class="durood-scroll__viewport">
                    <p class="durood-scroll__text font-display" data-durood-scroll-text dir="rtl">
                        {{ $duroodText }}
                    </p>
                </div>
            @endif

            @if ($duroodFazilat !== '')
                <div class="durood-scroll__bottom">
                    <p class="durood-scroll__fazilat font-urdu">{{ $duroodFazilat }}</p>
                </div>
            @endif
        </div>
    </section>
@endif

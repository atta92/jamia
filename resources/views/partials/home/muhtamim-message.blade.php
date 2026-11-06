@php
    $show = ! empty($site['show_muhtamim_message']);
    $message = trim((string) ($site['muhtamim_message'] ?? ''));
    $heading = $site['muhtamim_heading'] ?: 'مہتمم صاحب کا پیغام';
    $name = trim((string) ($site['muhtamim_name'] ?? ''));
    $role = trim((string) ($site['muhtamim_role'] ?? ''));
    $photo = ! empty($site['muhtamim_photo'])
        ? asset('storage/'.$site['muhtamim_photo'])
        : null;
    $facebook = trim((string) ($site['muhtamim_facebook_url'] ?? ''));
    $whatsappRaw = trim((string) ($site['muhtamim_whatsapp'] ?? $site['phone'] ?? ''));
    $whatsapp = null;
    if ($whatsappRaw !== '') {
        $whatsapp = str_starts_with($whatsappRaw, 'http')
            ? $whatsappRaw
            : 'https://wa.me/'.preg_replace('/\D+/', '', $whatsappRaw);
    }
    $paragraphs = collect(preg_split("/\r\n|\n|\r/", $message) ?: [])
        ->map(fn ($line) => trim($line))
        ->filter()
        ->values();
@endphp

@if ($show && $message !== '')
<section id="muhtamim-message" class="ceo-message" aria-label="{{ $heading }}">
    <div class="page-cage" aria-hidden="true"></div>

    <div class="page-shell relative z-[1]">
        <div class="ceo-message__grid">
            <div class="ceo-message__body reveal">
                <h2 class="ceo-message__title font-display">
                    <span class="ceo-message__title-text" data-highlight>{{ $heading }}</span>
                </h2>

                <div class="ceo-message__text font-urdu">
                    @foreach ($paragraphs as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </div>

                <div class="ceo-message__footer">
                    @if ($name !== '')
                        <div class="ceo-message__signoff">
                            <strong class="font-display">{{ $name }}</strong>
                            @if ($role !== '')
                                <span>{{ $role }}</span>
                            @endif
                        </div>
                    @endif

                    @if ($facebook !== '' || $whatsapp)
                        <div class="ceo-message__socials">
                            @if ($facebook !== '')
                                <a class="ceo-message__social ceo-message__social--facebook"
                                   href="{{ $facebook }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   aria-label="فیس بک">
                                    <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
                                        <path fill="currentColor" d="M22 12.07C22 6.48 17.52 2 11.93 2S1.86 6.48 1.86 12.07c0 5.02 3.66 9.18 8.44 9.93v-7.03H7.9v-2.9h2.4V9.84c0-2.37 1.4-3.69 3.56-3.69 1.03 0 2.12.19 2.12.19v2.34h-1.2c-1.18 0-1.55.74-1.55 1.49v1.79h2.64l-.42 2.9h-2.22V22c4.78-.75 8.44-4.91 8.44-9.93z"/>
                                    </svg>
                                </a>
                            @endif
                            @if ($whatsapp)
                                <a class="ceo-message__social ceo-message__social--whatsapp"
                                   href="{{ $whatsapp }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   aria-label="واٹس ایپ">
                                    <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
                                        <path fill="currentColor" d="M19.05 4.91A9.82 9.82 0 0 0 12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.87 9.87 0 0 0 4.79 1.22h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.91-7.02zm-7.01 15.24h-.01a8.2 8.2 0 0 1-4.18-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.18 8.18 0 0 1-1.26-4.38c0-4.53 3.69-8.21 8.23-8.21 2.2 0 4.26.86 5.81 2.41a8.16 8.16 0 0 1 2.41 5.81c0 4.53-3.69 8.23-8.21 8.23zm4.51-6.15c-.25-.12-1.46-.72-1.69-.8-.23-.09-.39-.12-.56.12-.16.25-.64.8-.78.96-.14.17-.29.19-.54.06-.25-.12-1.05-.39-2-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.38.11-.51.11-.11.25-.29.37-.43.12-.14.16-.25.25-.41.08-.17.04-.31-.02-.43-.06-.12-.56-1.35-.77-1.85-.2-.48-.41-.42-.56-.42h-.48c-.17 0-.43.06-.66.31-.23.25-.87.85-.87 2.07 0 1.22.89 2.4 1.01 2.56.12.17 1.75 2.67 4.23 3.74 1.48.64 2.07.7 2.81.59.43-.07 1.46-.6 1.66-1.18.21-.58.21-1.07.14-1.18-.06-.1-.23-.17-.48-.29z"/>
                                    </svg>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <div class="ceo-message__media reveal" style="--reveal-delay: 0.12s">
                @if ($photo)
                    <img src="{{ $photo }}" alt="{{ $name !== '' ? $name : $heading }}" loading="lazy">
                @else
                    <div class="ceo-message__placeholder" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm0 2c-4.42 0-8 2.24-8 5v1h16v-1c0-2.76-3.58-5-8-5Z"/>
                        </svg>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endif

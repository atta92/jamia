@php
    $waRaw = trim((string) ($site['muhtamim_whatsapp'] ?? $site['phone'] ?? ''));
    $waHref = null;
    if ($waRaw !== '') {
        $waHref = str_starts_with($waRaw, 'http')
            ? $waRaw
            : 'https://wa.me/'.preg_replace('/\D+/', '', $waRaw);
    }
@endphp

<div class="float-actions" data-float-actions>
    @if ($waHref)
        <a href="{{ $waHref }}"
           class="float-actions__btn float-actions__btn--wa"
           target="_blank"
           rel="noopener noreferrer"
           aria-label="واٹس ایپ">
            <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
                <path fill="currentColor" d="M20.5 3.5A11 11 0 0 0 3.2 17.7L2 22l4.4-1.1A11 11 0 1 0 20.5 3.5zM12 20.2a9.1 9.1 0 0 1-4.6-1.3l-.3-.2-2.6.7.7-2.5-.2-.3A9.1 9.1 0 1 1 12 20.2zm5-6.8c-.3-.1-1.6-.8-1.9-.9s-.4-.1-.6.1-.7.9-.8 1-.3.2-.6 0a7.4 7.4 0 0 1-2.2-1.4 8.2 8.2 0 0 1-1.5-1.9c-.2-.3 0-.4.1-.6l.4-.5c.1-.2.1-.3 0-.5l-.9-2.1c-.2-.5-.5-.5-.6-.5h-.5c-.2 0-.5.1-.7.3s-1 1-1 2.4 1 2.8 1.2 3c.1.2 2 3.1 4.9 4.3 1.8.8 2.2.8 3 .7.5-.1 1.6-.7 1.8-1.3s.2-1.2.2-1.3-.2-.2-.5-.3z"/>
            </svg>
        </a>
    @endif

    <button type="button"
            class="float-actions__btn float-actions__btn--top"
            data-scroll-top
            aria-label="اوپر جائیں"
            hidden>
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5M5 12l7-7 7 7"/>
        </svg>
    </button>
</div>

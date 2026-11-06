@php
    $phone = trim((string) ($site['phone'] ?? ''));
    $email = trim((string) ($site['email'] ?? ''));
    $address = trim((string) ($site['address'] ?? ''));
    if ($address === '') {
        $address = trim(implode('، ', array_filter([
            $site['location_label'] ?? null,
            ($site['prayer']['city_ur'] ?? $site['prayer']['city'] ?? null),
            $site['prayer']['country_ur'] ?? null,
        ])));
    }
    $phoneHref = $phone !== '' ? 'tel:'.preg_replace('/\s+/', '', $phone) : null;
    $whatsappHref = $phone !== '' ? 'https://wa.me/'.preg_replace('/\D+/', '', $phone) : null;
@endphp

<footer id="contact" class="site-footer">
    <div class="page-cage page-cage--light" aria-hidden="true"></div>

    <div class="site-footer__contact">
        <div class="page-shell">
            <div class="site-footer__contact-grid">
                <div class="site-footer__contact-item">
                    <span class="site-footer__contact-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.5 5.5c0-.8.7-1.5 1.5-1.5h3.1c.6 0 1.1.4 1.3 1l1 3.1c.2.5 0 1.1-.4 1.4l-1.5 1.2a13 13 0 0 0 5.8 5.8l1.2-1.5c.3-.4.9-.6 1.4-.4l3.1 1c.6.2 1 .7 1 1.3V20c0 .8-.7 1.5-1.5 1.5C9.9 21.5 2.5 14.1 2.5 5.5z"/>
                        </svg>
                    </span>
                    <div class="site-footer__contact-text">
                        <p class="site-footer__contact-label">فون</p>
                        @if ($phoneHref)
                            <a href="{{ $phoneHref }}" class="site-footer__contact-value" dir="ltr">{{ $phone }}</a>
                        @else
                            <p class="site-footer__contact-value">ابھی دستیاب نہیں</p>
                        @endif
                    </div>
                </div>

                <div class="site-footer__contact-item">
                    <span class="site-footer__contact-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <rect x="3" y="5" width="18" height="14" rx="2"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4 7 8 6 8-6"/>
                        </svg>
                    </span>
                    <div class="site-footer__contact-text">
                        <p class="site-footer__contact-label">ای میل</p>
                        @if ($email !== '')
                            <a href="mailto:{{ $email }}" class="site-footer__contact-value" dir="ltr">{{ $email }}</a>
                        @else
                            <p class="site-footer__contact-value">ابھی دستیاب نہیں</p>
                        @endif
                    </div>
                </div>

                <div class="site-footer__contact-item">
                    <span class="site-footer__contact-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-5.4 7-11a7 7 0 1 0-14 0c0 5.6 7 11 7 11z"/>
                            <circle cx="12" cy="10" r="2.5"/>
                        </svg>
                    </span>
                    <div class="site-footer__contact-text">
                        <p class="site-footer__contact-label">پتہ</p>
                        @if ($address !== '')
                            <p class="site-footer__contact-value" dir="ltr">{{ $address }}</p>
                        @else
                            <p class="site-footer__contact-value">ابھی دستیاب نہیں</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="site-footer__main">
        <div class="page-shell site-footer__main-grid">
            <div class="site-footer__brand">
                @if (! empty($site['logo']))
                    <img
                        src="{{ asset('storage/'.$site['logo']) }}"
                        alt="{{ $site['name'] ?? config('site.name') }}"
                        class="site-footer__logo"
                    >
                @endif
                <p class="site-footer__name site-brand--logo">{{ $site['name'] ?? config('site.name') }}</p>
                <p class="site-footer__desc font-urdu">
                    {{ $site['short_description'] ?? config('site.short_description') }}
                </p>

                @if ($whatsappHref || $phoneHref)
                    <div class="site-footer__social">
                        @if ($whatsappHref)
                            <a href="{{ $whatsappHref }}" target="_blank" rel="noopener noreferrer" aria-label="واٹس ایپ" class="site-footer__social-link">
                                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path d="M20.5 3.5A11 11 0 0 0 3.2 17.7L2 22l4.4-1.1A11 11 0 1 0 20.5 3.5zM12 20.2a9.1 9.1 0 0 1-4.6-1.3l-.3-.2-2.6.7.7-2.5-.2-.3A9.1 9.1 0 1 1 12 20.2zm5-6.8c-.3-.1-1.6-.8-1.9-.9s-.4-.1-.6.1-.7.9-.8 1-.3.2-.6 0a7.4 7.4 0 0 1-2.2-1.4 8.2 8.2 0 0 1-1.5-1.9c-.2-.3 0-.4.1-.6l.4-.5c.1-.2.1-.3 0-.5l-.9-2.1c-.2-.5-.5-.5-.6-.5h-.5c-.2 0-.5.1-.7.3s-1 1-1 2.4 1 2.8 1.2 3c.1.2 2 3.1 4.9 4.3 1.8.8 2.2.8 3 .7.5-.1 1.6-.7 1.8-1.3s.2-1.2.2-1.3-.2-.2-.5-.3z"/>
                                </svg>
                            </a>
                        @endif
                        @if ($phoneHref)
                            <a href="{{ $phoneHref }}" aria-label="فون" class="site-footer__social-link">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.5 5.5c0-.8.7-1.5 1.5-1.5h3.1c.6 0 1.1.4 1.3 1l1 3.1c.2.5 0 1.1-.4 1.4l-1.5 1.2a13 13 0 0 0 5.8 5.8l1.2-1.5c.3-.4.9-.6 1.4-.4l3.1 1c.6.2 1 .7 1 1.3V20c0 .8-.7 1.5-1.5 1.5C9.9 21.5 2.5 14.1 2.5 5.5z"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            <div>
                <p class="site-footer__heading">صفحات</p>
                <ul class="site-footer__links">
                    <li><a href="{{ route('home') }}">صفحہ اول</a></li>
                    <li><a href="{{ route('about') }}">ہمارے بارے میں</a></li>
                    <li><a href="{{ route('contact') }}">رابطہ</a></li>
                    <li><a href="{{ route('events.index') }}">پروگرامات</a></li>
                    <li><a href="{{ route('hifz.show') }}">حفظ القرآن</a></li>
                </ul>
            </div>

            <div>
                <p class="site-footer__heading">محتویات</p>
                <ul class="site-footer__links">
                    <li><a href="{{ route('hadith.index') }}">احادیث</a></li>
                    <li><a href="{{ route('blog.index') }}">بلاگ</a></li>
                    <li><a href="{{ route('gallery.photos') }}">تصویری یادگار</a></li>
                    <li><a href="{{ route('gallery.videos') }}">ویڈیو یادگار</a></li>
                </ul>
            </div>

            <div>
                <p class="site-footer__heading">فوری روابط</p>
                <ul class="site-footer__links">
                    <li><a href="{{ url('/#prayer-timing') }}">اوقاتِ نماز</a></li>
                    <li><a href="{{ url('/#hifz') }}">حفظ القرآن</a></li>
                    <li><a href="{{ url('/#muhtamim-message') }}">مہتمم کا پیغام</a></li>
                    @if ($whatsappHref)
                        <li><a href="{{ $whatsappHref }}" target="_blank" rel="noopener noreferrer">واٹس ایپ</a></li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

    <div class="site-footer__bottom">
        <div class="page-shell">
            <p class="site-footer__copy">
                <span class="site-footer__copy-mark" dir="ltr" aria-hidden="true">&copy; {{ date('Y') }}</span>
                <span class="site-footer__copy-name">{{ $site['name'] ?? config('site.name') }}</span>
                <span class="site-footer__copy-sep" aria-hidden="true">·</span>
                <span>جملہ حقوق محفوظ ہیں</span>
            </p>
        </div>
    </div>
</footer>

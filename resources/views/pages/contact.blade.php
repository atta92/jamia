@extends('layouts.public')

@section('title', 'رابطہ — '.($site['name'] ?? config('site.name')))
@section('meta_description', 'دارالعلوم پیر شہانی سے رابطہ کریں — پتہ، فون، ای میل اور پیغام بھیجیں۔')

@section('content')
    @php
        $phone = trim((string) ($site['phone'] ?? ''));
        $email = trim((string) ($site['email'] ?? ''));
        $address = trim((string) ($site['address'] ?? ''));
        if ($address === '') {
            $address = trim(($site['location_label'] ?? '').'، '.($site['prayer']['city_ur'] ?? $site['prayer']['city'] ?? ''));
        }
        $whatsapp = preg_replace('/\D+/', '', (string) ($site['muhtamim_whatsapp'] ?? $phone));
        $facebook = trim((string) ($site['muhtamim_facebook_url'] ?? ''));
        $mapsQuery = $address !== '' ? $address : (($site['prayer']['city'] ?? 'Chakwal').', Pakistan');
        $mapsEmbed = 'https://maps.google.com/maps?q='.rawurlencode($mapsQuery).'&z=14&output=embed';
        $mapsOpen = 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($mapsQuery);
    @endphp

    @include('partials.page-hero', [
        'kicker' => 'رابطہ',
        'title' => 'ہم سے رابطہ کریں',
        'lead' => 'سوالات، داخلہ یا عمومی معلومات کے لیے ہم سے رابطہ کیجیے، ہم یہاں مدد کے لیے موجود ہیں۔',
        'crumbs' => [
            ['label' => 'صفحہ اول', 'url' => route('home')],
            ['label' => 'رابطہ', 'url' => null],
        ],
    ])

    <section class="contact-page page-fade-section bg-white py-10 sm:py-14">
        @include('partials.page-fade-icons')
        <div class="page-shell">
            @if (session('contact_success'))
                <div class="contact-alert contact-alert--success mb-6" role="status">
                    {{ session('contact_success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="contact-alert contact-alert--error mb-6" role="alert">
                    براہِ کرم فارم درست بھریں۔
                </div>
            @endif

            <div class="contact-page__grid">
                {{-- Form --}}
                <div class="contact-panel">
                    <h2 class="contact-panel__title font-display">پیغام بھیجیں</h2>
                    <p class="contact-panel__lead">اپنا نام، رابطہ اور پیغام لکھیں، ہم جلد جواب دیں گے۔</p>

                    <form method="POST" action="{{ route('contact.store') }}" class="contact-form">
                        @csrf

                        <div class="contact-form__row">
                            <label class="contact-form__field">
                                <span>نام <em>*</em></span>
                                <input type="text" name="name" value="{{ old('name') }}" required maxlength="120" autocomplete="name">
                                @error('name') <small>{{ $message }}</small> @enderror
                            </label>
                            <label class="contact-form__field">
                                <span>ای میل <em>*</em></span>
                                <input type="email" name="email" value="{{ old('email') }}" required maxlength="160" autocomplete="email" dir="ltr">
                                @error('email') <small>{{ $message }}</small> @enderror
                            </label>
                        </div>

                        <div class="contact-form__row">
                            <label class="contact-form__field">
                                <span>فون</span>
                                <input type="text" name="phone" value="{{ old('phone') }}" maxlength="40" autocomplete="tel" dir="ltr">
                                @error('phone') <small>{{ $message }}</small> @enderror
                            </label>
                            <label class="contact-form__field">
                                <span>موضوع</span>
                                <input type="text" name="subject" value="{{ old('subject') }}" maxlength="160">
                                @error('subject') <small>{{ $message }}</small> @enderror
                            </label>
                        </div>

                        <label class="contact-form__field">
                            <span>پیغام <em>*</em></span>
                            <textarea name="message" rows="6" required maxlength="5000">{{ old('message') }}</textarea>
                            @error('message') <small>{{ $message }}</small> @enderror
                        </label>

                        {{-- Honeypot --}}
                        <input type="text" name="website" value="" tabindex="-1" autocomplete="off" class="contact-form__hp" aria-hidden="true">

                        <button type="submit" class="contact-form__submit">
                            پیغام بھیجیں
                            <span aria-hidden="true">←</span>
                        </button>
                    </form>
                </div>

                {{-- Details --}}
                <aside class="contact-panel contact-panel--aside">
                    <div class="contact-panel__body">
                        <h2 class="contact-panel__title font-display">رابطے کی تفصیل</h2>
                        <p class="contact-panel__lead">ای میل، فون یا واٹس ایپ سے بھی رابطہ کر سکتے ہیں۔</p>

                        <ul class="contact-details">
                            @if ($email !== '')
                                <li>
                                    <span class="contact-details__label">ای میل</span>
                                    <a href="mailto:{{ $email }}" dir="ltr">{{ $email }}</a>
                                </li>
                            @endif
                            @if ($phone !== '')
                                <li>
                                    <span class="contact-details__label">فون</span>
                                    <a href="tel:{{ preg_replace('/\s+/', '', $phone) }}" dir="ltr">{{ $phone }}</a>
                                </li>
                            @endif
                            @if ($address !== '')
                                <li>
                                    <span class="contact-details__label">پتہ</span>
                                    <p dir="ltr">{{ $address }}</p>
                                </li>
                            @endif
                        </ul>
                    </div>

                    <div class="contact-panel__footer">
                        <div class="contact-details__actions">
                            @if ($email !== '')
                                <a href="mailto:{{ $email }}" class="contact-details__btn">ای میل بھیجیں ↗</a>
                            @endif
                            @if ($whatsapp)
                                <a href="https://wa.me/{{ $whatsapp }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="contact-details__btn contact-details__btn--ghost">
                                    واٹس ایپ
                                </a>
                            @endif
                        </div>

                        @if ($facebook !== '' || $whatsapp || $phone !== '')
                            <div class="contact-social">
                                <p class="contact-social__label">ہمیں فالو کریں</p>
                                <div class="contact-social__links">
                                    @if ($facebook !== '')
                                        <a href="{{ $facebook }}"
                                           target="_blank"
                                           rel="noopener noreferrer"
                                           class="contact-social__link"
                                           aria-label="فیس بک">
                                            <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
                                                <path fill="currentColor" d="M22 12.07C22 6.48 17.52 2 11.93 2S1.86 6.48 1.86 12.07c0 5.02 3.66 9.18 8.44 9.93v-7.03H7.9v-2.9h2.4V9.84c0-2.37 1.4-3.69 3.56-3.69 1.03 0 2.12.19 2.12.19v2.34h-1.2c-1.18 0-1.55.74-1.55 1.49v1.79h2.64l-.42 2.9h-2.22V22c4.78-.75 8.44-4.91 8.44-9.93z"/>
                                            </svg>
                                        </a>
                                    @endif
                                    @if ($whatsapp)
                                        <a href="https://wa.me/{{ $whatsapp }}"
                                           target="_blank"
                                           rel="noopener noreferrer"
                                           class="contact-social__link"
                                           aria-label="واٹس ایپ">
                                            <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
                                                <path fill="currentColor" d="M19.05 4.91A9.82 9.82 0 0 0 12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.87 9.87 0 0 0 4.79 1.22h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.91-7.02zm-7.01 15.24h-.01a8.2 8.2 0 0 1-4.18-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.18 8.18 0 0 1-1.26-4.38c0-4.53 3.69-8.21 8.23-8.21 2.2 0 4.26.86 5.81 2.41a8.16 8.16 0 0 1 2.41 5.81c0 4.53-3.69 8.23-8.21 8.23zm4.51-6.15c-.25-.12-1.46-.72-1.69-.8-.23-.09-.39-.12-.56.12-.16.25-.64.8-.78.96-.14.17-.29.19-.54.06-.25-.12-1.05-.39-2-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.38.11-.51.11-.11.25-.29.37-.43.12-.14.16-.25.25-.41.08-.17.04-.31-.02-.43-.06-.12-.56-1.35-.77-1.85-.2-.48-.41-.42-.56-.42h-.48c-.17 0-.43.06-.66.31-.23.25-.87.85-.87 2.07 0 1.22.89 2.4 1.01 2.56.12.17 1.75 2.67 4.23 3.74 1.48.64 2.07.7 2.81.59.43-.07 1.46-.6 1.66-1.18.21-.58.21-1.07.14-1.18-.06-.1-.23-.17-.48-.29z"/>
                                            </svg>
                                        </a>
                                    @endif
                                    @if ($phone !== '')
                                        <a href="tel:{{ preg_replace('/\s+/', '', $phone) }}"
                                           class="contact-social__link"
                                           aria-label="فون">
                                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.5 5.5c0-.8.7-1.5 1.5-1.5h3.1c.6 0 1.1.4 1.3 1l1 3.1c.2.5 0 1.1-.4 1.4l-1.5 1.2a13 13 0 0 0 5.8 5.8l1.2-1.5c.3-.4.9-.6 1.4-.4l3.1 1c.6.2 1 .7 1 1.3V20c0 .8-.7 1.5-1.5 1.5C9.9 21.5 2.5 14.1 2.5 5.5z"/>
                                            </svg>
                                        </a>
                                    @endif
                                    @if ($email !== '')
                                        <a href="mailto:{{ $email }}"
                                           class="contact-social__link"
                                           aria-label="ای میل">
                                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <rect x="3" y="5" width="18" height="14" rx="2"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m4 7 8 6 8-6"/>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <section class="contact-maps page-fade-section" aria-label="مقام">
        @include('partials.page-fade-icons')
        <div class="page-shell">
            <div class="contact-maps__head">
                <p class="about-block__kicker">مقام</p>
                <h2 class="about-block__title font-display">ہمارا پتہ تلاش کریں</h2>
                <p class="about-block__lead">نقشے پر جامعہ کا مقام دیکھیں یا گوگل میپس میں کھولیں۔</p>
            </div>

            <div class="contact-maps__panel">
                <div class="contact-maps__meta">
                    <div class="contact-maps__info">
                        <span>{{ $site['name'] ?? config('site.name') }}</span>
                        @if ($address !== '')
                            <p dir="ltr">{{ $address }}</p>
                        @endif
                    </div>
                    <a class="contact-maps__btn"
                       href="{{ $mapsOpen }}"
                       target="_blank"
                       rel="noopener noreferrer">
                        گوگل میپس میں کھولیں ↗
                    </a>
                </div>

                <div class="contact-maps__frame">
                    <iframe
                        title="مقامِ جامعہ"
                        src="{{ $mapsEmbed }}"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        allowfullscreen
                    ></iframe>
                </div>
            </div>
        </div>
    </section>
@endsection

@php
    $isSolid = ! empty($solidHeader);
    $home = url('/');
    $rabtaOpen = request()->routeIs('about', 'contact');
    $galleryOpen = request()->routeIs('gallery.*');
@endphp

<header id="site-header"
        @class([
            'site-header fixed inset-x-0 top-0 z-50 transition-all duration-300',
            'site-header--solid border-b border-teal-800/10 bg-white/98 shadow-sm backdrop-blur-md' => $isSolid,
            'site-header--overlay bg-transparent data-[scrolled=true]:border-b data-[scrolled=true]:border-teal-800/10 data-[scrolled=true]:bg-white/95 data-[scrolled=true]:shadow-sm data-[scrolled=true]:backdrop-blur-md' => ! $isSolid,
        ])
        data-scrolled="{{ $isSolid ? 'true' : 'false' }}"
        data-header-mode="{{ $isSolid ? 'solid' : 'overlay' }}">
    <div class="page-shell site-header__bar {{ $isSolid ? 'py-2.5' : 'py-3' }}">
        <a href="{{ $home }}" class="site-brand-link group flex min-w-0 items-center gap-1.5 sm:gap-2">
            @if (! empty($site['logo']))
                <img
                    src="{{ asset('storage/'.$site['logo']) }}"
                    alt="{{ $site['name'] ?? config('site.name') }}"
                    class="site-brand-img h-7 w-auto max-w-[96px] object-contain sm:h-9 sm:max-w-[150px]"
                >
            @endif
            <span class="site-brand site-brand--logo transition-colors {{ $isSolid ? 'text-ink' : 'text-white' }}">
                {{ $site['name'] ?? config('site.name') }}
            </span>
        </a>

        <nav class="site-nav hidden items-center justify-center gap-5 xl:gap-6 text-[0.95rem] font-medium transition-colors lg:flex {{ $isSolid ? 'text-ink' : 'text-white/90' }}">
            <a href="{{ $home }}" class="site-nav-link transition hover:text-gold-500">صفحہ اول</a>
            <a href="{{ $home }}#prayer-timing" class="site-nav-link transition hover:text-gold-500">اوقاتِ نماز</a>
            <div class="site-nav-dropdown relative">
                <button type="button"
                        class="site-nav-link inline-flex items-center gap-1 transition hover:text-gold-500 {{ $isSolid && $galleryOpen ? 'text-gold-600' : '' }}"
                        data-nav-dropdown-btn
                        aria-expanded="false"
                        aria-haspopup="true">
                    یادگار
                    <svg class="site-nav-dropdown__arrow h-3.5 w-3.5 opacity-80" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06z" clip-rule="evenodd"/>
                    </svg>
                </button>
                <div class="site-nav-dropdown__menu" data-nav-dropdown-menu hidden>
                    <a href="{{ route('gallery.photos') }}"
                       @class(['site-nav-dropdown__link', 'is-active' => request()->routeIs('gallery.photos')])>
                        تصاویر
                    </a>
                    <a href="{{ route('gallery.videos') }}"
                       @class(['site-nav-dropdown__link', 'is-active' => request()->routeIs('gallery.videos')])>
                        ویڈیوز
                    </a>
                </div>
            </div>

            <a href="{{ route('blog.index') }}"
               @class([
                   'site-nav-link transition hover:text-gold-500',
                   'text-gold-600' => $isSolid && request()->routeIs('blog.*'),
               ])>
                بلاگ
            </a>
            <a href="{{ route('hadith.index') }}"
               @class([
                   'site-nav-link transition hover:text-gold-500',
                   'text-gold-600' => $isSolid && request()->routeIs('hadith.*'),
               ])>
                احادیث
            </a>
            <a href="{{ route('events.index') }}"
               @class([
                   'site-nav-link transition hover:text-gold-500',
                   'text-gold-600' => $isSolid && request()->routeIs('events.*'),
               ])>
                پروگرامات
            </a>
            <a href="{{ route('hifz.show') }}"
               @class([
                   'site-nav-link transition hover:text-gold-500',
                   'text-gold-600' => $isSolid && request()->routeIs('hifz.*'),
               ])>
                حفظ القرآن
            </a>

            <div class="site-nav-dropdown relative">
                <button type="button"
                        class="site-nav-link inline-flex items-center gap-1 transition hover:text-gold-500 {{ $isSolid && $rabtaOpen ? 'text-gold-600' : '' }}"
                        data-nav-dropdown-btn
                        aria-expanded="false"
                        aria-haspopup="true">
                    رابطہ
                    <svg class="site-nav-dropdown__arrow h-3.5 w-3.5 opacity-80" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06z" clip-rule="evenodd"/>
                    </svg>
                </button>
                <div class="site-nav-dropdown__menu" data-nav-dropdown-menu hidden>
                    <a href="{{ route('about') }}"
                       @class(['site-nav-dropdown__link', 'is-active' => request()->routeIs('about')])>
                        ہمارے بارے میں
                    </a>
                    <a href="{{ route('about') }}#teachers" class="site-nav-dropdown__link">
                        اساتذہ کرام
                    </a>
                    <a href="{{ route('about') }}#faizan-e-nazar" class="site-nav-dropdown__link">
                        فیضانِ نظر
                    </a>
                    <a href="{{ route('contact') }}"
                       @class(['site-nav-dropdown__link', 'is-active' => request()->routeIs('contact')])>
                        رابطہ کریں
                    </a>
                </div>
            </div>
        </nav>

        <div class="site-header__actions flex items-center justify-end gap-3">
            <a href="{{ $home }}#prayer-timing"
               id="prayer-countdown"
               class="prayer-countdown"
               data-prayer-countdown='@json($prayerCountdown ?? ["timezone" => "UTC", "prayers" => []])'
               aria-live="polite"
               aria-busy="false"
               title="اگلی نماز کا باقی وقت">
                <span class="prayer-countdown__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="8.5"/>
                        <path d="M12 8v4.4l2.6 1.6"/>
                    </svg>
                </span>
                <span class="prayer-countdown__skeleton" aria-hidden="true">
                    <span class="prayer-countdown__skeleton-line prayer-countdown__skeleton-line--label"></span>
                    <span class="prayer-countdown__skeleton-line prayer-countdown__skeleton-line--time"></span>
                </span>
                <span class="prayer-countdown__body">
                    <span class="prayer-countdown__label">
                        <span data-countdown-label>نماز کا باقی وقت</span>
                    </span>
                    <span class="prayer-countdown__time" dir="ltr" data-countdown-time>--:--:--</span>
                </span>
            </a>

            <button type="button"
                    id="mobile-menu-btn"
                    @class([
                        'mobile-menu-btn inline-flex h-9 w-9 items-center justify-center rounded-md border transition lg:hidden',
                        'border-ink/20 text-ink hover:bg-cream-100' => $isSolid,
                        'border-white/30 text-white' => ! $isSolid,
                    ])
                    aria-label="مینو کھولیں"
                    aria-expanded="false"
                    aria-controls="mobile-nav">
                <svg class="mobile-menu-btn__icon mobile-menu-btn__icon--open h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16"/>
                </svg>
                <svg class="mobile-menu-btn__icon mobile-menu-btn__icon--close h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18"/>
                </svg>
            </button>
        </div>
    </div>

    <div id="mobile-nav" class="hidden lg:hidden">
        <nav @class([
            'page-shell flex flex-col gap-1 py-3 text-base font-medium',
            'border-t border-ink/10 bg-white text-ink' => $isSolid,
            'bg-teal-950/95 text-white backdrop-blur-sm' => ! $isSolid,
        ])>
            <a href="{{ $home }}" class="rounded-md px-3 py-2 {{ $isSolid ? 'hover:bg-cream-100' : 'hover:bg-white/10' }}">صفحہ اول</a>
            <a href="{{ $home }}#prayer-timing" class="rounded-md px-3 py-2 {{ $isSolid ? 'hover:bg-cream-100' : 'hover:bg-white/10' }}">اوقاتِ نماز</a>
            <p class="px-3 pt-2 text-sm font-semibold {{ $isSolid ? 'text-gold-600' : 'text-gold-500' }}">یادگار</p>
            <a href="{{ route('gallery.photos') }}" class="rounded-md px-3 py-2 {{ $isSolid ? 'hover:bg-cream-100' : 'hover:bg-white/10' }}">تصاویر</a>
            <a href="{{ route('gallery.videos') }}" class="rounded-md px-3 py-2 {{ $isSolid ? 'hover:bg-cream-100' : 'hover:bg-white/10' }}">ویڈیوز</a>
            <a href="{{ route('blog.index') }}" class="rounded-md px-3 py-2 {{ $isSolid ? 'hover:bg-cream-100' : 'hover:bg-white/10' }}">بلاگ</a>
            <a href="{{ route('hadith.index') }}" class="rounded-md px-3 py-2 {{ $isSolid ? 'hover:bg-cream-100' : 'hover:bg-white/10' }}">احادیث</a>
            <a href="{{ route('events.index') }}" class="rounded-md px-3 py-2 {{ $isSolid ? 'hover:bg-cream-100' : 'hover:bg-white/10' }}">پروگرامات</a>
            <a href="{{ route('hifz.show') }}" class="rounded-md px-3 py-2 {{ $isSolid ? 'hover:bg-cream-100' : 'hover:bg-white/10' }}">حفظ القرآن</a>

            <p class="px-3 pt-2 text-sm font-semibold {{ $isSolid ? 'text-gold-600' : 'text-gold-500' }}">رابطہ</p>
            <a href="{{ route('about') }}" class="rounded-md px-3 py-2 {{ $isSolid ? 'hover:bg-cream-100' : 'hover:bg-white/10' }}">ہمارے بارے میں</a>
            <a href="{{ route('about') }}#teachers" class="rounded-md px-3 py-2 {{ $isSolid ? 'hover:bg-cream-100' : 'hover:bg-white/10' }}">اساتذہ کرام</a>
            <a href="{{ route('about') }}#faizan-e-nazar" class="rounded-md px-3 py-2 {{ $isSolid ? 'hover:bg-cream-100' : 'hover:bg-white/10' }}">فیضانِ نظر</a>
            <a href="{{ route('contact') }}" class="rounded-md px-3 py-2 {{ $isSolid ? 'hover:bg-cream-100' : 'hover:bg-white/10' }}">رابطہ کریں</a>
        </nav>
    </div>
</header>

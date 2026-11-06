@php
    $posts = $blogPosts ?? collect();
@endphp

@if ((! empty($site['show_blog_section'] ?? true)) && $posts->isNotEmpty())
@php
    $blogKicker = $site['blog_kicker'] ?: 'بلاگ';
    $blogTitle = $site['blog_title'] ?: 'علمی مباحث اور شرعی رہنمائی';
    $blogLead = $site['blog_lead'] ?: 'تعلیم، اخلاق، عبادت اور جامعہ کی زندگی سے متعلق تازہ مضامین۔';
    $blogCta = $site['blog_cta'] ?: 'تمام مضامین';
@endphp
<section id="blog" class="home-blog relative overflow-hidden bg-white py-14 sm:py-20" aria-label="{{ $blogKicker }}">
    <div class="page-cage" aria-hidden="true"></div>

    <div class="page-shell relative z-[1]">
        <div class="mx-auto max-w-3xl text-center reveal">
            <p class="text-base font-medium text-gold-600">{{ $blogKicker }}</p>
            <h2 class="font-display mt-3 text-4xl font-bold leading-loose text-ink">
                {{ $blogTitle }}
            </h2>
            @if ($blogLead !== '')
                <p class="mx-auto mt-5 max-w-2xl text-lg leading-loose text-muted">
                    {{ $blogLead }}
                </p>
            @endif
        </div>

        @if ($posts->count() > 1)
            <div class="home-blog__nav reveal" aria-label="بلاگ سکرول">
                <button
                    type="button"
                    class="home-blog__nav-btn"
                    data-blog-prev
                    aria-label="پچھلے مضامین"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/>
                    </svg>
                </button>
                <button
                    type="button"
                    class="home-blog__nav-btn"
                    data-blog-next
                    aria-label="اگلے مضامین"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/>
                    </svg>
                </button>
            </div>
        @endif

        <div class="home-blog__scroller mt-8 reveal" data-blog-scroller>
            <div class="home-blog__track" data-blog-track>
                @foreach ($posts as $post)
                    <a href="{{ route('blog.show', $post->slug) }}" class="home-blog__item">
                        <span class="home-blog__media">
                            @if ($post->coverUrl())
                                <img src="{{ $post->coverUrl() }}" alt="{{ $post->title }}" loading="lazy">
                            @else
                                <span class="home-blog__placeholder" aria-hidden="true"></span>
                            @endif
                        </span>
                        <h3 class="home-blog__title font-display">{{ $post->title }}</h3>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="mt-10 flex justify-center reveal">
            <a href="{{ route('blog.index') }}" class="home-blog__all">
                <span>{{ $blogCta }}</span>
                @include('partials.icons.chevron-start')
            </a>
        </div>
    </div>
</section>
@endif

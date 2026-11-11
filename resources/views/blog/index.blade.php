@extends('layouts.public')

@section('title', 'بلاگ - '.($site['name'] ?? config('site.name')))
@section('meta_description', 'علمی مباحث، شرعی رہنمائی اور جامعہ کی زندگی سے متعلق تازہ مضامین۔')

@section('content')
<header class="page-hero">
    <div class="page-hero__cage" aria-hidden="true"></div>
    <div class="page-shell relative">
        <nav class="page-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">صفحہ اول</a>
            <span aria-hidden="true">/</span>
            <span>بلاگ</span>
        </nav>
        <p class="page-kicker">بلاگ</p>
        <h1 class="page-hero__title">مضامین و تحریریں</h1>
        <p class="page-hero__lead">جامعہ کی تعلیم، عبادت اور خدمت سے متعلق تازہ خیالات۔</p>
    </div>
</header>

<section class="page-fade-section bg-white pb-16 sm:pb-20">
    @include('partials.page-fade-icons')
    <div class="page-shell pt-5 sm:pt-6">
        @if ($posts->isEmpty())
            <div class="rounded-2xl border border-teal-800/10 bg-cream-50 px-6 py-16 text-center">
                <p class="font-display text-2xl font-bold text-teal-800">ابھی کوئی مضمون نہیں</p>
                <p class="mt-3 text-base text-muted">جلد نئی تحریریں شامل کی جائیں گی۔</p>
            </div>
        @else
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($posts as $post)
                    <a href="{{ route('blog.show', $post->slug) }}" class="blog-card group">
                        <div class="blog-card__media">
                            @if ($post->coverUrl())
                                <img src="{{ $post->coverUrl() }}" alt="{{ $post->title }}" loading="lazy">
                            @else
                                <div class="blog-card__placeholder" aria-hidden="true"></div>
                            @endif
                        </div>
                        <div class="blog-card__body">
                            <div class="blog-card__meta">
                                @if ($post->category)
                                    <span class="blog-card__category">{{ $post->category }}</span>
                                @endif
                                @if ($post->readingLabel())
                                    <span>{{ $post->readingLabel() }}</span>
                                @endif
                            </div>
                            <h2 class="blog-card__title">{{ $post->title }}</h2>
                            @if ($post->excerpt)
                                <p class="blog-card__excerpt">{{ $post->excerpt }}</p>
                            @endif
                            <span class="blog-card__more">
                                <span>مزید پڑھیں</span>
                                @include('partials.icons.chevron-start')
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
</section>
@endsection

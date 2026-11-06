@extends('layouts.public')

@section('title', $post->title.' — '.($site['name'] ?? config('site.name')))

@section('content')
@php
    $published = $post->published_at ?: $post->created_at;
    $moreLabel = $post->category
        ? 'مزید از '.$post->category
        : 'مزید مضامین';
@endphp

<header class="page-hero">
    <div class="page-hero__cage" aria-hidden="true"></div>
    <div class="page-shell relative">
        <nav class="page-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">صفحہ اول</a>
            <span aria-hidden="true">/</span>
            <a href="{{ route('blog.index') }}">بلاگ</a>
            @if ($post->category)
                <span aria-hidden="true">/</span>
                <span>{{ $post->category }}</span>
            @endif
        </nav>
        <p class="page-kicker">بلاگ</p>
        <p class="page-hero__title">مضامین و تحریریں</p>
    </div>
</header>

<article class="blog-article">
    <div class="page-shell blog-article__shell">
        <header class="blog-article__head">
            @if ($post->category)
                <p class="blog-article__category">{{ $post->category }}</p>
            @endif

            <h1 class="blog-article__title font-display">{{ $post->title }}</h1>

            @if ($published)
                <time class="blog-article__date" datetime="{{ $published->toDateString() }}">
                    {{ $published->format('d M Y') }}
                </time>
            @endif
        </header>

        <div class="blog-article__layout">
            <div class="blog-article__main">
                @if ($post->coverUrl())
                    <figure class="blog-article__cover">
                        <img src="{{ $post->coverUrl() }}" alt="{{ $post->title }}" loading="eager">
                    </figure>
                @endif

                <div class="blog-content blog-article__body">
                    {!! $post->body !!}
                </div>
            </div>

            @if ($related->isNotEmpty())
                <aside class="blog-article__related" aria-label="مزید مضامین">
                    <div class="blog-article__related-list">
                        @foreach ($related as $item)
                            <a href="{{ route('blog.show', $item->slug) }}" class="blog-article__related-card">
                                <span class="blog-article__related-media">
                                    @if ($item->coverUrl())
                                        <img src="{{ $item->coverUrl() }}" alt="" loading="lazy">
                                    @else
                                        <span class="blog-article__related-placeholder" aria-hidden="true"></span>
                                    @endif
                                </span>
                                <span class="blog-article__related-name font-display">{{ $item->title }}</span>
                            </a>
                        @endforeach
                    </div>
                </aside>
            @endif
        </div>

        @if ($related->isNotEmpty())
            <section class="blog-article__more" aria-label="{{ $moreLabel }}">
                <h2 class="blog-article__more-title font-display">{{ $moreLabel }}</h2>
                <div class="blog-article__more-grid">
                    @foreach ($related as $item)
                        <a href="{{ route('blog.show', $item->slug) }}" class="blog-article__more-card">
                            <span class="blog-article__more-media">
                                @if ($item->coverUrl())
                                    <img src="{{ $item->coverUrl() }}" alt="" loading="lazy">
                                @else
                                    <span class="blog-article__more-placeholder" aria-hidden="true"></span>
                                @endif
                            </span>
                            <span class="blog-article__more-name font-display">{{ $item->title }}</span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</article>
@endsection

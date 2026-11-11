@extends('layouts.public')

@section('title', 'تلاش - '.$book->titleUr().' - '.($site['name'] ?? config('site.name')))
@section('meta_description', $book->titleUr().' میں احادیث کی تلاش۔')

@section('content')
<header class="page-hero">
    <div class="page-hero__cage" aria-hidden="true"></div>
    <div class="page-shell relative">
        <nav class="page-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">صفحہ اول</a>
            <span aria-hidden="true">/</span>
            <a href="{{ route('hadith.index') }}">احادیث</a>
            <span aria-hidden="true">/</span>
            <a href="{{ route('hadith.book', $book) }}">{{ $book->titleUr() }}</a>
            <span aria-hidden="true">/</span>
            <span>تلاش</span>
        </nav>
        <p class="page-kicker">{{ $book->titleUr() }}</p>
        <h1 class="page-hero__title">احادیث تلاش</h1>
        <p class="page-hero__lead">اس کتاب کے تمام ابواب میں تلاش کے نتائج۔</p>
    </div>
</header>

<section
    class="hadith-browse hadith-browse--plain page-fade-section pb-16 sm:pb-20"
    x-data="hadithSearch({
        endpoint: @js(route('hadith.search', $book)),
        query: @js($search),
        mode: @js($searchType ?? 'urdu'),
        typed: true,
    })"
>
    <div class="page-shell pt-5 sm:pt-6">
        <form class="hadith-search hadith-search--typed" role="search" @submit="onSubmit">
            <div class="hadith-search__group">
                <label for="hadith-book-search-type" class="sr-only">تلاش کی قسم</label>
                <select
                    id="hadith-book-search-type"
                    class="hadith-search__type"
                    name="type"
                    x-model="mode"
                >
                    <option value="urdu">اردو</option>
                    <option value="arabic">عربی</option>
                    <option value="number">نمبر</option>
                </select>

                <label for="hadith-book-text-search" class="sr-only">حدیث تلاش کریں</label>
                <input
                    id="hadith-book-text-search"
                    type="search"
                    name="q"
                    x-model="query"
                    :placeholder="placeholder"
                    class="hadith-search__input"
                    autocomplete="off"
                    :dir="mode === 'arabic' ? 'rtl' : (mode === 'number' ? 'ltr' : 'rtl')"
                >
                <button type="submit" class="hadith-search__btn" :disabled="loading">
                    <span x-show="!loading">تلاش</span>
                    <span x-cloak x-show="loading">…</span>
                </button>
            </div>
            <button
                type="button"
                class="hadith-search__clear"
                x-cloak
                x-show="query.trim() !== ''"
                @click="clear"
            >صاف کریں</button>
        </form>

        <div class="hadith-step-nav">
            <a href="{{ route('hadith.index') }}">
                @include('partials.icons.chevron-start')
                <span>تمام کتب</span>
            </a>
            <a href="{{ route('hadith.book', $book) }}">
                @include('partials.icons.chevron-start')
                <span>ابوابِ {{ $book->titleUr() }}</span>
            </a>
        </div>

        <div
            class="hadith-search-results"
            :class="{ 'is-loading': loading }"
            x-ref="results"
        >
            @include('hadith.partials.search-results')
        </div>
    </div>
</section>
@endsection

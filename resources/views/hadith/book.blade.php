@extends('layouts.public')

@section('title', $book->titleUr().' — ابواب — '.($site['name'] ?? config('site.name')))

@section('content')
<header class="page-hero">
    <div class="page-hero__cage" aria-hidden="true"></div>
    <div class="page-shell relative">
        <nav class="page-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">صفحہ اول</a>
            <span aria-hidden="true">/</span>
            <a href="{{ route('hadith.index') }}">احادیث</a>
            <span aria-hidden="true">/</span>
            <span>{{ $book->titleUr() }}</span>
        </nav>
        <p class="page-kicker">{{ $book->book_name }}</p>
        <h1 class="page-hero__title">{{ $book->titleUr() }}</h1>
        <p class="page-hero__lead">
            ابواب منتخب کریں تاکہ اس باب کی احادیث دیکھ سکیں۔
            @if ($book->writer_name)
                <span class="block mt-2 text-base">مصنف: {{ $book->writer_name }}</span>
            @endif
        </p>
    </div>
</header>

<section
    class="hadith-browse page-fade-section bg-white pb-16 sm:pb-20"
    x-data="hadithSearch({ endpoint: @js(route('hadith.book', $book)), query: @js($search), placeholder: 'باب تلاش کریں…' })"
>
    @include('partials.page-fade-icons')
    <div class="page-shell pt-5 sm:pt-6">
        <form class="hadith-search" role="search" @submit="onSubmit">
            <label for="hadith-chapter-search" class="sr-only">باب تلاش کریں</label>
            <input
                id="hadith-chapter-search"
                type="search"
                name="q"
                x-model="query"
                :placeholder="placeholder"
                class="hadith-search__input"
                autocomplete="off"
            >
            <button type="submit" class="hadith-search__btn" :disabled="loading">
                <span x-show="!loading">تلاش</span>
                <span x-cloak x-show="loading">…</span>
            </button>
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
        </div>

        <div
            class="hadith-search-results"
            :class="{ 'is-loading': loading }"
            x-ref="results"
        >
            @include('hadith.partials.chapters-results')
        </div>
    </div>
</section>
@endsection

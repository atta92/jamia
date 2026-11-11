@extends('layouts.public')

@section('title', 'کتبِ حدیث - '.($site['name'] ?? config('site.name')))
@section('meta_description', 'صحیح بخاری، مسلم اور دیگر کتبِ حدیث - ابواب اور احادیث تلاش کریں۔')

@section('content')
<header class="page-hero">
    <div class="page-hero__cage" aria-hidden="true"></div>
    <div class="page-shell relative">
        <nav class="page-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">صفحہ اول</a>
            <span aria-hidden="true">/</span>
            <span>احادیث</span>
        </nav>
        <p class="page-kicker">احادیث</p>
        <h1 class="page-hero__title">کتبِ حدیث</h1>
        <p class="page-hero__lead">کتاب منتخب کریں، پھر باب دیکھیں، پھر احادیث پڑھیں۔</p>
    </div>
</header>

<section
    class="hadith-browse page-fade-section pb-16 sm:pb-20"
    x-data="hadithSearch({ endpoint: @js(route('hadith.index')), query: @js($search), placeholder: 'کتاب یا مصنف تلاش کریں…' })"
>
    <div class="page-cage" aria-hidden="true"></div>
    @include('partials.page-fade-icons')
    <div class="page-shell pt-5 sm:pt-6">
        <form class="hadith-search" role="search" @submit="onSubmit">
            <label for="hadith-book-search" class="sr-only">کتاب تلاش کریں</label>
            <input
                id="hadith-book-search"
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

        <div
            class="hadith-search-results"
            :class="{ 'is-loading': loading }"
            x-ref="results"
        >
            @include('hadith.partials.books-results')
        </div>
    </div>
</section>
@endsection

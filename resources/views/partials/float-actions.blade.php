@php
    $waRaw = trim((string) ($site['muhtamim_whatsapp'] ?? $site['phone'] ?? ''));
    $waHref = null;
    if ($waRaw !== '') {
        $waHref = str_starts_with($waRaw, 'http')
            ? $waRaw
            : 'https://wa.me/'.preg_replace('/\D+/', '', $waRaw);
    }

    $botBooks = \App\Models\HadithBook::query()
        ->visibleOnFrontend()
        ->orderByDesc('hadiths_count')
        ->orderBy('id')
        ->get(['id', 'book_name', 'book_slug', 'hadiths_count'])
        ->map(fn ($book) => [
            'slug' => $book->book_slug,
            'title' => $book->titleUr(),
        ])
        ->values()
        ->all();
@endphp

<div
    class="float-actions float-actions--bot"
    data-float-actions
    x-data="hadithBot({ endpoint: @js(route('api.hadith.search')), books: @js($botBooks) })"
    :class="{ 'is-docked': docked }"
    @keydown.escape.window="open && close()"
>
    <div class="hadith-bot" :class="{ 'is-docked': docked }">
        <div
            class="hadith-bot__panel"
            x-cloak
            x-show="open"
            x-transition.opacity.duration.150ms
            role="dialog"
            aria-label="حدیث تلاش بوٹ"
            @click.outside="if (!$event.target.closest('.hadith-bot__launcher')) close()"
        >
            <div class="hadith-bot__head">
                <div class="hadith-bot__head-text">
                    <p class="hadith-bot__title">حدیث تلاش کریں</p>
                    <p class="hadith-bot__subtitle" x-text="selectedBook ? (selectedBook.title + ' - ' + modeLabel) : 'پہلے کتاب منتخب کریں'"></p>
                </div>
                <button type="button" class="hadith-bot__close" @click="close" aria-label="بند کریں">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18"/>
                    </svg>
                </button>
            </div>

            <div class="hadith-bot__bookbar" x-show="selectedBook" x-cloak>
                <span class="hadith-bot__bookbar-label">کتاب:</span>
                <span class="hadith-bot__bookbar-name" x-text="selectedBook?.title"></span>
                <button type="button" class="hadith-bot__bookbar-change" @click="clearBook">تبدیل کریں</button>
            </div>

            <div class="hadith-bot__messages" x-ref="messages">
                <template x-for="(msg, index) in messages" :key="index">
                    <div class="hadith-bot__msg" :class="'hadith-bot__msg--' + msg.role">
                        <template x-if="msg.role === 'user'">
                            <p class="hadith-bot__bubble" x-text="msg.text"></p>
                        </template>

                        <template x-if="msg.role === 'bot' && msg.kind === 'text'">
                            <p class="hadith-bot__bubble" x-text="msg.text"></p>
                        </template>

                        <template x-if="msg.role === 'bot' && msg.kind === 'books'">
                            <div class="hadith-bot__books">
                                <p class="hadith-bot__results-label" x-text="msg.text"></p>
                                <div class="hadith-bot__book-list">
                                    <template x-for="book in books" :key="book.slug">
                                        <button
                                            type="button"
                                            class="hadith-bot__book-btn"
                                            :class="{ 'is-active': selectedBook?.slug === book.slug }"
                                            @click="selectBook(book)"
                                            x-text="book.title"
                                        ></button>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <template x-if="msg.role === 'bot' && msg.kind === 'results'">
                            <div class="hadith-bot__results">
                                <p class="hadith-bot__results-label" x-text="msg.text"></p>
                                <template x-for="item in msg.results" :key="item.id">
                                    <a :href="resultHref(item)" class="hadith-bot__card">
                                        <span class="hadith-bot__card-meta">
                                            <span class="hadith-bot__card-book" x-text="'باب ' + item.chapter_number + (item.chapter_title ? ' - ' + item.chapter_title : '')"></span>
                                            <span class="hadith-bot__card-num" dir="ltr" x-text="'#' + item.hadith_number"></span>
                                        </span>
                                        <span class="hadith-bot__card-snippet" x-text="item.snippet || '-'"></span>
                                        <span class="hadith-bot__card-cta">مکمل دیکھیں</span>
                                    </a>
                                </template>
                                <a
                                    x-show="msg.moreUrl"
                                    x-cloak
                                    :href="msg.moreUrl"
                                    class="hadith-bot__more"
                                    x-text="'تمام ' + (msg.total || '') + ' نتائج دیکھیں'"
                                ></a>
                            </div>
                        </template>
                    </div>
                </template>

                <div class="hadith-bot__msg hadith-bot__msg--bot" x-cloak x-show="loading">
                    <p class="hadith-bot__bubble hadith-bot__bubble--loading">تلاش جاری ہے…</p>
                </div>
            </div>

            <form class="hadith-bot__form" @submit.prevent="submit">
                <div class="hadith-bot__modes" x-show="selectedBook" x-cloak>
                    <button type="button" class="hadith-bot__mode" :class="{ 'is-active': mode === 'urdu' }" @click="setMode('urdu')">اردو</button>
                    <button type="button" class="hadith-bot__mode" :class="{ 'is-active': mode === 'arabic' }" @click="setMode('arabic')">عربی</button>
                    <button type="button" class="hadith-bot__mode" :class="{ 'is-active': mode === 'number' }" @click="setMode('number')">نمبر</button>
                </div>

                <div class="hadith-bot__compose">
                    <label class="sr-only" for="hadith-bot-input">حدیث تلاش کریں</label>
                    <input
                        id="hadith-bot-input"
                        class="hadith-bot__input"
                        type="search"
                        x-model="query"
                        x-ref="input"
                        :placeholder="inputPlaceholder"
                        :dir="mode === 'number' ? 'ltr' : 'rtl'"
                        autocomplete="off"
                        :disabled="loading || !selectedBook"
                    >
                    <button type="submit" class="hadith-bot__send" :disabled="loading || !selectedBook || !query.trim()" aria-label="بھیجیں">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <div class="hadith-bot__launcher" :class="{ 'is-docked': docked }">
            <div
                class="float-actions__btn float-actions__btn--bot hadith-bot__combined"
                :class="{ 'is-open': open }"
                x-show="!docked"
                x-transition.opacity.duration.150ms
            >
                <button
                    type="button"
                    class="hadith-bot__main"
                    @click="toggle"
                    :aria-expanded="open ? 'true' : 'false'"
                    aria-label="حدیث تلاش بوٹ"
                >
                    <span class="float-actions__btn-icon" aria-hidden="true">
                        <svg class="float-actions__bot-open-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                        <svg class="float-actions__bot-close-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18"/>
                        </svg>
                    </span>
                    <span class="float-actions__bot-label" x-text="open ? 'بند' : 'حدیث تلاش کریں'">حدیث تلاش کریں</span>
                </button>

                <button
                    type="button"
                    class="hadith-bot__dock hadith-bot__dock--inline"
                    x-show="!open"
                    x-cloak
                    @click.stop="toggleDock"
                    aria-label="حدیث تلاش چھپائیں"
                    title="چھپائیں"
                >
                    <svg class="hadith-bot__dock-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18"/>
                    </svg>
                </button>
            </div>

            <button
                type="button"
                class="hadith-bot__dock hadith-bot__dock--peek"
                x-cloak
                x-show="docked"
                @click="toggleDock"
                aria-label="حدیث تلاش دکھائیں"
                title="دکھائیں"
            >
                <svg class="hadith-bot__dock-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
                </svg>
            </button>
        </div>
    </div>
</div>

<div class="float-actions float-actions--social" data-float-actions>
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

@php
    $type = $searchType ?? 'urdu';
@endphp

@if ($search === '')
    <div class="hadith-empty">
        <p class="font-display text-2xl font-bold text-teal-800">تلاش کا لفظ لکھیں</p>
        <p class="mt-2 text-muted">اردو، عربی متن یا حدیث نمبر سے تمام ابواب میں تلاش کریں۔</p>
    </div>
@elseif ($hadiths->isEmpty())
    <div class="hadith-empty">
        <p class="font-display text-2xl font-bold text-teal-800">کوئی حدیث نہیں ملی</p>
        <p class="mt-2 text-muted">دوسرا لفظ آزمائیں یا تلاش کی قسم تبدیل کریں۔</p>
    </div>
@else
    <p class="hadith-search-summary">
        کل <span dir="ltr">{{ number_format($hadiths->total()) }}</span> نتیجہ - مختلف ابواب سے
    </p>

    <div class="hadith-hit-list">
        @foreach ($hadiths as $item)
            @php
                $chapter = $item->chapter;
                $chapterNumber = $chapter?->chapter_number;
                $number = $item->hadith_number ?: (string) $item->id;
                $snippetSource = match ($type) {
                    'arabic' => $item->hadith_arabic ?: $item->heading_arabic ?: $item->hadith_urdu ?: '',
                    'number' => $item->hadith_urdu ?: $item->hadith_arabic ?: $item->hadith_english ?: '',
                    default => $item->hadith_urdu ?: $item->heading_urdu ?: $item->urdu_narrator ?: $item->hadith_arabic ?: '',
                };
                $snippet = \Illuminate\Support\Str::of($snippetSource)->stripTags()->squish()->limit(180)->toString();
                $urlQuery = array_filter([
                    'hn' => $number,
                    'hq' => $search !== '' ? $search : null,
                ]);
                $url = $chapterNumber
                    ? route('hadith.chapter', [$book, $chapterNumber]).($urlQuery ? '?'.http_build_query($urlQuery) : '').'#hadith-'.$number
                    : '#';
            @endphp

            <a href="{{ $url }}" class="hadith-hit">
                <span class="hadith-hit__meta">
                    <span class="hadith-hit__chapter">
                        باب {{ $chapterNumber ?? '-' }}
                        @if ($chapter)
                            - {{ $chapter->titleUr() }}
                        @endif
                    </span>
                    <span class="hadith-hit__num" dir="ltr">#{{ $number }}</span>
                </span>
                <span class="hadith-hit__snippet">{{ $snippet !== '' ? $snippet : '-' }}</span>
                <span class="hadith-hit__cta">مکمل حدیث دیکھیں</span>
            </a>
        @endforeach
    </div>

    <div class="mt-10 hadith-pagination">
        {{ $hadiths->links() }}
    </div>
@endif

@if ($books->isEmpty())
    <div class="hadith-empty">
        <p class="font-display text-2xl font-bold text-teal-800">کوئی کتاب نہیں ملی</p>
        <p class="mt-2 text-muted">دوسرا لفظ آزما کر دیکھیں۔</p>
    </div>
@else
    <div class="hadith-book-grid">
        @foreach ($books as $book)
            <a href="{{ route('hadith.book', $book) }}" class="hadith-book-card">
                <div class="hadith-book-card__head">
                    <h2 class="hadith-book-card__title font-display">
                        <span class="hadith-book-card__ur">{{ $book->titleUr() }}</span>
                    </h2>
                    @if ($book->writer_name)
                        <p class="hadith-book-card__writer">{{ $book->writer_name }}</p>
                    @endif
                </div>

                <div class="hadith-book-card__stats">
                    <span class="hadith-book-card__stat">
                        <span class="hadith-book-card__stat-num" dir="ltr">{{ number_format($book->chapters_count) }}</span>
                        <span class="hadith-book-card__stat-label">ابواب</span>
                    </span>
                    <span class="hadith-book-card__stat">
                        <span class="hadith-book-card__stat-num" dir="ltr">{{ number_format($book->hadiths_count) }}</span>
                        <span class="hadith-book-card__stat-label">احادیث</span>
                    </span>
                </div>

                <span class="hadith-book-card__cta">
                    <span>ابواب دیکھیں</span>
                    @include('partials.icons.chevron-start', ['class' => 'hadith-book-card__cta-icon'])
                </span>
            </a>
        @endforeach
    </div>
@endif

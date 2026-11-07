@if ($chapters->isEmpty())
    <div class="hadith-empty">
        <p class="font-display text-2xl font-bold text-teal-800">کوئی باب نہیں ملا</p>
        <p class="mt-2 text-muted">دوسرا لفظ آزما کر دیکھیں۔</p>
    </div>
@else
    <div class="hadith-chapter-list">
        @foreach ($chapters as $chapter)
            <a href="{{ route('hadith.chapter', [$book, $chapter->chapter_number]) }}" class="hadith-chapter-card">
                <span class="hadith-chapter-card__num" dir="ltr">{{ $chapter->chapter_number }}</span>

                <div class="hadith-chapter-card__body">
                    <h2 class="hadith-chapter-card__title font-display">
                        <span class="hadith-chapter-card__ur">{{ $chapter->titleUr() }}</span>
                    </h2>
                </div>

                <span class="hadith-chapter-card__meta" dir="ltr">
                    @if ($chapter->hadith_number_from !== null && $chapter->hadith_number_to !== null)
                        <span class="hadith-chapter-card__meta-num">{{ $chapter->hadith_number_from }}</span>
                        <span class="hadith-chapter-card__meta-sep">–</span>
                        <span class="hadith-chapter-card__meta-num">{{ $chapter->hadith_number_to }}</span>
                    @else
                        <span class="hadith-chapter-card__meta-num">—</span>
                    @endif
                </span>

                <span class="hadith-chapter-card__arrow" aria-hidden="true">
                    @include('partials.icons.chevron-start')
                </span>
            </a>
        @endforeach
    </div>
@endif

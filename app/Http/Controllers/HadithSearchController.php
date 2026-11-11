<?php

namespace App\Http\Controllers;

use App\Models\Hadith;
use App\Models\HadithBook;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HadithSearchController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        $bookSlug = trim((string) $request->query('book', ''));
        $limit = min(8, max(1, (int) $request->query('limit', 5)));
        $type = $this->resolveType($request->query('type'), $q);

        if ($q === '' || mb_strlen($q) < 1) {
            return response()->json([
                'q' => $q,
                'book' => $bookSlug,
                'type' => $type,
                'total' => 0,
                'more_url' => null,
                'results' => [],
            ]);
        }

        $hidden = HadithBook::FRONTEND_HIDDEN_SLUGS;
        $allowedBook = $bookSlug !== ''
            && ! in_array($bookSlug, $hidden, true)
            && HadithBook::query()->where('book_slug', $bookSlug)->exists();

        if ($bookSlug !== '' && ! $allowedBook) {
            return response()->json([
                'q' => $q,
                'book' => $bookSlug,
                'type' => $type,
                'total' => 0,
                'more_url' => null,
                'results' => [],
            ]);
        }

        $query = Hadith::query()
            ->with([
                'book:id,book_name,book_slug',
                'chapter:id,hadith_book_id,chapter_number,chapter_urdu,chapter_arabic,chapter_english',
            ])
            ->whereHas('book', function ($bookQuery) use ($hidden, $bookSlug, $allowedBook) {
                $bookQuery->whereNotIn('book_slug', $hidden);
                if ($allowedBook) {
                    $bookQuery->where('book_slug', $bookSlug);
                }
            })
            ->whereHas('chapter');

        $this->applySearch($query, $q, $type);

        $total = (clone $query)->count();

        $rows = $query
            ->when($allowedBook, function ($builder) {
                $builder->orderBy(
                    \App\Models\HadithChapter::query()
                        ->select('chapter_number')
                        ->whereColumn('hadith_chapters.id', 'hadiths.hadith_chapter_id')
                        ->limit(1)
                );
            })
            ->orderByRaw('CAST(hadith_number AS UNSIGNED)')
            ->orderBy('id')
            ->limit($limit)
            ->get();

        $results = $rows->map(function (Hadith $hadith) use ($q, $type) {
            $book = $hadith->book;
            $chapter = $hadith->chapter;
            $slug = $book?->book_slug;
            $chapterNumber = $chapter?->chapter_number;

            if (! $slug || $chapterNumber === null) {
                return null;
            }

            $number = $hadith->hadith_number ?: (string) $hadith->id;
            $snippetSource = match ($type) {
                'arabic' => $hadith->hadith_arabic
                    ?: $hadith->heading_arabic
                    ?: $hadith->hadith_urdu
                    ?: $hadith->hadith_english
                    ?: '',
                'english' => $hadith->hadith_english
                    ?: $hadith->heading_english
                    ?: $hadith->english_narrator
                    ?: $hadith->hadith_urdu
                    ?: '',
                'number' => $hadith->hadith_urdu
                    ?: $hadith->hadith_arabic
                    ?: $hadith->hadith_english
                    ?: '',
                default => $hadith->hadith_urdu
                    ?: $hadith->heading_urdu
                    ?: $hadith->urdu_narrator
                    ?: $hadith->hadith_arabic
                    ?: $hadith->hadith_english
                    ?: '',
            };

            $page = $this->pageForHadithInChapter((int) $hadith->hadith_chapter_id, $hadith);
            $queryParams = array_filter([
                'page' => $page > 1 ? $page : null,
                'hn' => $number,
                'hq' => $q,
            ], fn ($value) => $value !== null && $value !== '');

            return [
                'id' => $hadith->id,
                'hadith_number' => $number,
                'snippet' => $this->makeSnippet((string) $snippetSource, $type === 'number' ? '' : $q),
                'book_title' => $book->titleUr(),
                'book_slug' => $slug,
                'chapter_number' => (int) $chapterNumber,
                'chapter_title' => $chapter->titleUr(),
                'page' => $page,
                'url' => route('hadith.chapter', [
                    'book' => $slug,
                    'chapter' => $chapterNumber,
                ]).($queryParams ? '?'.http_build_query($queryParams) : '').'#hadith-'.$number,
            ];
        })->filter()->values();

        $moreUrl = null;
        if ($allowedBook) {
            $moreUrl = route('hadith.search', ['book' => $bookSlug]).'?'.http_build_query([
                'q' => $q,
                'type' => $type === 'english' ? 'urdu' : $type,
                'hq' => $q,
            ]);
        }

        return response()->json([
            'q' => $q,
            'book' => $allowedBook ? $bookSlug : '',
            'type' => $type,
            'total' => $total,
            'more_url' => $moreUrl,
            'results' => $results,
        ]);
    }

    protected function resolveType(mixed $requested, string $q): string
    {
        $requested = strtolower(trim((string) $requested));
        if (in_array($requested, ['urdu', 'arabic', 'english', 'number'], true)) {
            return $requested;
        }

        if ($q !== '' && preg_match('/^\d+$/u', $q)) {
            return 'number';
        }

        if (preg_match('/[A-Za-z]/', $q) && ! preg_match('/\p{Arabic}/u', $q)) {
            return 'english';
        }

        if (preg_match('/\p{Arabic}/u', $q)) {
            if (preg_match('/[ٹڈڑںےھہ]/u', $q)) {
                return 'urdu';
            }

            if (preg_match('/[ةىإأآؤئ]/u', $q)) {
                return 'arabic';
            }

            return 'urdu';
        }

        return 'urdu';
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<\App\Models\Hadith>  $query
     */
    protected function applySearch($query, string $q, string $type): void
    {
        match ($type) {
            'number' => $query->where(function ($inner) use ($q) {
                $inner->where('hadith_number', $q)
                    ->orWhereRaw('CAST(hadith_number AS UNSIGNED) = ?', [(int) $q]);
            }),
            'arabic' => $query->where(function ($inner) use ($q) {
                $inner->where('hadith_arabic', 'like', "%{$q}%")
                    ->orWhere('heading_arabic', 'like', "%{$q}%");
            }),
            'english' => $query->where(function ($inner) use ($q) {
                $inner->where('hadith_english', 'like', "%{$q}%")
                    ->orWhere('english_narrator', 'like', "%{$q}%")
                    ->orWhere('heading_english', 'like', "%{$q}%");
            }),
            default => $query->where(function ($inner) use ($q) {
                $inner->where('hadith_urdu', 'like', "%{$q}%")
                    ->orWhere('urdu_narrator', 'like', "%{$q}%")
                    ->orWhere('heading_urdu', 'like', "%{$q}%");
            }),
        };
    }

    protected function makeSnippet(string $source, string $q, int $limit = 140): string
    {
        $text = Str::of($source)->stripTags()->squish()->toString();
        if ($text === '') {
            return '';
        }

        if ($q === '') {
            return Str::limit($text, $limit);
        }

        $pos = mb_stripos($text, $q);
        if ($pos === false) {
            return Str::limit($text, $limit);
        }

        $qLen = mb_strlen($q);
        $radius = (int) max(20, floor(($limit - $qLen) / 2));
        $start = max(0, $pos - $radius);
        $end = min(mb_strlen($text), $pos + $qLen + $radius);
        $slice = mb_substr($text, $start, $end - $start);

        if ($start > 0) {
            $slice = '…'.$slice;
        }
        if ($end < mb_strlen($text)) {
            $slice .= '…';
        }

        return $slice;
    }

    protected function pageForHadithInChapter(int $chapterId, Hadith $hadith, int $perPage = 12): int
    {
        $before = Hadith::query()
            ->where('hadith_chapter_id', $chapterId)
            ->where(function ($query) use ($hadith) {
                $num = (int) $hadith->hadith_number;
                $query->whereRaw('CAST(hadith_number AS UNSIGNED) < ?', [$num])
                    ->orWhere(function ($inner) use ($hadith, $num) {
                        $inner->whereRaw('CAST(hadith_number AS UNSIGNED) = ?', [$num])
                            ->where('id', '<', $hadith->id);
                    });
            })
            ->count();

        return intdiv($before, $perPage) + 1;
    }
}

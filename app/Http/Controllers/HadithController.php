<?php

namespace App\Http\Controllers;

use App\Models\Hadith;
use App\Models\HadithBook;
use App\Models\HadithChapter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class HadithController extends Controller
{
    public function index(Request $request): View|Response
    {
        $search = trim((string) $request->query('q', ''));

        $books = HadithBook::query()
            ->visibleOnFrontend()
            ->search($search)
            ->orderByDesc('hadiths_count')
            ->orderBy('id')
            ->get();

        if ($this->wantsPartial($request)) {
            return response()->view('hadith.partials.books-results', [
                'books' => $books,
            ]);
        }

        return view('hadith.index', [
            'books' => $books,
            'search' => $search,
            'solidHeader' => true,
        ]);
    }

    public function book(Request $request, HadithBook $book): View|Response
    {
        abort_if($book->isHiddenOnFrontend(), 404);

        $search = trim((string) $request->query('q', ''));

        $chapters = $book->chapters()
            ->addSelect([
                'hadith_number_from' => Hadith::query()
                    ->selectRaw('MIN(CAST(hadith_number AS UNSIGNED))')
                    ->whereColumn('hadiths.hadith_chapter_id', 'hadith_chapters.id'),
                'hadith_number_to' => Hadith::query()
                    ->selectRaw('MAX(CAST(hadith_number AS UNSIGNED))')
                    ->whereColumn('hadiths.hadith_chapter_id', 'hadith_chapters.id'),
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('chapter_urdu', 'like', "%{$search}%")
                        ->orWhere('chapter_english', 'like', "%{$search}%")
                        ->orWhere('chapter_arabic', 'like', "%{$search}%")
                        ->orWhere('chapter_number', 'like', "%{$search}%");
                });
            })
            ->get();

        if ($this->wantsPartial($request)) {
            return response()->view('hadith.partials.chapters-results', [
                'book' => $book,
                'chapters' => $chapters,
            ]);
        }

        return view('hadith.book', [
            'book' => $book,
            'chapters' => $chapters,
            'search' => $search,
            'solidHeader' => true,
        ]);
    }

    public function search(Request $request, HadithBook $book): View|Response
    {
        abort_if($book->isHiddenOnFrontend(), 404);

        $search = trim((string) $request->query('q', ''));
        $type = $this->resolveHadithSearchType($request->query('type'));
        $perPage = 12;

        $hadiths = Hadith::query()
            ->with([
                'chapter:id,hadith_book_id,chapter_number,chapter_urdu,chapter_arabic,chapter_english',
            ])
            ->whereHas('chapter', function ($query) use ($book) {
                $query->where('hadith_book_id', $book->id);
            })
            ->when($search !== '', function ($query) use ($search, $type) {
                $this->applyHadithTextSearch($query, $search, $type);
            }, function ($query) {
                $query->whereRaw('0 = 1');
            })
            ->orderBy(
                HadithChapter::query()
                    ->select('chapter_number')
                    ->whereColumn('hadith_chapters.id', 'hadiths.hadith_chapter_id')
                    ->limit(1)
            )
            ->orderByRaw('CAST(hadith_number AS UNSIGNED)')
            ->orderBy('id')
            ->paginate($perPage)
            ->withQueryString();

        if ($this->wantsPartial($request)) {
            return response()->view('hadith.partials.search-results', [
                'book' => $book,
                'hadiths' => $hadiths,
                'search' => $search,
                'searchType' => $type,
            ]);
        }

        return view('hadith.search', [
            'book' => $book,
            'hadiths' => $hadiths,
            'search' => $search,
            'searchType' => $type,
            'solidHeader' => true,
        ]);
    }

    public function chapter(Request $request, HadithBook $book, int $chapter): View|Response|RedirectResponse
    {
        abort_if($book->isHiddenOnFrontend(), 404);

        $chapterModel = HadithChapter::query()
            ->where('hadith_book_id', $book->id)
            ->where('chapter_number', $chapter)
            ->firstOrFail();

        $search = trim((string) $request->query('q', ''));
        $type = $this->resolveHadithSearchType($request->query('type'));
        $perPage = 12;

        $hadithNumber = trim((string) $request->query('hn', ''));
        if ($hadithNumber !== '' && ! $this->wantsPartial($request)) {
            $targetPage = $this->pageForHadithNumber($chapterModel->id, $hadithNumber, $perPage);
            $currentPage = max(1, (int) $request->query('page', 1));
            if ($targetPage !== null && $targetPage !== $currentPage) {
                return redirect()->to(
                    $request->fullUrlWithQuery(['page' => $targetPage]).'#hadith-'.$hadithNumber
                );
            }
        }

        $hadiths = Hadith::query()
            ->where('hadith_chapter_id', $chapterModel->id)
            ->when($search !== '', function ($query) use ($search, $type) {
                $this->applyHadithTextSearch($query, $search, $type);
            })
            ->orderByRaw('CAST(hadith_number AS UNSIGNED)')
            ->orderBy('id')
            ->paginate($perPage)
            ->withQueryString();

        if ($this->wantsPartial($request)) {
            return response()->view('hadith.partials.ahadith-results', [
                'book' => $book,
                'chapter' => $chapterModel,
                'hadiths' => $hadiths,
            ]);
        }

        return view('hadith.chapter', [
            'book' => $book,
            'chapter' => $chapterModel,
            'hadiths' => $hadiths,
            'search' => $search,
            'searchType' => $type,
            'solidHeader' => true,
        ]);
    }

    protected function pageForHadithNumber(int $chapterId, string $hadithNumber, int $perPage = 12): ?int
    {
        $hadith = Hadith::query()
            ->where('hadith_chapter_id', $chapterId)
            ->where(function ($query) use ($hadithNumber) {
                $query->where('hadith_number', $hadithNumber)
                    ->orWhereRaw('CAST(hadith_number AS UNSIGNED) = ?', [(int) $hadithNumber]);
            })
            ->orderBy('id')
            ->first();

        if (! $hadith) {
            return null;
        }

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

    protected function resolveHadithSearchType(mixed $type): string
    {
        $type = strtolower(trim((string) $type));

        return in_array($type, ['urdu', 'arabic', 'number'], true) ? $type : 'urdu';
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<\App\Models\Hadith>  $query
     */
    protected function applyHadithTextSearch($query, string $search, string $type): void
    {
        match ($type) {
            'number' => $query->where(function ($q) use ($search) {
                $q->where('hadith_number', $search)
                    ->orWhereRaw('CAST(hadith_number AS UNSIGNED) = ?', [(int) $search]);
            }),
            'arabic' => $query->where(function ($q) use ($search) {
                $q->where('hadith_arabic', 'like', "%{$search}%")
                    ->orWhere('heading_arabic', 'like', "%{$search}%");
            }),
            default => $query->where(function ($q) use ($search) {
                $q->where('hadith_urdu', 'like', "%{$search}%")
                    ->orWhere('urdu_narrator', 'like', "%{$search}%")
                    ->orWhere('heading_urdu', 'like', "%{$search}%");
            }),
        };
    }

    protected function wantsPartial(Request $request): bool
    {
        return $request->boolean('partial')
            || $request->ajax()
            || $request->header('X-Hadith-Search') === '1';
    }
}

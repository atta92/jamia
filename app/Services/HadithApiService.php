<?php

namespace App\Services;

use App\Models\Hadith;
use App\Models\HadithBook;
use App\Models\HadithChapter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class HadithApiService
{
    public function __construct(
        protected ?string $apiKey = null,
        protected ?string $baseUrl = null,
    ) {
        $this->apiKey ??= config('hadith.api_key');
        $this->baseUrl ??= rtrim((string) config('hadith.base_url'), '/');
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function fetchBooks(): array
    {
        $payload = $this->get('/books');

        return $payload['books'] ?? [];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function fetchChapters(string $bookSlug): array
    {
        $payload = $this->get('/'.$bookSlug.'/chapters');

        return $payload['chapters'] ?? [];
    }

    /**
     * @return array{data: list<array<string, mixed>>, current_page: int, last_page: int, total: int}
     */
    public function fetchHadithsPage(string $bookSlug, int $page = 1, int $paginate = 200): array
    {
        $payload = $this->get('/hadiths', [
            'book' => $bookSlug,
            'paginate' => $paginate,
            'page' => $page,
        ]);

        $hadiths = $payload['hadiths'] ?? [];

        return [
            'data' => $hadiths['data'] ?? [],
            'current_page' => (int) ($hadiths['current_page'] ?? 1),
            'last_page' => (int) ($hadiths['last_page'] ?? 1),
            'total' => (int) ($hadiths['total'] ?? 0),
        ];
    }

    public function importBooks(): int
    {
        $count = 0;

        foreach ($this->fetchBooks() as $book) {
            HadithBook::query()->updateOrCreate(
                ['api_id' => $book['id']],
                [
                    'book_name' => $book['bookName'] ?? '',
                    'book_slug' => $book['bookSlug'] ?? '',
                    'writer_name' => $book['writerName'] ?? null,
                    'about_writer' => $book['aboutWriter'] ?? null,
                    'writer_death' => $book['writerDeath'] ?? null,
                    'hadiths_count' => (int) ($book['hadiths_count'] ?? 0),
                    'chapters_count' => (int) ($book['chapters_count'] ?? 0),
                ]
            );
            $count++;
        }

        return $count;
    }

    public function importChapters(?callable $onBook = null): int
    {
        $count = 0;

        foreach (HadithBook::query()->orderBy('id')->get() as $book) {
            if ($onBook) {
                $onBook($book);
            }

            foreach ($this->fetchChapters($book->book_slug) as $chapter) {
                HadithChapter::query()->updateOrCreate(
                    [
                        'book_slug' => $book->book_slug,
                        'api_id' => $chapter['id'],
                    ],
                    [
                        'hadith_book_id' => $book->id,
                        'chapter_number' => (int) ($chapter['chapterNumber'] ?? 0),
                        'chapter_english' => $chapter['chapterEnglish'] ?? null,
                        'chapter_urdu' => $chapter['chapterUrdu'] ?? null,
                        'chapter_arabic' => $chapter['chapterArabic'] ?? null,
                    ]
                );
                $count++;
            }

            $this->throttle();
        }

        return $count;
    }

    public function importHadiths(?callable $onProgress = null): int
    {
        $count = 0;
        $paginate = (int) config('hadith.paginate', 200);

        $books = HadithBook::query()
            ->where('hadiths_count', '>', 0)
            ->orderBy('id')
            ->get();

        foreach ($books as $book) {
            $chaptersByApiId = HadithChapter::query()
                ->where('hadith_book_id', $book->id)
                ->pluck('id', 'api_id')
                ->all();

            // Some API books send chapterNumber in chapterId (not the global chapter api id).
            $chaptersByNumber = HadithChapter::query()
                ->where('hadith_book_id', $book->id)
                ->pluck('id', 'chapter_number')
                ->all();

            $page = 1;
            $lastPage = 1;

            do {
                $result = $this->fetchHadithsPage($book->book_slug, $page, $paginate);
                $lastPage = max(1, $result['last_page']);

                foreach ($result['data'] as $item) {
                    $chapterApiId = array_key_exists('chapterId', $item) && $item['chapterId'] !== null
                        ? (int) $item['chapterId']
                        : null;

                    $hadithChapterId = $chapterApiId === null
                        ? null
                        : ($chaptersByApiId[$chapterApiId] ?? $chaptersByNumber[$chapterApiId] ?? null);

                    Hadith::query()->updateOrCreate(
                        ['api_id' => $item['id']],
                        [
                            'hadith_book_id' => $book->id,
                            'hadith_chapter_id' => $hadithChapterId,
                            'hadith_number' => isset($item['hadithNumber']) ? (string) $item['hadithNumber'] : null,
                            'english_narrator' => $item['englishNarrator'] ?? null,
                            'hadith_english' => $item['hadithEnglish'] ?? null,
                            'hadith_urdu' => $item['hadithUrdu'] ?? null,
                            'urdu_narrator' => $item['urduNarrator'] ?? null,
                            'hadith_arabic' => $item['hadithArabic'] ?? null,
                            'heading_arabic' => $item['headingArabic'] ?? null,
                            'heading_urdu' => $item['headingUrdu'] ?? null,
                            'heading_english' => $item['headingEnglish'] ?? null,
                            'book_slug' => $item['bookSlug'] ?? $book->book_slug,
                            'chapter_api_id' => $chapterApiId,
                            'volume' => isset($item['volume']) ? (string) $item['volume'] : null,
                            'status' => $item['status'] ?? null,
                        ]
                    );
                    $count++;
                }

                if ($onProgress) {
                    $onProgress($book, $page, $lastPage, $count);
                }

                $page++;
                $this->throttle();
            } while ($page <= $lastPage);
        }

        return $count;
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    protected function get(string $path, array $query = []): array
    {
        if (blank($this->apiKey)) {
            throw new RuntimeException('HADITH_API_KEY is missing.');
        }

        $response = Http::timeout(60)
            ->retry(3, 500)
            ->acceptJson()
            ->get($this->baseUrl.$path, array_merge($query, [
                'apiKey' => $this->apiKey,
            ]));

        if (! $response->successful()) {
            Log::error('Hadith API error', [
                'path' => $path,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new RuntimeException('Hadith API request failed: HTTP '.$response->status());
        }

        $payload = $response->json();

        if (($payload['status'] ?? null) !== 200) {
            throw new RuntimeException('Hadith API returned status: '.($payload['status'] ?? 'unknown'));
        }

        return is_array($payload) ? $payload : [];
    }

    protected function throttle(): void
    {
        $ms = (int) config('hadith.request_delay_ms', 150);

        if ($ms > 0) {
            usleep($ms * 1000);
        }
    }
}

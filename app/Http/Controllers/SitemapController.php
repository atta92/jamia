<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Event;
use App\Models\HadithBook;
use App\Models\HadithChapter;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = [];

        $push = function (string $loc, ?string $lastmod = null, string $changefreq = 'weekly', string $priority = '0.7') use (&$urls): void {
            $urls[] = [
                'loc' => $loc,
                'lastmod' => $lastmod,
                'changefreq' => $changefreq,
                'priority' => $priority,
            ];
        };

        $push(route('home'), now()->toAtomString(), 'daily', '1.0');
        $push(route('about'), null, 'monthly', '0.8');
        $push(route('contact'), null, 'monthly', '0.7');
        $push(route('hifz.show'), null, 'weekly', '0.8');
        $push(route('gallery.photos'), null, 'weekly', '0.7');
        $push(route('gallery.videos'), null, 'weekly', '0.7');
        $push(route('blog.index'), null, 'daily', '0.8');
        $push(route('events.index'), null, 'weekly', '0.8');
        $push(route('hadith.index'), null, 'weekly', '0.8');

        BlogPost::query()
            ->published()
            ->orderByDesc('updated_at')
            ->get(['slug', 'updated_at', 'published_at'])
            ->each(function (BlogPost $post) use ($push): void {
                $push(
                    route('blog.show', $post->slug),
                    optional($post->updated_at ?: $post->published_at)->toAtomString(),
                    'monthly',
                    '0.7',
                );
            });

        Event::query()
            ->published()
            ->orderByDesc('updated_at')
            ->get(['slug', 'updated_at', 'starts_at'])
            ->each(function (Event $event) use ($push): void {
                $push(
                    route('events.show', $event->slug),
                    optional($event->updated_at ?: $event->starts_at)->toAtomString(),
                    'weekly',
                    '0.7',
                );
            });

        HadithBook::query()
            ->visibleOnFrontend()
            ->orderBy('id')
            ->get(['book_slug', 'updated_at'])
            ->each(function (HadithBook $book) use ($push): void {
                $push(
                    route('hadith.book', $book->book_slug),
                    optional($book->updated_at)->toAtomString(),
                    'monthly',
                    '0.6',
                );
            });

        // Limit chapters so the sitemap stays manageable.
        HadithChapter::query()
            ->whereHas('book', fn ($q) => $q->visibleOnFrontend())
            ->with('book:id,book_slug')
            ->orderBy('id')
            ->limit(2000)
            ->get(['id', 'hadith_book_id', 'chapter_number', 'updated_at'])
            ->each(function (HadithChapter $chapter) use ($push): void {
                if (! $chapter->book?->book_slug) {
                    return;
                }

                $push(
                    route('hadith.chapter', [
                        'book' => $chapter->book->book_slug,
                        'chapter' => $chapter->chapter_number,
                    ]),
                    optional($chapter->updated_at)->toAtomString(),
                    'monthly',
                    '0.5',
                );
            });

        $xml = view('seo.sitemap', ['urls' => $urls])->render();

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }
}

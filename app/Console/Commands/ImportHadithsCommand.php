<?php

namespace App\Console\Commands;

use App\Services\HadithApiService;
use Illuminate\Console\Command;

class ImportHadithsCommand extends Command
{
    protected $signature = 'hadith:import
        {--books-only : Import only books}
        {--chapters-only : Import only chapters}
        {--hadiths-only : Import only hadiths}';

    protected $description = 'Import books, chapters, and hadiths from HadithAPI into local tables';

    public function handle(HadithApiService $api): int
    {
        if (blank(config('hadith.api_key'))) {
            $this->error('Set HADITH_API_KEY in your .env file first.');

            return self::FAILURE;
        }

        $booksOnly = (bool) $this->option('books-only');
        $chaptersOnly = (bool) $this->option('chapters-only');
        $hadithsOnly = (bool) $this->option('hadiths-only');
        $all = ! $booksOnly && ! $chaptersOnly && ! $hadithsOnly;

        if ($all || $booksOnly) {
            $this->info('Importing books...');
            $books = $api->importBooks();
            $this->info("Books imported/updated: {$books}");
        }

        if ($all || $chaptersOnly) {
            $this->info('Importing chapters...');
            $chapters = $api->importChapters(function ($book) {
                $this->line('  Chapters for: '.$book->book_name);
            });
            $this->info("Chapters imported/updated: {$chapters}");
        }

        if ($all || $hadithsOnly) {
            $this->info('Importing hadiths (this can take several minutes)...');
            $hadiths = $api->importHadiths(function ($book, $page, $lastPage, $total) {
                $this->line(sprintf(
                    '  %s — page %d/%d (saved so far: %d)',
                    $book->book_name,
                    $page,
                    $lastPage,
                    $total
                ));
            });
            $this->info("Hadiths imported/updated: {$hadiths}");
        }

        $this->newLine();
        $this->info('Done.');

        return self::SUCCESS;
    }
}

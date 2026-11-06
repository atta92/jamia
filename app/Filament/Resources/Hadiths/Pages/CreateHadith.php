<?php

namespace App\Filament\Resources\Hadiths\Pages;

use App\Filament\Resources\Hadiths\HadithResource;
use App\Models\Hadith;
use App\Models\HadithBook;
use App\Models\HadithChapter;
use Filament\Resources\Pages\CreateRecord;

class CreateHadith extends CreateRecord
{
    protected static string $resource = HadithResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['api_id'] = ((int) Hadith::query()->max('api_id')) + 1;

        if (! empty($data['hadith_book_id'])) {
            $book = HadithBook::query()->find($data['hadith_book_id']);
            $data['book_slug'] = $book?->book_slug;
        }

        if (! empty($data['hadith_chapter_id'])) {
            $chapter = HadithChapter::query()->find($data['hadith_chapter_id']);
            $data['chapter_api_id'] = $chapter?->api_id;
        }

        return $data;
    }
}

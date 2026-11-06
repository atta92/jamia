<?php

namespace App\Filament\Resources\Hadiths\Pages;

use App\Filament\Resources\Hadiths\HadithResource;
use App\Models\HadithBook;
use App\Models\HadithChapter;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHadith extends EditRecord
{
    protected static string $resource = HadithResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('viewPublic')
                ->label('سائٹ پر دیکھیں')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(function (): ?string {
                    $hadith = $this->getRecord();
                    $book = $hadith->book;
                    $chapter = $hadith->chapter;

                    if (! $book || ! $chapter) {
                        return null;
                    }

                    return route('hadith.chapter', [
                        'book' => $book,
                        'chapter' => $chapter->chapter_number,
                    ]).'#hadith-'.($hadith->hadith_number ?: $hadith->id);
                })
                ->openUrlInNewTab()
                ->visible(fn (): bool => filled($this->getRecord()->book) && filled($this->getRecord()->chapter)),
            DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! empty($data['hadith_book_id'])) {
            $book = HadithBook::query()->find($data['hadith_book_id']);
            $data['book_slug'] = $book?->book_slug;
        }

        if (! empty($data['hadith_chapter_id'])) {
            $chapter = HadithChapter::query()->find($data['hadith_chapter_id']);
            $data['chapter_api_id'] = $chapter?->api_id;
        } else {
            $data['chapter_api_id'] = null;
        }

        return $data;
    }
}

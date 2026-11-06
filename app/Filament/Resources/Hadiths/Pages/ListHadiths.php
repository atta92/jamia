<?php

namespace App\Filament\Resources\Hadiths\Pages;

use App\Filament\Resources\Hadiths\HadithResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\Table;
use Filament\Tables\View\TablesRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;

class ListHadiths extends ListRecords
{
    protected static string $resource = HadithResource::class;

    #[Url(as: 'type')]
    public string $searchType = 'urdu';

    public function boot(): void
    {
        FilamentView::registerRenderHook(
            TablesRenderHook::TOOLBAR_SEARCH_BEFORE,
            fn (): View => view('filament.hadiths.search-type-select'),
            scopes: static::class,
        );
    }

    public function table(Table $table): Table
    {
        return $table
            ->searchable()
            ->searchDebounce('400ms')
            ->searchPlaceholder(fn (): string => $this->searchPlaceholderForType())
            ->searchUsing(function (Builder $query, string $search): void {
                $this->applyTypedHadithSearch($query, $search);
            });
    }

    public function updatedSearchType(): void
    {
        $this->searchType = $this->resolveSearchType($this->searchType);

        if ($this->getTable()->shouldDeselectAllRecordsWhenFiltered()) {
            $this->deselectAllTableRecords();
        }

        $this->resetPage();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('نئی حدیث'),
        ];
    }

    protected function searchPlaceholderForType(): string
    {
        return match ($this->resolveSearchType($this->searchType)) {
            'arabic' => 'عربی متن لکھیں…',
            'number' => 'حدیث نمبر…',
            default => 'اردو متن لکھیں…',
        };
    }

    protected function applyTypedHadithSearch(Builder $query, string $search): void
    {
        $search = trim($search);

        if ($search === '') {
            return;
        }

        $like = '%'.$search.'%';

        match ($this->resolveSearchType($this->searchType)) {
            'number' => $query->where('hadith_number', 'like', $like),
            'arabic' => $query->where(function (Builder $inner) use ($like): void {
                $inner->where('hadith_arabic', 'like', $like)
                    ->orWhere('heading_arabic', 'like', $like);
            }),
            default => $query->where(function (Builder $inner) use ($like): void {
                $inner->where('hadith_urdu', 'like', $like)
                    ->orWhere('urdu_narrator', 'like', $like)
                    ->orWhere('heading_urdu', 'like', $like);
            }),
        };
    }

    protected function resolveSearchType(mixed $type): string
    {
        $type = strtolower(trim((string) $type));

        return in_array($type, ['urdu', 'arabic', 'number'], true) ? $type : 'urdu';
    }
}

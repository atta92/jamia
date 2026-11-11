<?php

namespace App\Filament\Pages;

use App\Models\Hadith;
use App\Models\HadithBook;
use App\Models\HadithChapter;
use App\Models\SiteSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use UnitEnum;

/**
 * @property-read Schema $form
 */
class ManageDailyHadith extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?string $navigationLabel = "Today's Hadith";

    protected static ?string $title = "Today's Hadith";

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.manage-site-settings';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $settings = SiteSetting::current();
        $hadith = $settings->daily_hadith_id
            ? Hadith::query()->find($settings->daily_hadith_id)
            : null;

        $this->form->fill([
            'show_daily_hadith' => $settings->show_daily_hadith,
            'filter_book_id' => $hadith?->hadith_book_id,
            'filter_chapter_id' => $hadith?->hadith_chapter_id,
            'daily_hadith_id' => $settings->daily_hadith_id,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Homepage hadith card')
                    ->description('Pick a book, then a chapter, then the hadith. Search by number if you already know it.')
                    ->schema([
                        Toggle::make('show_daily_hadith')
                            ->label('Show on hero')
                            ->default(true),

                        Select::make('filter_book_id')
                            ->label('1. Book')
                            ->options(
                                fn (): array => HadithBook::query()
                                    ->orderBy('id')
                                    ->get()
                                    ->mapWithKeys(fn (HadithBook $book): array => [
                                        $book->id => $book->titleUr().' ('.$book->book_name.')',
                                    ])
                                    ->all()
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->dehydrated(false)
                            ->afterStateUpdated(function (callable $set): void {
                                $set('filter_chapter_id', null);
                                $set('daily_hadith_id', null);
                            })
                            ->helperText('Start here - e.g. صحیح بخاری'),

                        Select::make('filter_chapter_id')
                            ->label('2. Chapter (باب)')
                            ->options(function (callable $get): array {
                                $bookId = $get('filter_book_id');
                                if (blank($bookId)) {
                                    return [];
                                }

                                return HadithChapter::query()
                                    ->where('hadith_book_id', $bookId)
                                    ->orderBy('chapter_number')
                                    ->get()
                                    ->mapWithKeys(fn (HadithChapter $chapter): array => [
                                        $chapter->id => $chapter->chapter_number.' - '.$chapter->titleUr(),
                                    ])
                                    ->all();
                            })
                            ->searchable()
                            ->preload()
                            ->live()
                            ->dehydrated(false)
                            ->disabled(fn (callable $get): bool => blank($get('filter_book_id')))
                            ->afterStateUpdated(fn (callable $set) => $set('daily_hadith_id', null))
                            ->helperText('Optional, but narrows the list a lot'),

                        Select::make('daily_hadith_id')
                            ->label('3. Hadith')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->disabled(fn (callable $get): bool => blank($get('filter_book_id')))
                            ->options(fn (callable $get): array => static::hadithOptions(
                                bookId: $get('filter_book_id'),
                                chapterId: $get('filter_chapter_id'),
                            ))
                            ->getSearchResultsUsing(fn (string $search, callable $get): array => static::hadithOptions(
                                bookId: $get('filter_book_id'),
                                chapterId: $get('filter_chapter_id'),
                                search: $search,
                            ))
                            ->getOptionLabelUsing(function ($value): ?string {
                                $hadith = Hadith::query()->with(['book', 'chapter'])->find($value);

                                return $hadith ? static::hadithOptionLabel($hadith) : null;
                            })
                            ->live()
                            ->helperText('Type a hadith number (e.g. 1) or words from the text'),

                        Placeholder::make('hadith_preview')
                            ->label('Preview')
                            ->content(function (callable $get): HtmlString {
                                $id = $get('daily_hadith_id');
                                if (blank($id)) {
                                    return new HtmlString(
                                        '<span class="text-gray-500">Select a hadith to preview Urdu / Arabic text here.</span>'
                                    );
                                }

                                $hadith = Hadith::query()->with(['book', 'chapter'])->find($id);
                                if (! $hadith) {
                                    return new HtmlString('<span class="text-danger-600">Hadith not found.</span>');
                                }

                                $book = e($hadith->book?->titleUr() ?: ($hadith->book?->book_name ?: $hadith->book_slug));
                                $chapter = $hadith->chapter?->titleUr();
                                $number = e((string) ($hadith->hadith_number ?: $hadith->api_id));
                                $urdu = e(str($hadith->hadith_urdu ?: '')->stripTags()->limit(280));
                                $arabic = e(str($hadith->hadith_arabic ?: '')->stripTags()->limit(180));

                                $html = '<div dir="rtl" style="line-height:1.7">'
                                    .'<div style="font-weight:700;margin-bottom:0.35rem">'.$book.' · حدیث نمبر '.$number.'</div>';

                                if ($chapter) {
                                    $html .= '<div style="opacity:0.75;margin-bottom:0.65rem">باب: '.e($chapter).'</div>';
                                }
                                if ($arabic !== '') {
                                    $html .= '<div style="margin-bottom:0.65rem;font-size:1.05rem">'.$arabic.'</div>';
                                }
                                if ($urdu !== '') {
                                    $html .= '<div>'.$urdu.'</div>';
                                }

                                $html .= '</div>';

                                return new HtmlString($html);
                            }),
                    ]),
            ])
            ->statePath('data')
            ->model(SiteSetting::current());
    }

    public function save(): void
    {
        $data = $this->form->getState();

        SiteSetting::current()->update([
            'show_daily_hadith' => (bool) ($data['show_daily_hadith'] ?? false),
            'daily_hadith_id' => $data['daily_hadith_id'] ?? null,
        ]);

        Notification::make()
            ->title('Hadith settings saved')
            ->success()
            ->send();
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Save changes')
                                ->submit('save')
                                ->keyBindings(['mod+s']),
                        ]),
                    ]),
            ]);
    }

    /**
     * @return array<int|string, string>
     */
    protected static function hadithOptions(mixed $bookId = null, mixed $chapterId = null, ?string $search = null): array
    {
        if (blank($bookId)) {
            return [];
        }

        $query = Hadith::query()
            ->with(['book', 'chapter'])
            ->where('hadith_book_id', $bookId);

        if (filled($chapterId)) {
            $query->where('hadith_chapter_id', $chapterId);
        }

        $search = trim((string) $search);
        if ($search !== '') {
            $query->where(function (Builder $q) use ($search): void {
                $q->where('hadith_number', 'like', "%{$search}%")
                    ->orWhere('api_id', 'like', "%{$search}%")
                    ->orWhere('hadith_urdu', 'like', "%{$search}%")
                    ->orWhere('hadith_english', 'like', "%{$search}%")
                    ->orWhere('hadith_arabic', 'like', "%{$search}%")
                    ->orWhere('heading_urdu', 'like', "%{$search}%");
            });
        }

        return $query
            ->orderBy('id')
            ->limit($search !== '' ? 40 : 80)
            ->get()
            ->mapWithKeys(fn (Hadith $hadith): array => [
                $hadith->id => static::hadithOptionLabel($hadith),
            ])
            ->all();
    }

    protected static function hadithOptionLabel(Hadith $hadith): string
    {
        $number = $hadith->hadith_number ?: $hadith->api_id;
        $chapterNo = $hadith->chapter?->chapter_number;
        $snippet = str($hadith->hadith_urdu ?: $hadith->hadith_english ?: $hadith->heading_urdu ?: '')
            ->stripTags()
            ->limit(50);

        $prefix = $chapterNo ? "باب {$chapterNo} · #{$number}" : "#{$number}";

        return trim("{$prefix} - {$snippet}");
    }
}

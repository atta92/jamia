<?php

namespace App\Filament\Resources\Hadiths\Schemas;

use App\Models\HadithBook;
use App\Models\HadithChapter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HadithForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('مقام و نمبر')
                    ->description('کتاب، باب اور حدیث نمبر تبدیل کریں۔')
                    ->columns(2)
                    ->schema([
                        Select::make('hadith_book_id')
                            ->label('کتاب')
                            ->relationship('book', 'book_name')
                            ->getOptionLabelFromRecordUsing(
                                fn (HadithBook $record): string => $record->titleUr().' ('.$record->book_name.')'
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set): void {
                                $set('hadith_chapter_id', null);
                                $set('book_slug', HadithBook::query()->find($state)?->book_slug);
                            }),
                        Select::make('hadith_chapter_id')
                            ->label('باب')
                            ->options(function (callable $get): array {
                                $bookId = $get('hadith_book_id');

                                if (blank($bookId)) {
                                    return [];
                                }

                                return HadithChapter::query()
                                    ->where('hadith_book_id', $bookId)
                                    ->orderBy('chapter_number')
                                    ->get()
                                    ->mapWithKeys(fn (HadithChapter $chapter): array => [
                                        $chapter->id => $chapter->chapter_number.' — '.$chapter->titleUr(),
                                    ])
                                    ->all();
                            })
                            ->searchable()
                            ->nullable()
                            ->disabled(fn (callable $get): bool => blank($get('hadith_book_id')))
                            ->afterStateUpdated(function ($state, callable $set): void {
                                $chapter = HadithChapter::query()->find($state);
                                $set('chapter_api_id', $chapter?->api_id);
                            }),
                        TextInput::make('hadith_number')
                            ->label('حدیث نمبر')
                            ->required()
                            ->maxLength(50)
                            ->helperText('جیسے 1، 1a، 2552'),
                        TextInput::make('volume')
                            ->label('جلد')
                            ->maxLength(50),
                        TextInput::make('status')
                            ->label('درجہ / Status')
                            ->maxLength(100)
                            ->placeholder('Sahih / Hasan / …'),
                        TextInput::make('book_slug')
                            ->label('Book slug')
                            ->disabled()
                            ->dehydrated()
                            ->maxLength(255),
                    ]),

                Section::make('عناوین')
                    ->columns(1)
                    ->schema([
                        Textarea::make('heading_urdu')
                            ->label('عنوان اردو')
                            ->rows(2)
                            ->extraAttributes(['dir' => 'rtl', 'lang' => 'ur']),
                        Textarea::make('heading_arabic')
                            ->label('عنوان عربی')
                            ->rows(2)
                            ->extraAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Textarea::make('heading_english')
                            ->label('Heading (English)')
                            ->rows(2)
                            ->extraAttributes(['dir' => 'ltr', 'lang' => 'en']),
                    ]),

                Section::make('متنِ حدیث')
                    ->description('عربی متن، اردو ترجمہ اور انگریزی متن۔')
                    ->schema([
                        Textarea::make('hadith_arabic')
                            ->label('متن عربی')
                            ->rows(8)
                            ->extraAttributes(['dir' => 'rtl', 'lang' => 'ar'])
                            ->columnSpanFull(),
                        Textarea::make('hadith_urdu')
                            ->label('اردو متن / ترجمہ')
                            ->rows(8)
                            ->extraAttributes(['dir' => 'rtl', 'lang' => 'ur'])
                            ->columnSpanFull(),
                        Textarea::make('hadith_english')
                            ->label('English text')
                            ->rows(6)
                            ->extraAttributes(['dir' => 'ltr', 'lang' => 'en'])
                            ->columnSpanFull(),
                    ]),

                Section::make('راوی')
                    ->columns(1)
                    ->schema([
                        Textarea::make('urdu_narrator')
                            ->label('راوی اردو')
                            ->rows(3)
                            ->extraAttributes(['dir' => 'rtl', 'lang' => 'ur']),
                        Textarea::make('english_narrator')
                            ->label('Narrator (English)')
                            ->rows(3)
                            ->extraAttributes(['dir' => 'ltr', 'lang' => 'en']),
                    ]),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Hadiths\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HadithsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id')
            ->columns([
                TextColumn::make('hadith_number')
                    ->label('نمبر')
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('book.book_name')
                    ->label('کتاب')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('chapter.chapter_number')
                    ->label('باب')
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('hadith_urdu')
                    ->label('اردو متن')
                    ->limit(55)
                    ->tooltip(fn ($record): ?string => $record->hadith_urdu)
                    ->placeholder('—'),
                TextColumn::make('hadith_arabic')
                    ->label('عربی متن')
                    ->limit(40)
                    ->tooltip(fn ($record): ?string => $record->hadith_arabic)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('—'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('hadith_book_id')
                    ->label('کتاب')
                    ->relationship('book', 'book_name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('hadith_chapter_id')
                    ->label('باب')
                    ->relationship(
                        name: 'chapter',
                        titleAttribute: 'chapter_urdu',
                        modifyQueryUsing: fn (Builder $query): Builder => $query->orderBy('chapter_number'),
                    )
                    ->getOptionLabelFromRecordUsing(
                        fn ($record): string => $record->chapter_number.' — '.($record->chapter_urdu ?: $record->chapter_english ?: 'باب')
                    )
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(fn (): array => \App\Models\Hadith::query()
                        ->whereNotNull('status')
                        ->where('status', '!=', '')
                        ->distinct()
                        ->orderBy('status')
                        ->limit(50)
                        ->pluck('status', 'status')
                        ->all()),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

<?php

namespace App\Filament\Resources\HadithErrorReports\Tables;

use App\Models\HadithErrorReport;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class HadithErrorReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('hadith.hadith_number')
                    ->label('Hadith #')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('hadith.book.book_name')
                    ->label('Book')
                    ->toggleable()
                    ->limit(24),
                TextColumn::make('error_type')
                    ->label('Type')
                    ->formatStateUsing(fn (?string $state): string => HadithErrorReport::errorTypes()[$state] ?? (string) $state)
                    ->badge()
                    ->wrap(),
                TextColumn::make('details')
                    ->label('Details')
                    ->limit(40)
                    ->toggleable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        HadithErrorReport::STATUS_FIXED => 'success',
                        HadithErrorReport::STATUS_REVIEWED => 'info',
                        HadithErrorReport::STATUS_DISMISSED => 'gray',
                        default => 'warning',
                    }),
                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        HadithErrorReport::STATUS_PENDING => 'Pending',
                        HadithErrorReport::STATUS_REVIEWED => 'Reviewed',
                        HadithErrorReport::STATUS_FIXED => 'Fixed',
                        HadithErrorReport::STATUS_DISMISSED => 'Dismissed',
                    ]),
                SelectFilter::make('error_type')
                    ->label('Type')
                    ->options(HadithErrorReport::errorTypes()),
            ])
            ->recordActions([
                EditAction::make()->label('Review'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

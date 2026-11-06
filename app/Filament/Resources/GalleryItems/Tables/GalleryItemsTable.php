<?php

namespace App\Filament\Resources\GalleryItems\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class GalleryItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('image')
                    ->label('Image')
                    ->disk('public')
                    ->square()
                    ->size(56),
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->placeholder('—')
                    ->limit(40),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'video' ? 'Video' : 'Photo'),
                TextColumn::make('year')
                    ->label('Year')
                    ->sortable(),
                TextColumn::make('category')
                    ->label('Category')
                    ->placeholder('—')
                    ->toggleable(),
                ToggleColumn::make('is_published')
                    ->label('Published'),
                ToggleColumn::make('show_on_home')
                    ->label('Home'),
                TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'photo' => 'Photo',
                        'video' => 'Video',
                    ]),
                SelectFilter::make('year')
                    ->options(fn () => \App\Models\GalleryItem::query()
                        ->whereNotNull('year')
                        ->distinct()
                        ->orderByDesc('year')
                        ->pluck('year', 'year')
                        ->all()),
                TernaryFilter::make('is_published')
                    ->label('Published'),
                TernaryFilter::make('show_on_home')
                    ->label('On homepage'),
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

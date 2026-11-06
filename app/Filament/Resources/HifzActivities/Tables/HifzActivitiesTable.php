<?php

namespace App\Filament\Resources\HifzActivities\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class HifzActivitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Image')
                    ->disk('public')
                    ->square(),
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('caption')
                    ->label('Caption')
                    ->limit(40)
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),
                ToggleColumn::make('is_published')
                    ->label('Published'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
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

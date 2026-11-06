<?php

namespace App\Filament\Resources\Events\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class EventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('starts_at', 'asc')
            ->columns([
                ImageColumn::make('cover_image')
                    ->label('Cover')
                    ->disk('public')
                    ->square()
                    ->size(48),
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                TextColumn::make('location')
                    ->label('Location')
                    ->placeholder('—')
                    ->limit(24),
                TextColumn::make('starts_at')
                    ->label('Starts')
                    ->dateTime('d M Y')
                    ->sortable(),
                ToggleColumn::make('is_published')
                    ->label('Live'),
                ToggleColumn::make('show_on_home')
                    ->label('Home'),
            ])
            ->filters([
                TernaryFilter::make('is_published')->label('Published'),
                TernaryFilter::make('show_on_home')->label('On homepage'),
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

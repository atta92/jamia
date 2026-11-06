<?php

namespace App\Filament\Resources\BlogPosts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class BlogPostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
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
                TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->placeholder('—'),
                TextColumn::make('published_at')
                    ->label('Published')
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

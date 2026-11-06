<?php

namespace App\Filament\Resources\GalleryItems\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GalleryItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Gallery item')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Title')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        FileUpload::make('image')
                            ->label('Image')
                            ->image()
                            ->imageEditor()
                            ->directory('gallery')
                            ->disk('public')
                            ->required()
                            ->columnSpanFull(),
                        Select::make('type')
                            ->label('Type')
                            ->options([
                                'photo' => 'Photo',
                                'video' => 'Video',
                            ])
                            ->default('photo')
                            ->required()
                            ->live(),
                        TextInput::make('video_url')
                            ->label('Video URL')
                            ->url()
                            ->maxLength(500)
                            ->visible(fn ($get) => $get('type') === 'video')
                            ->helperText('YouTube or direct video link (optional).'),
                        TextInput::make('year')
                            ->label('Year')
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(2100)
                            ->default((int) date('Y')),
                        TextInput::make('category')
                            ->label('Category')
                            ->maxLength(100)
                            ->placeholder('e.g. events, classes'),
                        TextInput::make('sort_order')
                            ->label('Sort order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower numbers appear first.'),
                        Toggle::make('is_published')
                            ->label('Published')
                            ->default(true),
                        Toggle::make('show_on_home')
                            ->label('Show on homepage')
                            ->default(true),
                    ]),
            ]);
    }
}

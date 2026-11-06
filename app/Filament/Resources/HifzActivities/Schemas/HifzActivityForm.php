<?php

namespace App\Filament\Resources\HifzActivities\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HifzActivityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Activity image')
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
                            ->directory('hifz/activities')
                            ->disk('public')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('caption')
                            ->label('Caption')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('sort_order')
                            ->label('Sort order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower numbers appear first.'),
                        Toggle::make('is_published')
                            ->label('Published')
                            ->default(true),
                    ]),
            ]);
    }
}

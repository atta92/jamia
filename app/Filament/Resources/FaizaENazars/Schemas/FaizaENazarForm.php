<?php

namespace App\Filament\Resources\FaizaENazars\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FaizaENazarForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Faizan e Nazar')
                    ->description('Homepage section featuring people (photo, name, title, description).')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('title')
                            ->label('Title')
                            ->maxLength(255)
                            ->placeholder('e.g. شیخ الحدیث'),
                        Textarea::make('description')
                            ->label('Description')
                            ->rows(5)
                            ->columnSpanFull(),
                        FileUpload::make('photo')
                            ->label('Photo')
                            ->image()
                            ->imageEditor()
                            ->directory('faiza-e-nazar')
                            ->disk('public')
                            ->required()
                            ->columnSpanFull(),
                        Toggle::make('is_published')
                            ->label('Published')
                            ->default(true),
                        Toggle::make('show_on_home')
                            ->label('Show on homepage')
                            ->default(true),
                        TextInput::make('sort_order')
                            ->label('Sort order')
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}

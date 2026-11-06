<?php

namespace App\Filament\Resources\Teachers\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TeacherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Teacher')
                    ->description('Shown on the About Us page (photo, name, title, description).')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('title')
                            ->label('Title')
                            ->maxLength(255)
                            ->placeholder('e.g. استادِ حدیث'),
                        Textarea::make('description')
                            ->label('Description')
                            ->rows(5)
                            ->columnSpanFull(),
                        FileUpload::make('photo')
                            ->label('Photo')
                            ->image()
                            ->imageEditor()
                            ->directory('teachers')
                            ->disk('public')
                            ->columnSpanFull(),
                        Toggle::make('is_published')
                            ->label('Published')
                            ->default(true),
                        TextInput::make('sort_order')
                            ->label('Sort order')
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}

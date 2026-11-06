<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Event')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                if (blank($get('slug')) && filled($state)) {
                                    $set('slug', Str::slug($state));
                                }
                            })
                            ->columnSpanFull(),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Used in the event URL.'),
                        TextInput::make('location')
                            ->label('Location')
                            ->maxLength(255)
                            ->placeholder('e.g. جامعہ، چکوال'),
                        DateTimePicker::make('starts_at')
                            ->label('Starts at')
                            ->seconds(false)
                            ->default(now()->addWeek()),
                        DateTimePicker::make('ends_at')
                            ->label('Ends at')
                            ->seconds(false),
                        Textarea::make('excerpt')
                            ->label('Short summary')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),
                        Textarea::make('audience')
                            ->label('For who')
                            ->rows(2)
                            ->columnSpanFull(),
                        Textarea::make('highlights')
                            ->label('What to expect')
                            ->rows(2)
                            ->columnSpanFull(),
                        RichEditor::make('body')
                            ->label('Details')
                            ->columnSpanFull(),
                        FileUpload::make('cover_image')
                            ->label('Cover image')
                            ->image()
                            ->directory('events')
                            ->disk('public')
                            ->imageEditor()
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

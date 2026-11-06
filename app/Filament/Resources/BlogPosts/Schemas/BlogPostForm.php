<?php

namespace App\Filament\Resources\BlogPosts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BlogPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Blog post')
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
                            ->helperText('Used in the blog URL.'),
                        TextInput::make('category')
                            ->label('Category')
                            ->maxLength(100)
                            ->placeholder('e.g. تعلیم، تقریبات'),
                        TextInput::make('author_name')
                            ->label('Author')
                            ->maxLength(120),
                        DateTimePicker::make('published_at')
                            ->label('Publish date')
                            ->seconds(false)
                            ->default(now()),
                        Textarea::make('excerpt')
                            ->label('Excerpt')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),
                        RichEditor::make('body')
                            ->label('Content')
                            ->columnSpanFull(),
                        FileUpload::make('cover_image')
                            ->label('Cover image')
                            ->image()
                            ->directory('blog')
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

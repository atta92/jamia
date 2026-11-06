<?php

namespace App\Filament\Resources\GalleryItems\Pages;

use App\Filament\Resources\GalleryItems\GalleryItemResource;
use App\Models\GalleryItem;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Str;

class ListGalleryItems extends ListRecords
{
    protected static string $resource = GalleryItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('bulkUpload')
                ->label('Bulk upload')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray')
                ->modalHeading('Bulk upload photos')
                ->modalDescription('Upload many photos at once. Each file becomes its own gallery item.')
                ->modalSubmitActionLabel('Upload all')
                ->form([
                    FileUpload::make('images')
                        ->label('Images')
                        ->image()
                        ->multiple()
                        ->reorderable()
                        ->appendFiles()
                        ->directory('gallery')
                        ->disk('public')
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                        ->maxSize(10240)
                        ->maxFiles(100)
                        ->required()
                        ->helperText('Up to 100 images, max 10MB each.')
                        ->columnSpanFull(),
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
                    Toggle::make('is_published')
                        ->label('Published')
                        ->default(true),
                    Toggle::make('show_on_home')
                        ->label('Show on homepage')
                        ->default(true),
                ])
                ->action(function (array $data): void {
                    $images = $data['images'] ?? [];

                    if (! is_array($images) || $images === []) {
                        Notification::make()
                            ->title('No images selected')
                            ->danger()
                            ->send();

                        return;
                    }

                    $sortBase = ((int) GalleryItem::query()->max('sort_order')) + 1;
                    $created = 0;

                    foreach (array_values($images) as $index => $path) {
                        if (! filled($path)) {
                            continue;
                        }

                        $filename = pathinfo((string) $path, PATHINFO_FILENAME);
                        $title = Str::of($filename)
                            ->replace(['-', '_'], ' ')
                            ->squish()
                            ->title()
                            ->limit(255)
                            ->toString();

                        GalleryItem::query()->create([
                            'title' => $title !== '' ? $title : null,
                            'image' => $path,
                            'type' => 'photo',
                            'video_url' => null,
                            'year' => $data['year'] ?? (int) date('Y'),
                            'category' => $data['category'] ?? null,
                            'sort_order' => $sortBase + $index,
                            'is_published' => (bool) ($data['is_published'] ?? true),
                            'show_on_home' => (bool) ($data['show_on_home'] ?? true),
                        ]);

                        $created++;
                    }

                    Notification::make()
                        ->title($created.' photo'.($created === 1 ? '' : 's').' uploaded')
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}

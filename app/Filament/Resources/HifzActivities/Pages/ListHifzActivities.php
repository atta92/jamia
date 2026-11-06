<?php

namespace App\Filament\Resources\HifzActivities\Pages;

use App\Filament\Resources\HifzActivities\HifzActivityResource;
use App\Models\HifzActivity;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Str;

class ListHifzActivities extends ListRecords
{
    protected static string $resource = HifzActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('bulkUpload')
                ->label('Bulk upload')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray')
                ->modalHeading('Bulk upload activity photos')
                ->modalSubmitActionLabel('Upload all')
                ->form([
                    FileUpload::make('images')
                        ->label('Images')
                        ->image()
                        ->multiple()
                        ->reorderable()
                        ->appendFiles()
                        ->directory('hifz/activities')
                        ->disk('public')
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                        ->maxSize(10240)
                        ->maxFiles(100)
                        ->required()
                        ->columnSpanFull(),
                    Toggle::make('is_published')
                        ->label('Published')
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

                    $sortBase = ((int) HifzActivity::query()->max('sort_order')) + 1;
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

                        HifzActivity::query()->create([
                            'title' => $title !== '' ? $title : null,
                            'image' => $path,
                            'caption' => null,
                            'sort_order' => $sortBase + $index,
                            'is_published' => (bool) ($data['is_published'] ?? true),
                        ]);

                        $created++;
                    }

                    Notification::make()
                        ->title("Uploaded {$created} images")
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}

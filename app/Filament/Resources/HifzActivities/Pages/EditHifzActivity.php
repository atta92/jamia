<?php

namespace App\Filament\Resources\HifzActivities\Pages;

use App\Filament\Resources\HifzActivities\HifzActivityResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHifzActivity extends EditRecord
{
    protected static string $resource = HifzActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\FaizaENazars\Pages;

use App\Filament\Resources\FaizaENazars\FaizaENazarResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFaizaENazar extends EditRecord
{
    protected static string $resource = FaizaENazarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

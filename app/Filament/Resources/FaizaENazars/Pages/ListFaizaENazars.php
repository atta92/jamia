<?php

namespace App\Filament\Resources\FaizaENazars\Pages;

use App\Filament\Resources\FaizaENazars\FaizaENazarResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFaizaENazars extends ListRecords
{
    protected static string $resource = FaizaENazarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

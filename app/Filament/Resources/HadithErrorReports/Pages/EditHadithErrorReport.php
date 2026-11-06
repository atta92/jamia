<?php

namespace App\Filament\Resources\HadithErrorReports\Pages;

use App\Filament\Resources\HadithErrorReports\HadithErrorReportResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHadithErrorReport extends EditRecord
{
    protected static string $resource = HadithErrorReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

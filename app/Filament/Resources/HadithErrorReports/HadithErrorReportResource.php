<?php

namespace App\Filament\Resources\HadithErrorReports;

use App\Filament\Resources\HadithErrorReports\Pages\EditHadithErrorReport;
use App\Filament\Resources\HadithErrorReports\Pages\ListHadithErrorReports;
use App\Filament\Resources\HadithErrorReports\Schemas\HadithErrorReportForm;
use App\Filament\Resources\HadithErrorReports\Tables\HadithErrorReportsTable;
use App\Models\HadithErrorReport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class HadithErrorReportResource extends Resource
{
    protected static ?string $model = HadithErrorReport::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static ?string $navigationLabel = 'Hadith reports';

    protected static ?string $modelLabel = 'Hadith error report';

    protected static ?string $pluralModelLabel = 'Hadith error reports';

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return HadithErrorReportForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HadithErrorReportsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHadithErrorReports::route('/'),
            'edit' => EditHadithErrorReport::route('/{record}/edit'),
        ];
    }
}

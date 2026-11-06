<?php

namespace App\Filament\Resources\HifzActivities;

use App\Filament\Resources\HifzActivities\Pages\CreateHifzActivity;
use App\Filament\Resources\HifzActivities\Pages\EditHifzActivity;
use App\Filament\Resources\HifzActivities\Pages\ListHifzActivities;
use App\Filament\Resources\HifzActivities\Schemas\HifzActivityForm;
use App\Filament\Resources\HifzActivities\Tables\HifzActivitiesTable;
use App\Models\HifzActivity;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class HifzActivityResource extends Resource
{
    protected static ?string $model = HifzActivity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCamera;

    protected static ?string $navigationLabel = 'Hifz Activities';

    protected static ?string $modelLabel = 'Hifz activity';

    protected static ?string $pluralModelLabel = 'Hifz activities';

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 8;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return HifzActivityForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HifzActivitiesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHifzActivities::route('/'),
            'create' => CreateHifzActivity::route('/create'),
            'edit' => EditHifzActivity::route('/{record}/edit'),
        ];
    }
}

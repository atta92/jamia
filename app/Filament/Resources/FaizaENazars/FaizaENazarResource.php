<?php

namespace App\Filament\Resources\FaizaENazars;

use App\Filament\Resources\FaizaENazars\Pages\CreateFaizaENazar;
use App\Filament\Resources\FaizaENazars\Pages\EditFaizaENazar;
use App\Filament\Resources\FaizaENazars\Pages\ListFaizaENazars;
use App\Filament\Resources\FaizaENazars\Schemas\FaizaENazarForm;
use App\Filament\Resources\FaizaENazars\Tables\FaizaENazarsTable;
use App\Models\FaizaENazar;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class FaizaENazarResource extends Resource
{
    protected static ?string $model = FaizaENazar::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected static ?string $navigationLabel = 'Faizan e Nazar';

    protected static ?string $modelLabel = 'Faizan e Nazar';

    protected static ?string $pluralModelLabel = 'Faizan e Nazar';

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return FaizaENazarForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FaizaENazarsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFaizaENazars::route('/'),
            'create' => CreateFaizaENazar::route('/create'),
            'edit' => EditFaizaENazar::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources\HadithErrorReports\Schemas;

use App\Models\HadithErrorReport;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HadithErrorReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Report')
                    ->columns(2)
                    ->schema([
                        TextInput::make('hadith_id')
                            ->label('Hadith ID')
                            ->disabled(),
                        Select::make('error_type')
                            ->label('Type of error')
                            ->options(HadithErrorReport::errorTypes())
                            ->disabled(),
                        TextInput::make('other_detail')
                            ->label('Other detail')
                            ->disabled()
                            ->columnSpanFull(),
                        Textarea::make('details')
                            ->label('Additional details')
                            ->rows(5)
                            ->disabled()
                            ->columnSpanFull(),
                        Toggle::make('notify_email')
                            ->label('Notify by email')
                            ->disabled(),
                        TextInput::make('email')
                            ->label('Email')
                            ->disabled(),
                        TextInput::make('page_url')
                            ->label('Page URL')
                            ->disabled()
                            ->columnSpanFull(),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                HadithErrorReport::STATUS_PENDING => 'Pending',
                                HadithErrorReport::STATUS_REVIEWED => 'Reviewed',
                                HadithErrorReport::STATUS_FIXED => 'Fixed',
                                HadithErrorReport::STATUS_DISMISSED => 'Dismissed',
                            ])
                            ->required(),
                    ]),
            ]);
    }
}

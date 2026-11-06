<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * @property-read Schema $form
 */
class ManageSiteSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Site Settings';

    protected static ?string $title = 'Site Settings';

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.manage-site-settings';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $settings = SiteSetting::current();

        $this->form->fill($settings->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Website info')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Site name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('tagline')
                            ->label('Tagline')
                            ->maxLength(255),
                        TextInput::make('location_label')
                            ->label('Location label')
                            ->maxLength(255),
                        TextInput::make('hero_line')
                            ->label('Hero line')
                            ->maxLength(255),
                        Textarea::make('short_description')
                            ->label('Short description')
                            ->rows(3)
                            ->columnSpanFull(),
                        FileUpload::make('logo')
                            ->label('Logo')
                            ->image()
                            ->imageEditor()
                            ->directory('logos')
                            ->disk('public')
                            ->helperText('Shown in the header and footer. PNG or SVG recommended.')
                            ->columnSpanFull(),
                        FileUpload::make('hero_image')
                            ->label('Hero image')
                            ->image()
                            ->directory('hero')
                            ->disk('public')
                            ->columnSpanFull(),
                    ]),

                Section::make('About page (ہمارے بارے میں)')
                    ->description('Content for /about')
                    ->schema([
                        TextInput::make('about_heading')
                            ->label('About heading')
                            ->maxLength(255),
                        Textarea::make('about_text')
                            ->label('About text')
                            ->rows(6)
                            ->helperText('Use blank lines to split into paragraphs.'),
                    ]),

                Section::make('Contact page (رابطہ)')
                    ->description('Content for /contact')
                    ->columns(2)
                    ->schema([
                        TextInput::make('phone')->tel()->maxLength(50),
                        TextInput::make('email')->email()->maxLength(255),
                        TextInput::make('address')->maxLength(255)->columnSpanFull(),
                    ]),

                Section::make('Prayer settings')
                    ->columns(2)
                    ->schema([
                        TextInput::make('prayer_city')
                            ->label('City (API)')
                            ->required()
                            ->helperText('English city name for Aladhan API'),
                        TextInput::make('prayer_city_ur')
                            ->label('City (Urdu)'),
                        TextInput::make('prayer_country')
                            ->label('Country (API)')
                            ->required(),
                        TextInput::make('prayer_country_ur')
                            ->label('Country (Urdu)'),
                        TextInput::make('prayer_timezone')
                            ->label('Timezone')
                            ->required(),
                        TextInput::make('prayer_method')
                            ->label('Calculation method')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(15)
                            ->helperText('1 = Karachi'),
                        TextInput::make('iqamah_fajr')->label('Iqamah Fajr (+min)')->numeric()->minValue(0),
                        TextInput::make('iqamah_dhuhr')->label('Iqamah Zuhr (+min)')->numeric()->minValue(0),
                        TextInput::make('iqamah_asr')->label('Iqamah Asr (+min)')->numeric()->minValue(0),
                        TextInput::make('iqamah_maghrib')->label('Iqamah Maghrib (+min)')->numeric()->minValue(0),
                        TextInput::make('iqamah_isha')->label('Iqamah Isha (+min)')->numeric()->minValue(0),
                        TextInput::make('iqamah_jummah')->label('Iqamah Jummah (+min)')->numeric()->minValue(0),
                    ]),
            ])
            ->statePath('data')
            ->model(SiteSetting::current());
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $settings = SiteSetting::current();
        $settings->update($data);

        // Clear prayer cache when city/method/offsets change
        cache()->flush();

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Save changes')
                                ->submit('save')
                                ->keyBindings(['mod+s']),
                        ]),
                    ]),
            ]);
    }

}

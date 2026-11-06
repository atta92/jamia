<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
class ManageMuhtamimMessage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;

    protected static ?string $navigationLabel = 'Muhtamim Message';

    protected static ?string $title = 'Muhtamim Message';

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.manage-site-settings';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $settings = SiteSetting::current();

        $this->form->fill([
            'show_muhtamim_message' => $settings->show_muhtamim_message,
            'muhtamim_heading' => $settings->muhtamim_heading,
            'muhtamim_name' => $settings->muhtamim_name,
            'muhtamim_role' => $settings->muhtamim_role,
            'muhtamim_message' => $settings->muhtamim_message,
            'muhtamim_photo' => $settings->muhtamim_photo,
            'muhtamim_facebook_url' => $settings->muhtamim_facebook_url,
            'muhtamim_whatsapp' => $settings->muhtamim_whatsapp,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('CEO / Muhtamim message')
                    ->description('Homepage section near the footer.')
                    ->columns(2)
                    ->schema([
                        Toggle::make('show_muhtamim_message')
                            ->label('Show on homepage')
                            ->default(true)
                            ->columnSpanFull(),
                        TextInput::make('muhtamim_heading')
                            ->label('Section heading')
                            ->maxLength(255)
                            ->placeholder('Muhtamim Sahib ka Paigham'),
                        TextInput::make('muhtamim_name')
                            ->label('Name')
                            ->maxLength(255),
                        TextInput::make('muhtamim_role')
                            ->label('Role / title')
                            ->maxLength(255)
                            ->placeholder('Muhtamim'),
                        Textarea::make('muhtamim_message')
                            ->label('Message')
                            ->rows(6)
                            ->columnSpanFull(),
                        FileUpload::make('muhtamim_photo')
                            ->label('Photo')
                            ->image()
                            ->imageEditor()
                            ->directory('muhtamim')
                            ->disk('public')
                            ->helperText('Prefer a cut-out / transparent PNG for best look.')
                            ->columnSpanFull(),
                        TextInput::make('muhtamim_facebook_url')
                            ->label('Facebook URL')
                            ->url()
                            ->maxLength(255)
                            ->placeholder('https://facebook.com/...'),
                        TextInput::make('muhtamim_whatsapp')
                            ->label('WhatsApp')
                            ->maxLength(50)
                            ->placeholder('923001234567')
                            ->helperText('Country code + number, or full https://wa.me/… link'),
                    ]),
            ])
            ->statePath('data')
            ->model(SiteSetting::current());
    }

    public function save(): void
    {
        $data = $this->form->getState();

        SiteSetting::current()->update([
            'show_muhtamim_message' => (bool) ($data['show_muhtamim_message'] ?? false),
            'muhtamim_heading' => $data['muhtamim_heading'] ?? null,
            'muhtamim_name' => $data['muhtamim_name'] ?? null,
            'muhtamim_role' => $data['muhtamim_role'] ?? null,
            'muhtamim_message' => $data['muhtamim_message'] ?? null,
            'muhtamim_photo' => $data['muhtamim_photo'] ?? null,
            'muhtamim_facebook_url' => $data['muhtamim_facebook_url'] ?? null,
            'muhtamim_whatsapp' => $data['muhtamim_whatsapp'] ?? null,
        ]);

        Notification::make()
            ->title('Message saved')
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

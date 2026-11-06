<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use BackedEnum;
use Filament\Actions\Action;
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
class ManageDuroodSection extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static ?string $navigationLabel = 'Durood Section';

    protected static ?string $title = 'Durood Section';

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.manage-site-settings';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $settings = SiteSetting::current();

        $this->form->fill([
            'show_durood_section' => $settings->show_durood_section,
            'durood_kicker' => $settings->durood_kicker,
            'durood_text' => $settings->durood_text,
            'durood_fazilat' => $settings->durood_fazilat,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Homepage durood scroll')
                    ->description('Sticky horizontal section right after the hero.')
                    ->schema([
                        Toggle::make('show_durood_section')
                            ->label('Show on homepage')
                            ->default(true),
                        TextInput::make('durood_kicker')
                            ->label('Small title')
                            ->maxLength(255)
                            ->placeholder('درود پڑھیے'),
                        TextInput::make('durood_text')
                            ->label('Big scrolling text')
                            ->maxLength(255)
                            ->placeholder('صلّی اللہ علیہ وآلہ وسلّم'),
                        Textarea::make('durood_fazilat')
                            ->label('Fazilat / bottom text')
                            ->rows(4)
                            ->helperText('Shown under the big text, like the demo site bottom line.')
                            ->placeholder('فرمانِ مصطفیٰ ﷺ: …'),
                    ]),
            ])
            ->statePath('data')
            ->model(SiteSetting::current());
    }

    public function save(): void
    {
        $data = $this->form->getState();

        SiteSetting::current()->update([
            'show_durood_section' => (bool) ($data['show_durood_section'] ?? false),
            'durood_kicker' => $data['durood_kicker'] ?? null,
            'durood_text' => $data['durood_text'] ?? null,
            'durood_fazilat' => $data['durood_fazilat'] ?? null,
        ]);

        Notification::make()
            ->title('Durood section saved')
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

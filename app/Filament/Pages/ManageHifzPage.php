<?php

namespace App\Filament\Pages;

use App\Models\HifzSetting;
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
class ManageHifzPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $navigationLabel = 'Hifz Section';

    protected static ?string $title = 'Madrassa Hifz ul Quran';

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 7;

    protected string $view = 'filament.pages.manage-site-settings';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $settings = HifzSetting::current();

        $this->form->fill($settings->only([
            'show_on_home',
            'kicker',
            'title',
            'excerpt',
            'image',
            'page_title',
            'page_lead',
            'body',
            'about_heading',
            'activities_heading',
            'cta_label',
            'contact_cta_label',
            'counter_1_value',
            'counter_1_label',
            'counter_2_value',
            'counter_2_label',
            'counter_3_value',
            'counter_3_label',
            'counter_4_value',
            'counter_4_label',
        ]));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Homepage section')
                    ->description('Landing page Hifz block: image, title, text, counters, and button.')
                    ->columns(2)
                    ->schema([
                        Toggle::make('show_on_home')
                            ->label('Show on homepage')
                            ->default(true)
                            ->columnSpanFull(),
                        TextInput::make('kicker')
                            ->label('Kicker / tag')
                            ->maxLength(255)
                            ->placeholder('حفظ القرآن'),
                        TextInput::make('title')
                            ->label('Title')
                            ->maxLength(255)
                            ->placeholder('مدرسہ حفظ القرآن'),
                        Textarea::make('excerpt')
                            ->label('Short description')
                            ->rows(4)
                            ->columnSpanFull(),
                        FileUpload::make('image')
                            ->label('Section image')
                            ->image()
                            ->imageEditor()
                            ->directory('hifz')
                            ->disk('public')
                            ->helperText('Used on the homepage half-panel. Landscape or portrait both work.')
                            ->columnSpanFull(),
                        TextInput::make('cta_label')
                            ->label('Homepage button label')
                            ->maxLength(100)
                            ->placeholder('مزید دیکھیں'),
                    ]),
                Section::make('Counters')
                    ->description('Shown on the homepage section and on the /hifz page.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('counter_1_value')->label('Counter 1 value')->maxLength(50),
                        TextInput::make('counter_1_label')->label('Counter 1 label')->maxLength(100),
                        TextInput::make('counter_2_value')->label('Counter 2 value')->maxLength(50),
                        TextInput::make('counter_2_label')->label('Counter 2 label')->maxLength(100),
                        TextInput::make('counter_3_value')->label('Counter 3 value')->maxLength(50),
                        TextInput::make('counter_3_label')->label('Counter 3 label')->maxLength(100),
                        TextInput::make('counter_4_value')->label('Counter 4 value')->maxLength(50),
                        TextInput::make('counter_4_label')->label('Counter 4 label')->maxLength(100),
                    ]),
                Section::make('Dedicated page (/hifz)')
                    ->description('Hero, about text, and section headings for the full Hifz page.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('page_title')
                            ->label('Page title')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('page_lead')
                            ->label('Page lead')
                            ->maxLength(500)
                            ->columnSpanFull(),
                        TextInput::make('about_heading')
                            ->label('About section heading')
                            ->maxLength(255)
                            ->placeholder('مدرسہ حفظ القرآن کے بارے میں')
                            ->columnSpanFull(),
                        Textarea::make('body')
                            ->label('Full description')
                            ->rows(8)
                            ->columnSpanFull()
                            ->helperText('Separate paragraphs with a blank line.'),
                        TextInput::make('contact_cta_label')
                            ->label('Contact button label')
                            ->maxLength(150)
                            ->placeholder('مزید معلومات کے لیے رابطہ')
                            ->columnSpanFull(),
                        TextInput::make('activities_heading')
                            ->label('Activities section heading')
                            ->maxLength(255)
                            ->placeholder('مدرسہ کی سرگرمیاں')
                            ->columnSpanFull()
                            ->helperText('Activity images are managed under Content → Hifz Activities.'),
                    ]),
            ])
            ->statePath('data')
            ->model(HifzSetting::current());
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $image = $data['image'] ?? null;
        if (is_array($image)) {
            $image = $image[0] ?? null;
        }

        HifzSetting::current()->update([
            'show_on_home' => (bool) ($data['show_on_home'] ?? false),
            'kicker' => $data['kicker'] ?? null,
            'title' => $data['title'] ?? null,
            'excerpt' => $data['excerpt'] ?? null,
            'image' => $image,
            'page_title' => $data['page_title'] ?? null,
            'page_lead' => $data['page_lead'] ?? null,
            'body' => $data['body'] ?? null,
            'about_heading' => $data['about_heading'] ?? null,
            'activities_heading' => $data['activities_heading'] ?? null,
            'cta_label' => $data['cta_label'] ?? null,
            'contact_cta_label' => $data['contact_cta_label'] ?? null,
            'counter_1_value' => $data['counter_1_value'] ?? null,
            'counter_1_label' => $data['counter_1_label'] ?? null,
            'counter_2_value' => $data['counter_2_value'] ?? null,
            'counter_2_label' => $data['counter_2_label'] ?? null,
            'counter_3_value' => $data['counter_3_value'] ?? null,
            'counter_3_label' => $data['counter_3_label'] ?? null,
            'counter_4_value' => $data['counter_4_value'] ?? null,
            'counter_4_label' => $data['counter_4_label'] ?? null,
        ]);

        Notification::make()
            ->title('Hifz content saved')
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

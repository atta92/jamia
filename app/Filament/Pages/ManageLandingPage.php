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
class ManageLandingPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHomeModern;

    protected static ?string $navigationLabel = 'Landing Page';

    protected static ?string $title = 'Landing Page Content';

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.manage-site-settings';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $settings = SiteSetting::current();

        $this->form->fill($settings->only([
            'hero_title',
            'hero_hadith_heading',
            'faiza_kicker',
            'show_prayer_section',
            'prayer_kicker',
            'prayer_title',
            'show_gallery_section',
            'gallery_kicker',
            'gallery_title',
            'gallery_lead',
            'gallery_cta',
            'show_blog_section',
            'blog_kicker',
            'blog_title',
            'blog_lead',
            'blog_cta',
        ]));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Hero')
                    ->description('Homepage hero copy. Image & site name are in Site Settings. Daily hadith is in Today\'s Hadith.')
                    ->schema([
                        TextInput::make('hero_title')
                            ->label('Hero title')
                            ->maxLength(255)
                            ->placeholder('دارالعلوم پیر شہانی کا مختصر تعارف')
                            ->helperText('Main headline under the tagline.'),
                        TextInput::make('hero_hadith_heading')
                            ->label('Daily hadith card heading')
                            ->maxLength(255)
                            ->placeholder('آج کی حدیثِ پاک'),
                        TextInput::make('faiza_kicker')
                            ->label('Faizan e Nazar kicker')
                            ->maxLength(255)
                            ->placeholder('فیضانِ نظر'),
                    ]),

                Section::make('Prayer times')
                    ->description('Section labels. City / offsets stay in Site Settings.')
                    ->columns(2)
                    ->schema([
                        Toggle::make('show_prayer_section')
                            ->label('Show on homepage')
                            ->default(true)
                            ->columnSpanFull(),
                        TextInput::make('prayer_kicker')
                            ->label('Kicker')
                            ->maxLength(255)
                            ->placeholder('آج کے اوقات'),
                        TextInput::make('prayer_title')
                            ->label('Title')
                            ->maxLength(255)
                            ->placeholder('اوقاتِ نماز'),
                    ]),

                Section::make('Gallery')
                    ->description('Section copy. Photos/videos are managed under Gallery.')
                    ->columns(2)
                    ->schema([
                        Toggle::make('show_gallery_section')
                            ->label('Show on homepage')
                            ->default(true)
                            ->columnSpanFull(),
                        TextInput::make('gallery_kicker')
                            ->label('Kicker')
                            ->maxLength(255)
                            ->placeholder('یادگار'),
                        TextInput::make('gallery_cta')
                            ->label('CTA label')
                            ->maxLength(100)
                            ->placeholder('مزید دیکھیں'),
                        TextInput::make('gallery_title')
                            ->label('Title')
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->placeholder('علم، تربیت اور کامیابی کے مناظر'),
                        Textarea::make('gallery_lead')
                            ->label('Lead text')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Blog')
                    ->description('Section copy. Posts are managed under Blog.')
                    ->columns(2)
                    ->schema([
                        Toggle::make('show_blog_section')
                            ->label('Show on homepage')
                            ->default(true)
                            ->columnSpanFull(),
                        TextInput::make('blog_kicker')
                            ->label('Kicker')
                            ->maxLength(255)
                            ->placeholder('بلاگ'),
                        TextInput::make('blog_cta')
                            ->label('CTA label')
                            ->maxLength(100)
                            ->placeholder('تمام مضامین'),
                        TextInput::make('blog_title')
                            ->label('Title')
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->placeholder('علمی مباحث اور شرعی رہنمائی'),
                        Textarea::make('blog_lead')
                            ->label('Lead text')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Other homepage sections')
                    ->description('Edit these from their dedicated pages.')
                    ->schema([
                        TextInput::make('_hint_durood')
                            ->label('Durood')
                            ->disabled()
                            ->dehydrated(false)
                            ->default('Content → Durood Section'),
                        TextInput::make('_hint_hifz')
                            ->label('Hifz')
                            ->disabled()
                            ->dehydrated(false)
                            ->default('Content → Hifz Section / Hifz Activities'),
                        TextInput::make('_hint_muhtamim')
                            ->label('Muhtamim message')
                            ->disabled()
                            ->dehydrated(false)
                            ->default('Content → Muhtamim Message'),
                        TextInput::make('_hint_site')
                            ->label('Site name, tagline, hero image, contact')
                            ->disabled()
                            ->dehydrated(false)
                            ->default('Content → Site Settings'),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ])
            ->statePath('data')
            ->model(SiteSetting::current());
    }

    public function save(): void
    {
        $data = $this->form->getState();

        SiteSetting::current()->update([
            'hero_title' => $data['hero_title'] ?? null,
            'hero_hadith_heading' => $data['hero_hadith_heading'] ?? null,
            'faiza_kicker' => $data['faiza_kicker'] ?? null,
            'show_prayer_section' => (bool) ($data['show_prayer_section'] ?? false),
            'prayer_kicker' => $data['prayer_kicker'] ?? null,
            'prayer_title' => $data['prayer_title'] ?? null,
            'show_gallery_section' => (bool) ($data['show_gallery_section'] ?? false),
            'gallery_kicker' => $data['gallery_kicker'] ?? null,
            'gallery_title' => $data['gallery_title'] ?? null,
            'gallery_lead' => $data['gallery_lead'] ?? null,
            'gallery_cta' => $data['gallery_cta'] ?? null,
            'show_blog_section' => (bool) ($data['show_blog_section'] ?? false),
            'blog_kicker' => $data['blog_kicker'] ?? null,
            'blog_title' => $data['blog_title'] ?? null,
            'blog_lead' => $data['blog_lead'] ?? null,
            'blog_cta' => $data['blog_cta'] ?? null,
        ]);

        Notification::make()
            ->title('Landing page content saved')
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

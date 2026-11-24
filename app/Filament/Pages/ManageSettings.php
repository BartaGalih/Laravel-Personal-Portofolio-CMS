<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Support\SiteSettings;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Settings';

    protected static ?string $title = 'Settings';

    protected static ?int $navigationSort = 90;

    /**
     * @var array<string, mixed>
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(SiteSettings::all());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Site identity')
                    ->schema([
                        TextInput::make('name')
                            ->label('Site name')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Used in the browser title, header fallback, footer and copyright.'),
                        Grid::make(2)->schema([
                            TextInput::make('tagline_en')
                                ->label('Tagline (English)')
                                ->maxLength(255),
                            TextInput::make('tagline_id')
                                ->label('Tagline (Indonesian)')
                                ->maxLength(255)
                                ->helperText('Leave empty to fall back to the English tagline.'),
                        ]),
                    ]),
                Section::make('Header brand')
                    ->description('Choose what the public header shows next to the navigation.')
                    ->schema([
                        Radio::make('header_brand')
                            ->label('Display')
                            ->options([
                                'name' => 'Site name',
                                'logo' => 'Uploaded logo',
                            ])
                            ->default('name')
                            ->required()
                            ->live()
                            ->helperText('Falls back to the site name if no logo is uploaded.'),
                        FileUpload::make('logo_path')
                            ->label('Logo')
                            ->image()
                            ->disk('public')
                            ->directory('branding')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->visible(fn (Get $get): bool => $get('header_brand') === 'logo'),
                    ]),
                Section::make('Favicon')
                    ->schema([
                        FileUpload::make('favicon_path')
                            ->label('Favicon')
                            ->image()
                            ->disk('public')
                            ->directory('branding')
                            ->visibility('public')
                            ->maxSize(1024)
                            ->helperText('A square PNG works best. Shown in browser tabs and bookmarks.'),
                    ]),
                Section::make('Default metadata')
                    ->description('Used on pages that do not set their own description.')
                    ->schema([
                        Grid::make(2)->schema([
                            Textarea::make('meta_description_en')
                                ->label('Meta description (English)')
                                ->rows(3)
                                ->maxLength(255),
                            Textarea::make('meta_description_id')
                                ->label('Meta description (Indonesian)')
                                ->rows(3)
                                ->maxLength(255)
                                ->helperText('Leave empty to fall back to the English description.'),
                        ]),
                        FileUpload::make('meta_image_path')
                            ->label('Social share image')
                            ->image()
                            ->disk('public')
                            ->directory('branding')
                            ->visibility('public')
                            ->maxSize(4096)
                            ->helperText('Used as the og:image when a page has no cover image.'),
                    ]),
            ])
            ->statePath('data');
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')
                            ->label('Save')
                            ->submit('save'),
                    ])->key('form-actions'),
                ]),
        ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();

        // FileUpload fields submit a path string, or an empty value when cleared.
        foreach (['logo_path', 'favicon_path', 'meta_image_path'] as $fileField) {
            if (blank($state[$fileField] ?? null)) {
                $state[$fileField] = null;
            }
        }

        Setting::put(SiteSettings::KEY, $state);

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }
}

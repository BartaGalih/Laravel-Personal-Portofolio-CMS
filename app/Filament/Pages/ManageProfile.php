<?php

namespace App\Filament\Pages;

use App\Models\Profile;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageProfile extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected static ?string $navigationLabel = 'Profile';

    protected static ?string $title = 'Profile';

    protected static ?int $navigationSort = 0;

    /**
     * @var array<string, mixed>
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(
            Profile::query()->first()?->attributesToArray() ?? [],
        );
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identity')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name_en')
                                ->label('Name (English)')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('name_id')
                                ->label('Name (Indonesian)')
                                ->maxLength(255)
                                ->helperText('Leave empty to fall back to the English name.'),
                        ]),
                        Grid::make(2)->schema([
                            Textarea::make('bio_en')
                                ->label('Bio (English)')
                                ->rows(4),
                            Textarea::make('bio_id')
                                ->label('Bio (Indonesian)')
                                ->rows(4),
                        ]),
                        FileUpload::make('photo_path')
                            ->label('Photo')
                            ->image()
                            ->avatar()
                            ->disk('public')
                            ->directory('profile')
                            ->visibility('public')
                            ->maxSize(4096),
                    ]),
                Section::make('Contact details')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('email')
                                ->email()
                                ->maxLength(255),
                            TextInput::make('phone')
                                ->tel()
                                ->maxLength(255),
                        ]),
                        Repeater::make('social_links')
                            ->label('Social links')
                            ->addActionLabel('Add link')
                            ->schema([
                                TextInput::make('label')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('url')
                                    ->required()
                                    ->url()
                                    ->maxLength(255),
                            ])
                            ->columns(2)
                            ->defaultItems(0),
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

        // Repeater rows carry synthetic keys; store a plain list.
        $state['social_links'] = array_values($state['social_links'] ?? []);

        Profile::query()->updateOrCreate(['id' => Profile::query()->value('id')], $state);

        Notification::make()
            ->title('Profile saved')
            ->success()
            ->send();
    }
}

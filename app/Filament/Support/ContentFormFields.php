<?php

namespace App\Filament\Support;

use App\Enums\ContentStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

/**
 * Reusable form building blocks for the bilingual content resources.
 */
class ContentFormFields
{
    public static function titleEn(): TextInput
    {
        return TextInput::make('title_en')
            ->label('Title (English)')
            ->required()
            ->maxLength(255)
            ->live(onBlur: true)
            ->afterStateUpdated(function (Set $set, ?string $state, string $operation): void {
                if ($operation === 'create' && filled($state)) {
                    $set('slug', Str::slug($state));
                }
            });
    }

    public static function titleId(): TextInput
    {
        return TextInput::make('title_id')
            ->label('Title (Indonesian)')
            ->maxLength(255)
            ->helperText('Leave empty to fall back to the English title.');
    }

    public static function slug(): TextInput
    {
        return TextInput::make('slug')
            ->required()
            ->maxLength(255)
            ->unique(ignoreRecord: true)
            ->helperText('Used in the public URL.');
    }

    public static function status(): Select
    {
        return Select::make('status')
            ->options(ContentStatus::class)
            ->default(ContentStatus::Draft)
            ->required();
    }

    public static function publishingSection(array $extra = []): Section
    {
        return Section::make('Publishing')
            ->columnSpan(1)
            ->schema(array_merge([self::slug(), self::status()], $extra));
    }

    public static function coverImage(): FileUpload
    {
        return FileUpload::make('cover_image_path')
            ->label('Cover image')
            ->image()
            ->disk('public')
            ->directory('covers')
            ->visibility('public')
            ->maxSize(4096);
    }

    public static function sortOrder(): TextInput
    {
        return TextInput::make('sort_order')
            ->numeric()
            ->default(0)
            ->required();
    }

    public static function localizedTextarea(string $field, string $label, int $rows = 3): array
    {
        $components = [];

        foreach (['en' => 'English', 'id' => 'Indonesian'] as $locale => $language) {
            $components[] = Textarea::make("{$field}_{$locale}")
                ->label("{$label} ({$language})")
                ->rows($rows)
                ->columnSpanFull();
        }

        return $components;
    }

    /**
     * Markdown editors for both locales, matching the public Markdown pipeline.
     */
    public static function contentTabs(): Tabs
    {
        $editor = fn (string $locale): MarkdownEditor => MarkdownEditor::make("content_{$locale}")
            ->columnSpanFull()
            ->fileAttachmentsDisk('public')
            ->fileAttachmentsDirectory('content')
            ->toolbarButtons([
                ['bold', 'italic', 'strike', 'link'],
                ['heading'],
                ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                ['table', 'attachFiles'],
                ['undo', 'redo'],
            ]);

        return Tabs::make('Content')
            ->columnSpanFull()
            ->persistTabInQueryString()
            ->tabs([
                Tab::make('English')
                    ->schema([$editor('en')]),
                Tab::make('Indonesian')
                    ->schema([
                        $editor('id')
                            ->helperText('Leave empty to fall back to the English content.'),
                    ]),
            ]);
    }

    public static function publishedAt(): DateTimePicker
    {
        return DateTimePicker::make('published_at')
            ->helperText('Only shown publicly while the status is Posted.');
    }
}

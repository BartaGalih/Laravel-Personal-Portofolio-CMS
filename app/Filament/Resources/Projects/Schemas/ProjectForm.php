<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Filament\Support\ContentFormFields;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(1)
                            ->columnSpan(2)
                            ->schema([
                                ContentFormFields::titleEn(),
                                ContentFormFields::titleId(),
                                ...ContentFormFields::localizedTextarea('summary', 'Summary'),
                                ContentFormFields::contentTabs(),
                            ]),
                        Grid::make(1)
                            ->columnSpan(1)
                            ->schema([
                                ContentFormFields::publishingSection([
                                    ContentFormFields::sortOrder(),
                                ]),
                                Section::make('Media')->schema([
                                    ContentFormFields::coverImage(),
                                ]),
                                Section::make('Links')->schema([
                                    TextInput::make('project_url')
                                        ->label('Live URL')
                                        ->url()
                                        ->maxLength(255),
                                    TextInput::make('repository_url')
                                        ->label('Repository URL')
                                        ->url()
                                        ->maxLength(255),
                                ]),
                            ]),
                    ]),
            ]);
    }
}

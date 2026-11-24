<?php

namespace App\Filament\Resources\Experiences\Schemas;

use App\Filament\Support\ContentFormFields;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExperienceForm
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
                                TextInput::make('company_en')
                                    ->label('Company (English)')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('company_id')
                                    ->label('Company (Indonesian)')
                                    ->maxLength(255),
                                TextInput::make('role_en')
                                    ->label('Role (English)')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('role_id')
                                    ->label('Role (Indonesian)')
                                    ->maxLength(255),
                                ...ContentFormFields::localizedTextarea('description', 'Description', 4),
                            ]),
                        Section::make('Timeline')
                            ->columnSpan(1)
                            ->schema([
                                DatePicker::make('start_date')
                                    ->native(false),
                                DatePicker::make('end_date')
                                    ->native(false)
                                    ->helperText('Leave empty for your current role.'),
                                ContentFormFields::sortOrder(),
                                Toggle::make('is_visible')
                                    ->default(true)
                                    ->required(),
                            ]),
                    ]),
            ]);
    }
}

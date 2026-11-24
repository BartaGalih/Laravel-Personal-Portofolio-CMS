<?php

namespace App\Filament\Resources\Education\Schemas;

use App\Filament\Support\ContentFormFields;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EducationForm
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
                                TextInput::make('institution_en')
                                    ->label('Institution (English)')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('institution_id')
                                    ->label('Institution (Indonesian)')
                                    ->maxLength(255)
                                    ->helperText('Leave empty to fall back to the English value.'),
                                Grid::make(2)->schema([
                                    TextInput::make('degree_en')
                                        ->label('Degree (English)')
                                        ->maxLength(255),
                                    TextInput::make('degree_id')
                                        ->label('Degree (Indonesian)')
                                        ->maxLength(255),
                                    TextInput::make('field_en')
                                        ->label('Field of study (English)')
                                        ->maxLength(255),
                                    TextInput::make('field_id')
                                        ->label('Field of study (Indonesian)')
                                        ->maxLength(255),
                                ]),
                                ...ContentFormFields::localizedTextarea('description', 'Description'),
                            ]),
                        Section::make('Details')
                            ->columnSpan(1)
                            ->schema([
                                TextInput::make('location')
                                    ->maxLength(255),
                                DatePicker::make('start_date')
                                    ->native(false),
                                DatePicker::make('end_date')
                                    ->native(false)
                                    ->helperText('Leave empty if ongoing.'),
                                ContentFormFields::sortOrder(),
                                Toggle::make('is_visible')
                                    ->default(true)
                                    ->required(),
                            ]),
                    ]),
            ]);
    }
}

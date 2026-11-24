<?php

namespace App\Filament\Resources\Skills\Schemas;

use App\Filament\Support\ContentFormFields;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SkillForm
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
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('category')
                                    ->maxLength(255)
                                    ->helperText('Groups skills on the about page.'),
                            ]),
                        Section::make('Display')
                            ->columnSpan(1)
                            ->schema([
                                TextInput::make('proficiency')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(100)
                                    ->suffix('%'),
                                TextInput::make('icon')
                                    ->maxLength(255),
                                ContentFormFields::sortOrder(),
                                Toggle::make('is_visible')
                                    ->default(true)
                                    ->required(),
                            ]),
                    ]),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Filament\Support\ContentFormFields;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PostForm
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
                                ...ContentFormFields::localizedTextarea('excerpt', 'Excerpt'),
                                ContentFormFields::contentTabs(),
                            ]),
                        Grid::make(1)
                            ->columnSpan(1)
                            ->schema([
                                ContentFormFields::publishingSection([
                                    ContentFormFields::publishedAt(),
                                ]),
                                Section::make('Media')->schema([
                                    ContentFormFields::coverImage(),
                                ]),
                            ]),
                    ]),
            ]);
    }
}

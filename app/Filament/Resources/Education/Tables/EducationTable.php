<?php

namespace App\Filament\Resources\Education\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EducationTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('institution_en')
                    ->label('Institution')
                    ->searchable()
                    ->description(fn ($record): ?string => $record->institution_id),
                TextColumn::make('degree_en')
                    ->label('Degree')
                    ->placeholder('—'),
                TextColumn::make('field_en')
                    ->label('Field')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('start_date')
                    ->label('Period')
                    ->date('Y')
                    ->description(fn ($record): ?string => $record->end_date?->format('Y') ?? 'Present'),
                IconColumn::make('is_visible')
                    ->label('Visible')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                CreateAction::make(),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

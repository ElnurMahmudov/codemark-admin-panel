<?php

namespace App\Filament\Resources\ProjectCategories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('order_column')
            ->defaultSort('order_column')
            ->columns([
                TextColumn::make('order_column')->label('#')->sortable(),
                TextColumn::make('name')
                    ->label('Ad')
                    ->formatStateUsing(fn ($record) => $record?->getTranslation('name', 'az') ?? (is_array($record?->name) ? ($record->name['az'] ?? reset($record->name)) : $record?->name))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->formatStateUsing(fn ($record) => $record?->getTranslation('slug', 'az') ?? (is_array($record?->slug) ? ($record->slug['az'] ?? reset($record->slug)) : $record?->slug))
                    ->searchable(),
                IconColumn::make('is_active')->label('Status')->boolean(),
                TextColumn::make('updated_at')->label('Yenilənmə')->dateTime('d.m.Y H:i'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

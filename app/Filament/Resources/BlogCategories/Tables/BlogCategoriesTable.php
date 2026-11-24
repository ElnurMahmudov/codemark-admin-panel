<?php

namespace App\Filament\Resources\BlogCategories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class BlogCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('order_column')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('index')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('name')
                    ->label('Ad')
                    ->formatStateUsing(fn ($record) => $record?->getTranslation('name', 'az') ?? (is_array($record?->name) ? ($record->name['az'] ?? reset($record->name)) : $record?->name))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->formatStateUsing(fn ($record) => $record?->getTranslation('slug', 'az') ?? (is_array($record?->slug) ? ($record->slug['az'] ?? reset($record->slug)) : $record?->slug))
                    ->searchable(),
                ToggleColumn::make('is_active')->label('Status'),
                TextColumn::make('created_at')->label('Yaradılma')->dateTime('d.m.Y H:i')->sortable(),
                TextColumn::make('updated_at')->label('Yenilənmə')->dateTime('d.m.Y H:i')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('order_column')->label('Sıra')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()->iconButton(),
                DeleteAction::make()->iconButton(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

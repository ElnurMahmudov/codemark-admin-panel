<?php

namespace App\Filament\Resources\Tags\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TagsTable
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
                    ->label('Teq Adı')
                    ->formatStateUsing(fn ($record) => $record?->getTranslation('name', 'az') ?? (is_array($record?->name) ? ($record->name['az'] ?? reset($record->name)) : $record?->name))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->formatStateUsing(fn ($record) => $record?->getTranslation('slug', 'az') ?? (is_array($record?->slug) ? ($record->slug['az'] ?? reset($record->slug)) : $record?->slug))
                    ->searchable(),
                TextColumn::make('posts_count')->counts('posts')->label('Məqalələr')->badge()->color('info'),
                TextColumn::make('projects_count')->counts('projects')->label('Layihələr')->badge()->color('success'),
                TextColumn::make('created_at')->label('Yaradılma')->dateTime('d.m.Y H:i')->sortable(),
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

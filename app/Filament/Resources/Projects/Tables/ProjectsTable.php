<?php

namespace App\Filament\Resources\Projects\Tables;

use Awcodes\Curator\Components\Tables\CuratorColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('order_column')
            ->defaultSort('order_column')
            ->columns([
                TextColumn::make('order_column')->label('#')->sortable(),
                CuratorColumn::make('image')->label('Vizual')->size(40),
                TextColumn::make('title')
                    ->label('Başlıq')
                    ->formatStateUsing(fn ($record) => $record?->getTranslation('title', 'az') ?? (is_array($record?->title) ? ($record->title['az'] ?? reset($record->title)) : $record?->title))
                    ->description(fn ($record) => $record?->getTranslation('slug', 'az') ?: $record?->project_code)
                    ->searchable()
                    ->limit(35),
                TextColumn::make('category.name')
                    ->label('Kateqoriya')
                    ->formatStateUsing(fn ($state, $record) => $record?->category?->getTranslation('name', 'az') ?? (is_array($state) ? ($state['az'] ?? reset($state)) : $state))
                    ->badge(),
                TextColumn::make('tags')
                    ->label('Teqlər')
                    ->formatStateUsing(fn ($record) => $record->tags->map(fn ($t) => $t->getTranslation('name', 'az') ?? (is_array($t->name) ? ($t->name['az'] ?? reset($t->name)) : $t->name))->implode(', '))
                    ->badge()
                    ->color('info'),
                TextColumn::make('views_count')
                    ->label('Baxış')
                    ->sortable()
                    ->badge()
                    ->color('info'),
                IconColumn::make('is_featured')->label('Önə Çıxan')->boolean(),
                IconColumn::make('is_published')->label('Status')->boolean(),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->getTranslation('name', 'az') ?? $record->name),
                TernaryFilter::make('is_published')->label('Dərc Statusu'),
                TernaryFilter::make('is_featured')->label('Önə Çıxanlar'),
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

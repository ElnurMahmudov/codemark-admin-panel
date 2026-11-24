<?php

namespace App\Filament\Resources\BlogPosts\Tables;

use Awcodes\Curator\Components\Tables\CuratorColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class BlogPostsTable
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
                CuratorColumn::make('image')->label('Şəkil')->size(40),
                TextColumn::make('title')
                    ->label('Başlıq')
                    ->formatStateUsing(fn ($record) => $record?->getTranslation('title', 'az') ?? (is_array($record?->title) ? ($record->title['az'] ?? reset($record->title)) : $record?->title))
                    ->searchable()
                    ->limit(40),
                TextColumn::make('category.name')
                    ->label('Kateqoriya')
                    ->formatStateUsing(fn ($state, $record) => $record?->category?->getTranslation('name', 'az') ?? (is_array($state) ? ($state['az'] ?? reset($state)) : $state))
                    ->badge(),
                TextColumn::make('tags')
                    ->label('Teqlər')
                    ->state(function ($record): array {
                        return $record->tags
                            ?->map(fn ($tag) => $tag->getTranslation('name', 'az') ?? (is_array($tag->name) ? ($tag->name['az'] ?? reset($tag->name)) : $tag->name))
                            ->filter()
                            ->unique()
                            ->values()
                            ->all() ?? [];
                    })
                    ->badge()
                    ->color('info')
                    ->wrap()
                    ->placeholder('—'),
                TextColumn::make('published_at')->label('Tarix')->date('d.m.Y')->sortable(),
                TextColumn::make('views_count')
                    ->label('Oxunma')
                    ->sortable()
                    ->badge()
                    ->color('info'),
                ToggleColumn::make('is_published')->label('Dərc'),
                TextColumn::make('created_at')
                    ->label('Yaradılma')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('order_column')
                    ->label('Sıra')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->getTranslation('name', 'az') ?? $record->name),
                TernaryFilter::make('is_published')->label('Dərc Statusu'),
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

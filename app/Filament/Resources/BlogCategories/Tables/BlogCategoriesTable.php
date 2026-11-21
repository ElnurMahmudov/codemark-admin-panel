<?php

namespace App\Filament\Resources\BlogCategories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BlogCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Category Name')
                    ->searchable()
                    ->formatStateUsing(function ($state, $record) {
                        $depth = 0;
                        $parent = $record->parent;

                        while ($parent) {
                            $depth++;
                            $parent = $parent->parent;
                        }

                        $prefix = $depth > 0 ? str_repeat('-', $depth) . ' ' : '';

                        return $prefix . $state;
                    })
                    ->color(fn ($record) => $record->parent_id ? 'gray' : 'primary'),

                    TextColumn::make('parent.name')
                        ->label('Parent')
                        ->badge()
                        ->color('info')
                        ->placeholder('Main Category') // Əgər parent yoxdursa bu yazılsın
                        ->searchable()
                        ->sortable(),

                    TextColumn::make('slug')
                        ->icon('heroicon-o-link')
                        ->iconColor('gray')
                        ->searchable()
                        ->toggleable(isToggledHiddenByDefault: true),

                    TextColumn::make('created_at')
                        ->dateTime()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->label(''),
                EditAction::make()->label(''),
                DeleteAction::make()->label(''),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

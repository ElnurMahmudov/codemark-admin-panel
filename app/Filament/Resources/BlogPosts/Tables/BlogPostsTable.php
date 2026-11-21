<?php

namespace App\Filament\Resources\BlogPosts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Awcodes\Curator\Components\Tables\CuratorColumn;
use Illuminate\Support\Str;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\Layout as FiltersLayout;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class BlogPostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                CuratorColumn::make('image')
                    ->label('Post')
                    ->size(60)
                    ->grow(false),

                TextColumn::make('title')
                    ->label('')
                    ->searchable()
                    ->description(fn ($record) => Str::limit(strip_tags(htmlspecialchars_decode($record->content)), 120))
                    ->wrap(),

                TextColumn::make('category.name')
                    ->label('Category'),

                ToggleColumn::make('is_published')
                    ->label('Status')
                    ->onIcon('heroicon-m-check')
                    ->offIcon('heroicon-m-x-mark')
                    ->onColor('success')
                    ->offColor('danger')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('user.name')
                    ->label('Author')
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TrashedFilter::make()
                    ->native(false)
                    ->label('Display Status'),

                SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name', function (Builder $query) {
                        return $query->orderBy('name'); 
                    })
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->parent_id 
                        ? "-- {$record->name}" 
                        : $record->name
                    )
                    ->searchable()
                    ->preload(),

                SelectFilter::make('user_id')
                    ->label('Author')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make()->label('')->visible(fn ($record) => !$record->trashed()),
                EditAction::make()->label('')->visible(fn ($record) => !$record->trashed()),
                DeleteAction::make()->label('')->visible(fn ($record) => !$record->trashed()),
                ForceDeleteAction::make()->visible(fn ($record) => $record->trashed()), 
                RestoreAction::make()->visible(fn ($record) => $record->trashed()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}

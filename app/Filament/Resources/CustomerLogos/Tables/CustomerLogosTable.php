<?php

namespace App\Filament\Resources\CustomerLogos\Tables;

use Awcodes\Curator\Components\Tables\CuratorColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CustomerLogosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('order_column')
            ->defaultSort('order_column')
            ->columns([
                TextColumn::make('order_column')->label('#')->sortable(),
                CuratorColumn::make('dark_logo')->label('Tünd Loqo')->size(40),
                CuratorColumn::make('light_logo')->label('Ağ Loqo')->size(40)->extraAttributes(['class' => 'bg-slate-900 rounded p-1']),
                TextColumn::make('name')->label('Ad')->searchable()->sortable(),
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

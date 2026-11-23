<?php

namespace App\Filament\Resources\AdminUsers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AdminUsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Ad və Soyad')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('E-poçt')
                    ->searchable()
                    ->copyable() 
                    ->icon('heroicon-m-envelope'),

                TextColumn::make('role.display_name') 
                    ->label('Rol')
                    ->badge() 
                    ->color(fn (string $state): string => match ($state) {
                        'Super Admin' => 'danger',
                        'Manager' => 'warning',
                        default => 'success',
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Yaradılma Tarixi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

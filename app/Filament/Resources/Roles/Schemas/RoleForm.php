<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Section;
use App\Models\Permission;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Sistem Adı (Slug)')
                    ->required(),
                TextInput::make('display_name')
                    ->label('Rolun Adı')
                    ->required(),
                Textarea::make('description')
                    ->label('Təsvir')
                    ->columnSpanFull(),

                Section::make('İcazələr')
                    ->icon('heroicon-o-shield-check')
                    ->description('Bu rola təyin olunan icazələr')
                    ->schema([
                        CheckboxList::make('permissions') // 'permissions' Model-dəki belongsToMany əlaqəsidir
                            ->label('İcazələrin Siyahısı')
                            ->relationship(
                                name: 'permissions', 
                                titleAttribute: 'display_name'
                            )
                            ->columns(2) // Səhifədə 2 sütun şəklində göstər
                            ->gridDirection('vertical')
                            ->bulkToggleable() // "Hamısını seç" düyməsini aktiv et
                            ->searchable() // İcazələr çoxdursa, axtarış funksiyası əlavə et
                            ->required(),
                    ])
                    ->columnSpanFull()
            ]);
    }
}

<?php

namespace App\Filament\Resources\AdminUsers\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Form;
use Filament\Schemas\Components\Section;

class AdminUserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('İnzibatçı Məlumatları')
                    ->schema([
                        TextInput::make('name')
                            ->label('Ad və Soyad')
                            ->required(),

                        TextInput::make('email')
                            ->label('E-poçt')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),

                        Select::make('role_id')
                            ->label('Rol')
                            ->relationship('role', 'display_name')
                            ->preload()
                            ->required(),

                        TextInput::make('password')
                            ->label('Şifrə')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->live(), 

                        TextInput::make('password_confirmation')
                            ->password()
                            ->label('Şifrənin Təkrarı')
                            ->required(fn (string $context): bool => $context === 'create')
                            ->visible(fn (callable $get, string $context): bool => 
                                $context === 'create' || filled($get('password'))
                            ),
                    ])->columnSpanFull(),
            ]);
    }
}

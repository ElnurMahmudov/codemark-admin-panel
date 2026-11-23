<?php

namespace App\Filament\Resources\CustomerLogos\Schemas;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CustomerLogoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Loqo Məlumatları')->schema([
                    TextInput::make('name')
                        ->label('Müştəri / Brend Adı')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('website_url')
                        ->label('Müştərinin Veb Saytı')
                        ->url()
                        ->nullable(),

                    Toggle::make('is_active')
                        ->label('Aktivdir')
                        ->default(true),
                ])->columns(3),

                Section::make('Loqo Şəkilləri')->schema([
                    CuratorPicker::make('dark_logo')
                        ->label('Tünd Loqo')
                        ->buttonLabel('Loqo seç')
                        ->required(),

                    CuratorPicker::make('light_logo')
                        ->label('Açıq Loqo')
                        ->buttonLabel('Loqo seç')
                        ->required(),
                ])->columns(2),
            ]);
    }
}

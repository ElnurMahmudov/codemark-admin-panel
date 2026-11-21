<?php

namespace App\Filament\Resources\BlogCategories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Filament\Schemas\Components\Utilities\Set;

class BlogCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Kateqoriya Məlumatları')
                    ->schema([
                        Tabs::make('Dillər')->tabs([
                            Tab::make('Azərbaycan (AZ)')->schema([
                                TextInput::make('name.az')
                                    ->label('Ad (AZ)')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug.az', Str::slug($state))),

                                TextInput::make('slug.az')
                                    ->label('Slug (AZ)')
                                    ->required(),
                            ])->columns(2),

                            Tab::make('English (EN)')->schema([
                                TextInput::make('name.en')
                                    ->label('Name (EN)')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug.en', Str::slug($state))),

                                TextInput::make('slug.en')
                                    ->label('Slug (EN)'),
                            ])->columns(2),

                            Tab::make('Русский (RU)')->schema([
                                TextInput::make('name.ru')
                                    ->label('Название (RU)')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug.ru', Str::slug($state))),

                                TextInput::make('slug.ru')
                                    ->label('Slug (RU)'),
                            ])->columns(2),
                        ])->columnSpanFull(),

                        Grid::make(2)->schema([
                            TextInput::make('order_column')
                                ->label('Sıralama')
                                ->numeric()
                                ->default(0),

                            Toggle::make('is_active')
                                ->label('Aktivdir')
                                ->default(true),
                        ]),
                    ]),
            ]);
    }
}

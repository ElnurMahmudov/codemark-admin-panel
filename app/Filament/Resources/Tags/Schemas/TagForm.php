<?php

namespace App\Filament\Resources\Tags\Schemas;

use App\Filament\Actions\TranslateWithAiAction;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class TagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Teq Məlumatları')
                    ->headerActions([
                        TranslateWithAiAction::forTag(),
                    ])
                    ->schema([
                        Tabs::make('Dillər')->tabs([
                            Tab::make('Azərbaycan (AZ)')->schema([
                                TextInput::make('name.az')
                                    ->label('Teq Adı')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug.az', Str::slug($state ?? '')))
                                    ->columnSpanFull(),

                                TextInput::make('slug.az')
                                    ->label('Link')
                                    ->required()
                                    ->columnSpanFull(),
                            ]),

                            Tab::make('English (EN)')->schema([
                                TextInput::make('name.en')
                                    ->label('Tag Name')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug.en', Str::slug($state ?? '')))
                                    ->columnSpanFull(),

                                TextInput::make('slug.en')
                                    ->label('Link')
                                    ->required()
                                    ->columnSpanFull(),
                            ]),

                            Tab::make('Русский (RU)')->schema([
                                TextInput::make('name.ru')
                                    ->label('Название тега')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug.ru', Str::slug($state ?? '')))
                                    ->columnSpanFull(),

                                TextInput::make('slug.ru')
                                    ->label('Ссылка')
                                    ->required()
                                    ->columnSpanFull(),
                            ]),
                        ])->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ])
            ->columns(1);
    }
}

<?php

namespace App\Filament\Resources\ProjectCategories\Schemas;

use App\Filament\Actions\TranslateWithAiAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProjectCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Kateqoriya Məlumatları')
                    ->headerActions([
                        TranslateWithAiAction::forProjectCategory(),
                    ])
                    ->schema([
                        Tabs::make('Dillər')->tabs([
                            Tab::make('Azərbaycan (AZ)')->schema([
                                TextInput::make('name.az')
                                    ->label('Ad')
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
                                    ->label('Name')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug.en', Str::slug($state ?? '')))
                                    ->columnSpanFull(),

                                TextInput::make('slug.en')
                                    ->label('Link')
                                    ->columnSpanFull(),
                            ]),

                            Tab::make('Русский (RU)')->schema([
                                TextInput::make('name.ru')
                                    ->label('Название')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug.ru', Str::slug($state ?? '')))
                                    ->columnSpanFull(),

                                TextInput::make('slug.ru')
                                    ->label('Ссылка')
                                    ->columnSpanFull(),
                            ]),
                        ])->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label('Aktivdir')
                            ->default(true),
                    ])
                    ->columnSpanFull(),
            ])
            ->columns(1);
    }
}

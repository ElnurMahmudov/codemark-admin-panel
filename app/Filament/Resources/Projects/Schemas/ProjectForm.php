<?php

namespace App\Filament\Resources\Projects\Schemas;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('ProjectTabs')->tabs([
                    // TAB 1: Ümumi & Kart Məlumatları
                    Tab::make('Əsas Məlumatlar')->schema([
                        TextInput::make('project_code')
                            ->label('Layihə Kodu (Unikal ID: express-avto)')
                            ->required()
                            ->unique(ignoreRecord: true),

                        TextInput::make('client')
                            ->label('Müştəri / Brend')
                            ->required(),

                        Select::make('category_id')
                            ->label('Kateqoriya')
                            ->relationship('category', 'name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->getTranslation('name', 'az') ?? $record->name)
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('year')
                            ->label('İl')
                            ->default('2026')
                            ->maxLength(4),

                        TextInput::make('website_url')
                            ->label('Layihənin Canlı Linki')
                            ->url(),

                        Select::make('tags')
                            ->label('Teqlər (Bazadan seçin və ya yeni yaradın)')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Teq Adı')
                                    ->required()
                                    ->unique('tags', 'name'),
                            ])
                            ->createOptionUsing(function (array $data) {
                                return \App\Models\Tag::create([
                                    'name' => $data['name'],
                                    'slug' => Str::slug($data['name']),
                                ])->id;
                            })
                            ->columnSpanFull(),

                        Toggle::make('is_featured')
                            ->label('Ana səhifədə önə çıxar')
                            ->default(false),

                        Toggle::make('is_published')
                            ->label('Dərc edilsin')
                            ->default(true),
                    ])->columns(2),

                    // TAB 2: Dizayn & Rənglər
                    Tab::make('Vizual & Rənglər')->schema([
                        CuratorPicker::make('image')
                            ->label('Əsas Vitrin Şəkli (Curator)')
                            ->buttonLabel('Şəkil seç')
                            ->required(),

                        ColorPicker::make('bg_light')
                            ->label('Light Kart Fon Rəngi (HEX)')
                            ->default('#FECDD3')
                            ->required(),

                        ColorPicker::make('bg_dark')
                            ->label('Dark Kart Fon Rəngi (HEX)')
                            ->default('#230A10')
                            ->required(),

                        CuratorPicker::make('gallery')
                            ->label('Qalereya Şəkilləri (Curator)')
                            ->buttonLabel('Şəkilləri seç')
                            ->multiple()
                            ->columnSpanFull(),
                    ])->columns(3),

                    // TAB 3: Spesifikasiyalar (Specs)
                    Tab::make('Göstəricilər (Specs)')->schema([
                        Repeater::make('specs')
                            ->label('Texniki Göstəricilər (Müştəri, Sahə, Xidmət, Müddət)')
                            ->schema([
                                TextInput::make('label')->label('Parametr')->required(),
                                TextInput::make('value')->label('Dəyər')->required(),
                            ])
                            ->columns(2)
                            ->default([
                                ['label' => 'Müştəri', 'value' => ''],
                                ['label' => 'Sahə', 'value' => ''],
                                ['label' => 'Xidmət', 'value' => ''],
                                ['label' => 'Müddət', 'value' => ''],
                            ])
                            ->columnSpanFull(),
                    ]),

                    // TAB 4: Azərbaycan (AZ)
                    Tab::make('Azərbaycan (AZ)')->schema([
                        TextInput::make('title.az')
                            ->label('Layihə Başlığı (AZ)')
                            ->required(),

                        Textarea::make('description.az')
                            ->label('Kart Təsviri (AZ)')
                            ->rows(2)
                            ->required(),

                        Textarea::make('challenge.az')
                            ->label('Müştərinin Problemi / Çətinlik (Challenge - AZ)')
                            ->rows(3),

                        Textarea::make('solution.az')
                            ->label('Təqdim Edilən Həll (Solution - AZ)')
                            ->rows(3),

                        Repeater::make('overview.az')
                            ->label('Ümumi Baxış Paraqrafları (AZ)')
                            ->simple(Textarea::make('paragraph')->rows(2)->required())
                            ->reorderable()
                            ->collapsible(),

                        Repeater::make('features.az')
                            ->label('Əsas Funksiyalar (AZ)')
                            ->simple(TextInput::make('feature')->required())
                            ->reorderable()
                            ->collapsible(),

                        Repeater::make('results.az')
                            ->label('Əldə Olunan Nəticələr (AZ)')
                            ->simple(TextInput::make('result')->required())
                            ->reorderable()
                            ->collapsible(),

                        TextInput::make('meta_title.az')
                            ->label('SEO Meta Başlıq (AZ)'),

                        Textarea::make('meta_description.az')
                            ->label('SEO Meta Təsvir (AZ)')
                            ->rows(2),
                    ])->columns(2),

                    // TAB 5: English (EN)
                    Tab::make('English (EN)')->schema([
                        TextInput::make('title.en')
                            ->label('Project Title (EN)'),

                        Textarea::make('description.en')
                            ->label('Card Description (EN)')
                            ->rows(2),

                        Textarea::make('challenge.en')
                            ->label('Client Challenge (EN)')
                            ->rows(3),

                        Textarea::make('solution.en')
                            ->label('Solution Provided (EN)')
                            ->rows(3),

                        Repeater::make('overview.en')
                            ->label('Overview Paragraphs (EN)')
                            ->simple(Textarea::make('paragraph')->rows(2)->required())
                            ->reorderable()
                            ->collapsible(),

                        Repeater::make('features.en')
                            ->label('Key Features (EN)')
                            ->simple(TextInput::make('feature')->required())
                            ->reorderable()
                            ->collapsible(),

                        Repeater::make('results.en')
                            ->label('Results Achieved (EN)')
                            ->simple(TextInput::make('result')->required())
                            ->reorderable()
                            ->collapsible(),

                        TextInput::make('meta_title.en')
                            ->label('SEO Meta Title (EN)'),

                        Textarea::make('meta_description.en')
                            ->label('SEO Meta Description (EN)')
                            ->rows(2),
                    ])->columns(2),

                    // TAB 6: Русский (RU)
                    Tab::make('Русский (RU)')->schema([
                        TextInput::make('title.ru')
                            ->label('Заголовок проекта (RU)'),

                        Textarea::make('description.ru')
                            ->label('Описание карточки (RU)')
                            ->rows(2),

                        Textarea::make('challenge.ru')
                            ->label('Проблема клиента (RU)')
                            ->rows(3),

                        Textarea::make('solution.ru')
                            ->label('Предоставленное решение (RU)')
                            ->rows(3),

                        Repeater::make('overview.ru')
                            ->label('Параграфы обзора (RU)')
                            ->simple(Textarea::make('paragraph')->rows(2)->required())
                            ->reorderable()
                            ->collapsible(),

                        Repeater::make('features.ru')
                            ->label('Основные функции (RU)')
                            ->simple(TextInput::make('feature')->required())
                            ->reorderable()
                            ->collapsible(),

                        Repeater::make('results.ru')
                            ->label('Достигнутые результаты (RU)')
                            ->simple(TextInput::make('result')->required())
                            ->reorderable()
                            ->collapsible(),

                        TextInput::make('meta_title.ru')
                            ->label('SEO Мета Заголовок (RU)'),

                        Textarea::make('meta_description.ru')
                            ->label('SEO Мета Описание (RU)')
                            ->rows(2),
                    ])->columns(2),
                ])->columnSpanFull(),
            ]);
    }
}

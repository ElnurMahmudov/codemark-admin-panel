<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Filament\Actions\TranslateWithAiAction;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Sol Tərəf: Məzmun, Göstəricilər və SEO (2/3 en)
                Group::make()
                    ->schema([
                        // Məzmun və Təsvir
                        Section::make('Məzmun və Təsvir')
                            ->headerActions([
                                TranslateWithAiAction::forProject(),
                            ])
                            ->schema([
                                Tabs::make('Məzmun Dilləri')->tabs([
                                    // 1. Azərbaycan (AZ)
                                    Tab::make('Azərbaycan (AZ)')->schema([
                                        TextInput::make('title.az')
                                            ->label('Başlıq')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                $slug = Str::slug($state ?? '');
                                                $set('slug.az', $slug);
                                                if (blank($get('project_code'))) {
                                                    $set('project_code', $slug);
                                                }
                                            })
                                            ->columnSpanFull(),

                                        TextInput::make('slug.az')
                                            ->label('Link')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                if (blank($get('project_code'))) {
                                                    $set('project_code', Str::slug($state ?? ''));
                                                }
                                            })
                                            ->columnSpanFull(),

                                        Textarea::make('description.az')
                                            ->label('Qısa Təsvir')
                                            ->rows(2)
                                            ->required()
                                            ->columnSpanFull(),

                                        Textarea::make('challenge.az')
                                            ->label('Çətinlik / Problem')
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        Textarea::make('solution.az')
                                            ->label('Təqdim Edilən Həll')
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        Repeater::make('overview.az')
                                            ->label('Ümumi Baxış')
                                            ->simple(Textarea::make('paragraph')->rows(2)->required())
                                            ->reorderable()
                                            ->collapsible()
                                            ->columnSpanFull(),

                                        Repeater::make('features.az')
                                            ->label('Əsas Funksiyalar')
                                            ->simple(TextInput::make('feature')->required())
                                            ->reorderable()
                                            ->collapsible()
                                            ->columnSpanFull(),

                                        Repeater::make('results.az')
                                            ->label('Nəticələr')
                                            ->simple(TextInput::make('result')->required())
                                            ->reorderable()
                                            ->collapsible()
                                            ->columnSpanFull(),
                                    ]),

                                    // 2. English (EN)
                                    Tab::make('English (EN)')->schema([
                                        TextInput::make('title.en')
                                            ->label('Title')
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn ($state, Set $set) => $set('slug.en', Str::slug($state ?? '')))
                                            ->columnSpanFull(),

                                        TextInput::make('slug.en')
                                            ->label('Slug / Link')
                                            ->columnSpanFull(),

                                        Textarea::make('description.en')
                                            ->label('Short Description')
                                            ->rows(2)
                                            ->columnSpanFull(),

                                        Textarea::make('challenge.en')
                                            ->label('Challenge')
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        Textarea::make('solution.en')
                                            ->label('Solution')
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        Repeater::make('overview.en')
                                            ->label('Overview')
                                            ->simple(Textarea::make('paragraph')->rows(2)->required())
                                            ->reorderable()
                                            ->collapsible()
                                            ->columnSpanFull(),

                                        Repeater::make('features.en')
                                            ->label('Key Features')
                                            ->simple(TextInput::make('feature')->required())
                                            ->reorderable()
                                            ->collapsible()
                                            ->columnSpanFull(),

                                        Repeater::make('results.en')
                                            ->label('Results')
                                            ->simple(TextInput::make('result')->required())
                                            ->reorderable()
                                            ->collapsible()
                                            ->columnSpanFull(),
                                    ]),

                                    // 3. Русский (RU)
                                    Tab::make('Русский (RU)')->schema([
                                        TextInput::make('title.ru')
                                            ->label('Заголовок')
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn ($state, Set $set) => $set('slug.ru', Str::slug($state ?? '')))
                                            ->columnSpanFull(),

                                        TextInput::make('slug.ru')
                                            ->label('Ссылка (Slug)')
                                            ->columnSpanFull(),

                                        Textarea::make('description.ru')
                                            ->label('Краткое описание')
                                            ->rows(2)
                                            ->columnSpanFull(),

                                        Textarea::make('challenge.ru')
                                            ->label('Проблема')
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        Textarea::make('solution.ru')
                                            ->label('Решение')
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        Repeater::make('overview.ru')
                                            ->label('Обзор')
                                            ->simple(Textarea::make('paragraph')->rows(2)->required())
                                            ->reorderable()
                                            ->collapsible()
                                            ->columnSpanFull(),

                                        Repeater::make('features.ru')
                                            ->label('Основные функции')
                                            ->simple(TextInput::make('feature')->required())
                                            ->reorderable()
                                            ->collapsible()
                                            ->columnSpanFull(),

                                        Repeater::make('results.ru')
                                            ->label('Результаты')
                                            ->simple(TextInput::make('result')->required())
                                            ->reorderable()
                                            ->collapsible()
                                            ->columnSpanFull(),
                                    ]),
                                ])->columnSpanFull(),
                            ]),

                        // Göstəricilər
                        Section::make('Göstəricilər')
                            ->schema([
                                Tabs::make('Göstəricilər Dilləri')->tabs([
                                    Tab::make('Azərbaycan (AZ)')->schema([
                                        Repeater::make('specs.az')
                                            ->label('Göstəricilər')
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
                                            ->reorderable()
                                            ->collapsible()
                                            ->columnSpanFull(),
                                    ]),

                                    Tab::make('English (EN)')->schema([
                                        Repeater::make('specs.en')
                                            ->label('Specifications')
                                            ->schema([
                                                TextInput::make('label')->label('Parametr')->required(),
                                                TextInput::make('value')->label('Dəyər')->required(),
                                            ])
                                            ->columns(2)
                                            ->default([
                                                ['label' => 'Client', 'value' => ''],
                                                ['label' => 'Industry', 'value' => ''],
                                                ['label' => 'Service', 'value' => ''],
                                                ['label' => 'Duration', 'value' => ''],
                                            ])
                                            ->reorderable()
                                            ->collapsible()
                                            ->columnSpanFull(),
                                    ]),

                                    Tab::make('Русский (RU)')->schema([
                                        Repeater::make('specs.ru')
                                            ->label('Показатели')
                                            ->schema([
                                                TextInput::make('label')->label('Parametr')->required(),
                                                TextInput::make('value')->label('Dəyər')->required(),
                                            ])
                                            ->columns(2)
                                            ->default([
                                                ['label' => 'Клиент', 'value' => ''],
                                                ['label' => 'Отрасль', 'value' => ''],
                                                ['label' => 'Услуга', 'value' => ''],
                                                ['label' => 'Срок', 'value' => ''],
                                            ])
                                            ->reorderable()
                                            ->collapsible()
                                            ->columnSpanFull(),
                                    ]),
                                ])->columnSpanFull(),
                            ]),

                        // SEO Parametrləri
                        Section::make('SEO Parametrləri')
                            ->schema([
                                Tabs::make('SEO Dilləri')->tabs([
                                    Tab::make('Azərbaycan (AZ)')->schema([
                                        TextInput::make('meta_title.az')
                                            ->label('Meta Başlıq')
                                            ->columnSpanFull(),

                                        Textarea::make('meta_description.az')
                                            ->label('Meta Təsvir')
                                            ->rows(2)
                                            ->columnSpanFull(),
                                    ]),

                                    Tab::make('English (EN)')->schema([
                                        TextInput::make('meta_title.en')
                                            ->label('Meta Title')
                                            ->columnSpanFull(),

                                        Textarea::make('meta_description.en')
                                            ->label('Meta Description')
                                            ->rows(2)
                                            ->columnSpanFull(),
                                    ]),

                                    Tab::make('Русский (RU)')->schema([
                                        TextInput::make('meta_title.ru')
                                            ->label('Мета Заголовок')
                                            ->columnSpanFull(),

                                        Textarea::make('meta_description.ru')
                                            ->label('Мета Описание')
                                            ->rows(2)
                                            ->columnSpanFull(),
                                    ]),
                                ])->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                // Sağ Tərəf: Media və Layihə Parametrləri (1/3 en)
                Group::make()
                    ->schema([
                        // Media & Vizual
                        Section::make('Media və Rənglər')
                            ->schema([
                                CuratorPicker::make('image')
                                    ->label('Əsas Şəkil')
                                    ->buttonLabel('Şəkil seç')
                                    ->required(),

                                CuratorPicker::make('gallery')
                                    ->label('Qalereya Şəkilləri')
                                    ->buttonLabel('Şəkilləri seç')
                                    ->multiple(),

                                ColorPicker::make('bg_light')
                                    ->label('Açıq Fon Rəngi')
                                    ->default('#FECDD3')
                                    ->required(),

                                ColorPicker::make('bg_dark')
                                    ->label('Qaranlıq Fon Rəngi')
                                    ->default('#230A10')
                                    ->required(),
                            ]),

                        // Layihə Parametrləri
                        Section::make('Layihə Parametrləri')
                            ->schema([
                                TextInput::make('project_code')
                                    ->label('Layihə Kodu (ID)')
                                    ->helperText('Sistem üçün unikal identifikator')
                                    ->unique(ignoreRecord: true),

                                TextInput::make('client')
                                    ->label('Müştəri')
                                    ->required(),

                                Select::make('category_id')
                                    ->label('Kateqoriya')
                                    ->relationship('category', 'name')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->getTranslation('name', 'az') ?? $record->name)
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Select::make('tags')
                                    ->label('Teqlər')
                                    ->relationship('tags', 'name')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->getTranslation('name', 'az') ?? (is_array($record->name) ? ($record->name['az'] ?? reset($record->name)) : $record->name))
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('name_az')->label('Teq Adı')->required(),
                                        TextInput::make('name_en')->label('Tag Name')->required(),
                                        TextInput::make('name_ru')->label('Название тега')->required(),
                                    ])
                                    ->createOptionUsing(function (array $data) {
                                        return \App\Models\Tag::create([
                                            'name' => [
                                                'az' => $data['name_az'],
                                                'en' => $data['name_en'],
                                                'ru' => $data['name_ru'],
                                            ],
                                            'slug' => [
                                                'az' => Str::slug($data['name_az']),
                                                'en' => Str::slug($data['name_en']),
                                                'ru' => Str::slug($data['name_ru']),
                                            ],
                                        ])->id;
                                    }),

                                TextInput::make('year')
                                    ->label('İl')
                                    ->default('2026')
                                    ->maxLength(4),

                                TextInput::make('website_url')
                                    ->label('Veb-sayt')
                                    ->url(),

                                Toggle::make('is_featured')
                                    ->label('Önə çıxar')
                                    ->default(false),

                                Toggle::make('is_published')
                                    ->label('Dərc edilsin')
                                    ->default(true),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}

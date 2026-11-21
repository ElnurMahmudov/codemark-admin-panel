<?php

namespace App\Filament\Resources\BlogPosts\Schemas;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BlogPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('BlogTabs')->tabs([
                    // 1. Azərbaycan (AZ)
                    Tab::make('Azərbaycan (AZ)')->schema([
                        TextInput::make('title.az')
                            ->label('Başlıq (AZ)')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Set $set) {
                                $set('slug.az', Str::slug($state));
                            }),

                        TextInput::make('slug.az')
                            ->label('URL Slug (AZ)')
                            ->required(),

                        Textarea::make('excerpt.az')
                            ->label('Qısa Giriş Mətni (Excerpt - AZ)')
                            ->rows(3)
                            ->columnSpanFull(),

                        TextInput::make('author.az')
                            ->label('Müəllif (AZ)')
                            ->default('CODEMARK'),

                        TextInput::make('meta_title.az')
                            ->label('SEO Meta Başlıq (AZ)'),

                        Textarea::make('meta_description.az')
                            ->label('SEO Meta Təsvir (AZ)')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(2),

                    // 2. English (EN)
                    Tab::make('English (EN)')->schema([
                        TextInput::make('title.en')
                            ->label('Title (EN)')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Set $set) {
                                $set('slug.en', Str::slug($state));
                            }),

                        TextInput::make('slug.en')
                            ->label('URL Slug (EN)'),

                        Textarea::make('excerpt.en')
                            ->label('Excerpt (EN)')
                            ->rows(3)
                            ->columnSpanFull(),

                        TextInput::make('author.en')
                            ->label('Author (EN)')
                            ->default('CODEMARK'),

                        TextInput::make('meta_title.en')
                            ->label('SEO Meta Title (EN)'),

                        Textarea::make('meta_description.en')
                            ->label('SEO Meta Description (EN)')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(2),

                    // 3. Русский (RU)
                    Tab::make('Русский (RU)')->schema([
                        TextInput::make('title.ru')
                            ->label('Заголовок (RU)')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Set $set) {
                                $set('slug.ru', Str::slug($state));
                            }),

                        TextInput::make('slug.ru')
                            ->label('URL Slug (RU)'),

                        Textarea::make('excerpt.ru')
                            ->label('Краткое описание (RU)')
                            ->rows(3)
                            ->columnSpanFull(),

                        TextInput::make('author.ru')
                            ->label('Автор (RU)')
                            ->default('CODEMARK'),

                        TextInput::make('meta_title.ru')
                            ->label('SEO Мета Заголовок (RU)'),

                        Textarea::make('meta_description.ru')
                            ->label('SEO Мета Описание (RU)')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(2),

                    // 4. Məqalənin Məzmunu (Sections)
                    Tab::make('Məqalənin Məzmunu (Sections)')->schema([
                        Repeater::make('sections')
                            ->label('Məzmun Bölmələri (Sections)')
                            ->schema([
                                TextInput::make('heading')
                                    ->label('Bölmə Başlığı (H2/H3)')
                                    ->required(),

                                Repeater::make('paragraphs')
                                    ->label('Paraqraflar')
                                    ->simple(Textarea::make('text')->rows(3)->required())
                                    ->reorderable()
                                    ->collapsible(),

                                Repeater::make('bullets')
                                    ->label('Siyahı Bəndləri')
                                    ->simple(TextInput::make('bullet')->required())
                                    ->reorderable()
                                    ->collapsible(),
                            ])
                            ->collapsible()
                            ->reorderable()
                            ->itemLabel(fn (array $state): ?string => $state['heading'] ?? 'Yeni Bölmə')
                            ->columnSpanFull(),
                    ]),

                    // 5. Media & Parametrlər
                    Tab::make('Media & Parametrlər')->schema([
                        CuratorPicker::make('image')
                            ->label('Qapaq Şəkli (Cover Image - Curator)')
                            ->buttonLabel('Şəkil seç')
                            ->required(),

                        Select::make('category_id')
                            ->label('Kateqoriya')
                            ->relationship('category', 'name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->getTranslation('name', 'az') ?? $record->name)
                            ->searchable()
                            ->preload()
                            ->required(),

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

                        DatePicker::make('published_at')
                            ->label('Dərc Tarixi')
                            ->default(now())
                            ->required(),

                        Toggle::make('is_published')
                            ->label('Dərc edilsin')
                            ->default(true),
                    ])->columns(2),
                ])->columnSpanFull(),
            ]);
    }
}

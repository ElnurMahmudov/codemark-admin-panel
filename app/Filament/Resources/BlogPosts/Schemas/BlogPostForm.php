<?php

namespace App\Filament\Resources\BlogPosts\Schemas;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
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
                // Sol tərəf: Başlıq, Əsas Məzmun və SEO (2/3 en)
                Group::make()
                    ->schema([
                        Section::make('Başlıq və Məlumatlar')
                            ->schema([
                                Tabs::make('Başlıq Dilləri')->tabs([
                                    Tab::make('Azərbaycan (AZ)')->schema([
                                        TextInput::make('title.az')
                                            ->label('Başlıq')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn ($state, Set $set) => $set('slug.az', Str::slug($state ?? '')))
                                            ->columnSpanFull(),

                                        TextInput::make('slug.az')
                                            ->label('Link')
                                            ->required()
                                            ->columnSpanFull(),

                                        Textarea::make('excerpt.az')
                                            ->label('Qısa Mətn')
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        TextInput::make('author.az')
                                            ->label('Müəllif')
                                            ->default('CODEMARK')
                                            ->columnSpanFull(),
                                    ]),

                                    Tab::make('English (EN)')->schema([
                                        TextInput::make('title.en')
                                            ->label('Title')
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn ($state, Set $set) => $set('slug.en', Str::slug($state ?? '')))
                                            ->columnSpanFull(),

                                        TextInput::make('slug.en')
                                            ->label('Link')
                                            ->columnSpanFull(),

                                        Textarea::make('excerpt.en')
                                            ->label('Short Excerpt')
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        TextInput::make('author.en')
                                            ->label('Author')
                                            ->default('CODEMARK')
                                            ->columnSpanFull(),
                                    ]),

                                    Tab::make('Русский (RU)')->schema([
                                        TextInput::make('title.ru')
                                            ->label('Заголовок')
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn ($state, Set $set) => $set('slug.ru', Str::slug($state ?? '')))
                                            ->columnSpanFull(),

                                        TextInput::make('slug.ru')
                                            ->label('Ссылка')
                                            ->columnSpanFull(),

                                        Textarea::make('excerpt.ru')
                                            ->label('Краткий текст')
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        TextInput::make('author.ru')
                                            ->label('Автор')
                                            ->default('CODEMARK')
                                            ->columnSpanFull(),
                                    ]),
                                ])->columnSpanFull(),
                            ]),

                        Section::make('Əsas Məzmun')
                            ->schema([
                                Tabs::make('Məzmun Dilləri')->tabs([
                                    Tab::make('Azərbaycan (AZ)')->schema([
                                        RichEditor::make('content.az')
                                            ->label('Əsas Məzmun')
                                            ->required()
                                            ->columnSpanFull(),
                                    ]),

                                    Tab::make('English (EN)')->schema([
                                        RichEditor::make('content.en')
                                            ->label('Content')
                                            ->columnSpanFull(),
                                    ]),

                                    Tab::make('Русский (RU)')->schema([
                                        RichEditor::make('content.ru')
                                            ->label('Основной контент')
                                            ->columnSpanFull(),
                                    ]),
                                ])->columnSpanFull(),
                            ]),

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

                // Sağ tərəf: Media və Parametrlər (1/3 en)
                Group::make()
                    ->schema([
                        Section::make('Media və Əsas Parametrlər')
                            ->schema([
                                CuratorPicker::make('image')
                                    ->label('Qapaq Şəkli')
                                    ->buttonLabel('Şəkil seç')
                                    ->required(),

                                Select::make('category_id')
                                    ->label('Kateqoriya')
                                    ->relationship('category', 'name')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->getTranslation('name', 'az') ?? (is_array($record->name) ? ($record->name['az'] ?? reset($record->name)) : $record->name))
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

                                DatePicker::make('published_at')
                                    ->label('Dərc Tarixi')
                                    ->default(now())
                                    ->required(),

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

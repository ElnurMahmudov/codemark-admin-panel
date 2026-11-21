<?php

namespace App\Filament\Resources\BlogPosts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use App\Filament\Traits\HasSeoFields;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Illuminate\Support\Str;
use Filament\Schemas\Components\Utilities\Set;
use App\Models\BlogCategory;
use Filament\Forms\Components\Hidden;

class BlogPostForm
{
    use HasSeoFields;
    
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('')
                    ->schema([
                        Grid::make([
                                    'default' => 1,
                                    'lg' => 3,
                                ])->schema([
                                    
                                    Grid::make(1)->schema([
                                        Section::make('Post Details')
                                            ->schema([
                                                TextInput::make('title')
                                                    ->label('Title')
                                                    ->required()
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                                                TextInput::make('slug')
                                                    ->label('Slug')
                                                    ->required()
                                                    ->unique(ignoreRecord: true),

                                                RichEditor::make('content')
                                                    ->label('Content')
                                                    ->required()
                                                    ->extraAttributes([
                                                        'style' => 'min-height: 500px;',
                                                    ])
                                                    ->columnSpanFull(),
                                            ]),
                                    ])->columnSpan(['lg' => 2]),

                                    Grid::make(1)->schema([
                                        Section::make('Featured Image')
                                            ->schema([
                                                CuratorPicker::make('image')
                                                    ->label('Main Image')
                                                    ->buttonLabel('Select Image')
                                                    ->relationship('image', 'id')
                                                    ->required()
                                                    ->columnSpanFull(),
                                            ]),

                                        Section::make('Associations & Status')
                                            ->schema([
                                                Select::make('blog_category_id')
                                                    ->label('Category')
                                                    ->options(function() {
                                                        return BlogCategory::all()->mapWithKeys(function ($category) {
                                                            $depth = 0;
                                                            $parent = $category->parent;
                                                            while ($parent) { $depth++; $parent = $parent->parent; }
                                                            return [$category->id => str_repeat('-', $depth) . ' ' . $category->name];
                                                        });
                                                    })
                                                    ->searchable()
                                                    ->required(),

                                                Toggle::make('is_published')
                                                    ->label('Published Status')
                                                    ->helperText('Enable to make this post visible on the website.')
                                                    ->default(true),

                                                Hidden::make('user_id')
                                                    ->default(auth()->id()),
                                            ]),
                                    ])->columnSpan(['lg' => 1]),

                                ]),
                ])->columnSpanFull(),
                
                ...self::getSeoFields(),
            ]);
    }
}

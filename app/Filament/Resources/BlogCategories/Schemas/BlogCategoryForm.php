<?php

namespace App\Filament\Resources\BlogCategories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Str;
use Filament\Schemas\Components\Utilities\Set;
use App\Models\BlogCategory;

class BlogCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Category Details')
                    ->description('Manage the basic information of the blog category.')
                    ->schema([
                        // Name və Slug-ı yan-yana qoymaq üçün Grid istifadə edirik
                        Grid::make(2)->schema([
                            TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255)
                            // Name üçün dehydrated() vacib deyil, çünki onsuz da gedir
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                // Name dəyişəndə slug-ı formalaşdırırıq
                                $set('slug', \Illuminate\Support\Str::slug($state));
                            }),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(BlogCategory::class, 'slug', ignoreRecord: true)
                            ->dehydrated() 
                            ->required(),
                        ]),

                        Select::make('parent_id')
                            ->label('Parent Category')
                            ->options(function (?BlogCategory $record) {
                                return BlogCategory::query()
                                    ->when($record, fn($query) => $query->where('id', '!=', $record->id))
                                    ->get()
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->placeholder('Select a parent category (Optional)'),

                        Textarea::make('description')
                            ->label('Description')
                            ->rows(3),

                    ])->columnSpanFull()
                
            ]);
    }
}

<?php

namespace App\Filament\Resources\BlogPosts\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class BlogPostInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General Information')
                    ->schema([
                        ImageEntry::make('image.path')
                            ->label('')
                            ->disk('public')
                            ->height(180)
                            ->width(350)
                            ->extraImgAttributes([
                                'class' => 'object-cover rounded-lg shadow-sm',
                            ])
                            ->columnSpan(1),

                        Grid::make(1)
                            ->schema([
                                TextEntry::make('title')
                                    ->weight('bold')
                                    ->size('lg'),
                                TextEntry::make('slug')
                                    ->color('gray')
                                    ->icon('heroicon-m-link'),
                                TextEntry::make('category.name')
                                    ->label('Category')
                                    ->badge()
                                    ->color('primary'),
                            ])
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Content')
                    ->schema([
                        TextEntry::make('content')
                            ->label('')
                            ->html()
                            ->extraAttributes([
                                'style' => 'line-height: 1.6; display: block;',
                                'class' => '[&_p]:mb-4 [&_h2]:mt-6 [&_h2]:mb-2',
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('Metadata')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created Date')
                            ->dateTime('d M Y, H:i'),
                        TextEntry::make('updated_at')
                            ->label('Last Update')
                            ->dateTime('d M Y, H:i'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}

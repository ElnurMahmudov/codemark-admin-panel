<?php

namespace App\Filament\Traits;

use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

trait HasSeoFields
{
    public static function getSeoFields(): array
    {
        return [
            Section::make('SEO Settings')
                    ->description('Meta information for search engines')
                    ->icon('heroicon-o-magnifying-glass')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Meta Title')
                            ->placeholder('Enter the meta title')
                            ->maxLength(255),

                        Textarea::make('meta_description')
                            ->label('Meta Description')
                            ->placeholder('Enter the meta description')
                            ->rows(4)
                            ->maxLength(500),
                    ])->columnSpanFull(),

        ];
    }
}
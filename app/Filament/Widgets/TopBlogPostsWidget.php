<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasDateRangeFilter;
use App\Models\BlogPost;
use Awcodes\Curator\Components\Tables\CuratorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseTableWidget;
use Illuminate\Contracts\Support\Htmlable;

class TopBlogPostsWidget extends BaseTableWidget
{
    use HasDateRangeFilter;

    protected static ?int $sort = 7;
    protected int | string | array $columnSpan = 'full';

    public function getTableHeading(): string | Htmlable | null
    {
        return 'Ən Çox Oxunan Məqalələr (' . $this->getPeriodLabel() . ')';
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(fn () => 'Ən Çox Oxunan Məqalələr (' . $this->getPeriodLabel() . ')')
            ->query(
                fn () => BlogPost::query()
                    ->withCount([
                        'visits' => function ($q) {
                            [$start, $end] = $this->getDateRange();
                            $q->whereBetween('created_at', [$start, $end]);
                        },
                    ])
                    ->orderByDesc('visits_count')
                    ->orderByDesc('views_count')
                    ->limit(5)
            )
            ->paginated(false)
            ->columns([
                CuratorColumn::make('image')->label('Şəkil')->size(40),
                TextColumn::make('title')
                    ->label('Məqalə')
                    ->formatStateUsing(fn ($record) => $record?->getTranslation('title', 'az') ?? (is_array($record?->title) ? ($record->title['az'] ?? reset($record->title)) : $record?->title))
                    ->limit(60)
                    ->weight('medium'),
                TextColumn::make('category.name')
                    ->label('Kateqoriya')
                    ->formatStateUsing(fn ($state, $record) => $record?->category?->getTranslation('name', 'az') ?? (is_array($state) ? ($state['az'] ?? reset($state)) : $state))
                    ->badge(),
                TextColumn::make('visits_count')
                    ->label('Bu Dövr')
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-m-eye'),
                TextColumn::make('views_count')
                    ->label('Cəmi Bütün Vaxt')
                    ->badge()
                    ->color('gray'),
            ]);
    }
}

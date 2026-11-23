<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasDateRangeFilter;
use App\Models\Project;
use Awcodes\Curator\Components\Tables\CuratorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseTableWidget;
use Illuminate\Contracts\Support\Htmlable;

class TopProjectsWidget extends BaseTableWidget
{
    use HasDateRangeFilter;

    protected static ?int $sort = 8;
    protected int | string | array $columnSpan = 'full';

    public function getTableHeading(): string | Htmlable | null
    {
        return 'Ən Çox Baxılan Layihələr (' . $this->getPeriodLabel() . ')';
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(fn () => 'Ən Çox Baxılan Layihələr (' . $this->getPeriodLabel() . ')')
            ->query(
                fn () => Project::query()
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
                    ->label('Layihə')
                    ->formatStateUsing(fn ($record) => $record?->getTranslation('title', 'az') ?? (is_array($record?->title) ? ($record->title['az'] ?? reset($record->title)) : $record?->title))
                    ->limit(50)
                    ->weight('medium'),
                TextColumn::make('project_code')
                    ->label('Kod')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('client')
                    ->label('Müştəri')
                    ->limit(30),
                TextColumn::make('year')
                    ->label('İl')
                    ->badge(),
                TextColumn::make('visits_count')
                    ->label('Bu Dövr')
                    ->badge()
                    ->color('warning')
                    ->icon('heroicon-m-eye'),
                TextColumn::make('views_count')
                    ->label('Cəmi Bütün Vaxt')
                    ->badge()
                    ->color('gray'),
            ]);
    }
}

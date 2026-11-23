<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasDateRangeFilter;
use App\Models\SiteVisit;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseTableWidget;
use Illuminate\Contracts\Support\Htmlable;

class TopPagesWidget extends BaseTableWidget
{
    use HasDateRangeFilter;

    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 'full';

    public function getTableHeading(): string | Htmlable | null
    {
        return 'Ən Çox Ziyarət Edilən Səhifələr (' . $this->getPeriodLabel() . ')';
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultKeySort(false)
            ->heading(fn () => 'Ən Çox Ziyarət Edilən Səhifələr (' . $this->getPeriodLabel() . ')')
            ->description('Sayt üzrə avtomatik aşkar edilən ən populyar daxili səhifələr')
            ->query(
                fn () => SiteVisit::query()
                    ->where(function ($q) {
                        [$start, $end] = $this->getDateRange();
                        $q->whereBetween('created_at', [$start, $end]);
                    })
                    ->selectRaw('MIN(id) as id, url, count(*) as visits_count, count(distinct ip_hash) as unique_count')
                    ->groupBy('url')
                    ->orderByDesc('visits_count')
            )
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5)
            ->columns([
                TextColumn::make('url')
                    ->label('Səhifə / Keçid')
                    ->icon('heroicon-m-globe-alt')
                    ->iconColor('primary')
                    ->limit(25)
                    ->copyable()
                    ->tooltip(fn ($record) => $record->url),

                TextColumn::make('page_title')
                    ->label('Səhifə Adı')
                    ->state(fn ($record) => SiteVisit::resolvePageInfo($record->url)['name'])
                    ->limit(22)
                    ->weight('medium'),

                TextColumn::make('page_type')
                    ->label('Növ')
                    ->state(fn ($record) => SiteVisit::resolvePageInfo($record->url)['type'])
                    ->badge()
                    ->color(fn ($record) => SiteVisit::resolvePageInfo($record->url)['color']),

                TextColumn::make('visits_count')
                    ->label('Baxış')
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-m-eye'),

                TextColumn::make('unique_count')
                    ->label('Unikal İP')
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-m-user'),
            ]);
    }
}

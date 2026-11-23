<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasDateRangeFilter;
use App\Models\SiteVisit;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseTableWidget;
use Illuminate\Contracts\Support\Htmlable;

class ReferrersBreakdownWidget extends BaseTableWidget
{
    use HasDateRangeFilter;

    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';

    public function getTableHeading(): string | Htmlable | null
    {
        return 'Axtarış Sistemləri və AI Keçidləri (' . $this->getPeriodLabel() . ')';
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultKeySort(false)
            ->heading(fn () => 'Axtarış Sistemləri və AI Keçidləri (' . $this->getPeriodLabel() . ')')
            ->description('Google, AI platformaları (ChatGPT, Perplexity) və digər axtarış sistemlərindən gələn kliklər')
            ->query(
                fn () => SiteVisit::query()
                    ->where(function ($q) {
                        [$start, $end] = $this->getDateRange();
                        $q->whereBetween('created_at', [$start, $end]);
                    })
                    ->selectRaw('MIN(id) as id, COALESCE(NULLIF(referer, ""), "direct") as referer_host, count(*) as visits_count')
                    ->groupBy('referer_host')
                    ->orderByDesc('visits_count')
            )
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5)
            ->columns([
                TextColumn::make('referer_host')
                    ->label('Platforma / Mənbə')
                    ->state(fn ($record) => SiteVisit::categorizeReferer($record->referer_host)['label'])
                    ->icon(fn ($record) => SiteVisit::categorizeReferer($record->referer_host)['icon'])
                    ->iconColor(fn ($record) => SiteVisit::categorizeReferer($record->referer_host)['color'])
                    ->description(fn ($record) => $record->referer_host !== 'direct' ? $record->referer_host : 'Brauzerdən birbaşa daxilolma')
                    ->weight('bold'),

                TextColumn::make('category')
                    ->label('Kateqoriya')
                    ->state(fn ($record) => SiteVisit::categorizeReferer($record->referer_host)['category'])
                    ->badge()
                    ->color(fn ($record) => SiteVisit::categorizeReferer($record->referer_host)['color']),

                TextColumn::make('visits_count')
                    ->label('Klik / Giriş')
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-m-cursor-arrow-rays'),

                TextColumn::make('percentage')
                    ->label('Trafik Payı')
                    ->state(function ($record) {
                        [$start, $end] = $this->getDateRange();
                        $total = SiteVisit::whereBetween('created_at', [$start, $end])->count();
                        if ($total === 0) {
                            return '0%';
                        }
                        return round(($record->visits_count / $total) * 100, 1) . '%';
                    })
                    ->badge()
                    ->color('gray'),
            ]);
    }
}

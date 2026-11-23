<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasDateRangeFilter;
use App\Models\BlogPost;
use App\Models\Project;
use App\Models\SiteVisit;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseStatsOverviewWidget
{
    use HasDateRangeFilter;

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        [$start, $end] = $this->getDateRange();
        $days = max(1, (int) round($start->diffInDays($end) + 1));
        $periodLabel = $this->getPeriodLabel();

        // 1. Ümumi ziyarət statistikası (seçilmiş dövr vs əvvəlki eyni müddət)
        $visits = SiteVisit::whereBetween('created_at', [$start, $end])->count();

        $prevStart = $start->copy()->subDays($days);
        $prevEnd = $start->copy()->subSecond();
        $prevVisits = SiteVisit::whereBetween('created_at', [$prevStart, $prevEnd])->count();

        $visitDifference = $visits - $prevVisits;
        $visitPercent = $prevVisits > 0
            ? round(($visitDifference / $prevVisits) * 100)
            : ($visits > 0 ? 100 : 0);

        // Dinamika əyrisi (sparkline - 7 nöqtə)
        $sparkline = [];
        $step = max(1, (int) ceil($days / 7));
        for ($i = 6; $i >= 0; $i--) {
            $segmentStart = $end->copy()->subDays(($i + 1) * $step);
            $segmentEnd = $end->copy()->subDays($i * $step);
            $sparkline[] = SiteVisit::whereBetween('created_at', [$segmentStart, $segmentEnd])->count();
        }

        $visitDescription = $visitPercent >= 0
            ? "+{$visitPercent}% əvvəlki dövrdən çox"
            : "{$visitPercent}% əvvəlki dövrdən az";
        $visitIcon = $visitPercent >= 0
            ? 'heroicon-m-arrow-trending-up'
            : 'heroicon-m-arrow-trending-down';
        $visitColor = $visitPercent >= 0 ? 'success' : 'danger';

        // 2. Google Axtarış klikləri
        $googleVisits = SiteVisit::whereBetween('created_at', [$start, $end])->fromGoogle()->count();
        $googlePercent = $visits > 0 ? round(($googleVisits / $visits) * 100, 1) : 0;

        // 3. AI tövsiyələri (ChatGPT, Perplexity, Claude, Gemini və s.)
        $aiVisits = SiteVisit::whereBetween('created_at', [$start, $end])->fromAi()->count();
        $aiPercent = $visits > 0 ? round(($aiVisits / $visits) * 100, 1) : 0;

        // 4. Bloq oxunmaları
        $totalBlogViews = BlogPost::sum('views_count');
        $blogViewsInPeriod = SiteVisit::whereBetween('created_at', [$start, $end])
            ->where('visitable_type', BlogPost::class)
            ->count();

        // 5. Layihə baxışları
        $totalProjectViews = Project::sum('views_count');
        $projectViewsInPeriod = SiteVisit::whereBetween('created_at', [$start, $end])
            ->where('visitable_type', Project::class)
            ->count();

        // 6. Unikal ziyarətçilər və mobil nisbəti
        $uniqueVisitors = SiteVisit::whereBetween('created_at', [$start, $end])
            ->distinct('ip_hash')
            ->count('ip_hash');
        $mobileVisits = SiteVisit::whereBetween('created_at', [$start, $end])
            ->where('device_type', 'mobile')
            ->count();
        $mobilePercent = $visits > 0
            ? round(($mobileVisits / $visits) * 100)
            : 0;

        return [
            Stat::make("Ümumi Baxış ({$periodLabel})", number_format($visits))
                ->description($visitDescription)
                ->descriptionIcon($visitIcon)
                ->color($visitColor)
                ->chart($sparkline),

            Stat::make("Google Axtarış ({$periodLabel})", number_format($googleVisits))
                ->description("Ümumi trafikin {$googlePercent}% payı")
                ->descriptionIcon('heroicon-m-magnifying-glass')
                ->color('info'),

            Stat::make("AI Tövsiyələri ({$periodLabel})", number_format($aiVisits))
                ->description("ChatGPT, Perplexity ({$aiPercent}%)")
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('success'),

            Stat::make("Bloq Oxunmaları ({$periodLabel})", number_format($blogViewsInPeriod))
                ->description("Cəmi bütün vaxt: " . number_format($totalBlogViews))
                ->descriptionIcon('heroicon-m-book-open')
                ->color('primary'),

            Stat::make("Layihə Baxışları ({$periodLabel})", number_format($projectViewsInPeriod))
                ->description("Cəmi bütün vaxt: " . number_format($totalProjectViews))
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('warning'),

            Stat::make("Unikal Ziyarətçi ({$periodLabel})", number_format($uniqueVisitors))
                ->description("Mobil nisbəti: {$mobilePercent}%")
                ->descriptionIcon('heroicon-m-device-phone-mobile')
                ->color('gray'),
        ];
    }
}

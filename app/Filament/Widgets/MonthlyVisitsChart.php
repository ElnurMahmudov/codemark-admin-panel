<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasDateRangeFilter;
use App\Models\BlogPost;
use App\Models\Project;
use App\Models\SiteVisit;
use Filament\Widgets\ChartWidget;

class MonthlyVisitsChart extends ChartWidget
{
    use HasDateRangeFilter;

    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    protected ?string $maxHeight = '420px; height: 420px; min-height: 420px;';

    public function getHeading(): ?string
    {
        return 'Aylıq Baxış və Ziyarət Dinamikası (' . $this->getPeriodLabel() . ')';
    }

    public function getDescription(): ?string
    {
        return 'Bütün sayt girişləri, bloq oxunmaları və layihə baxışlarının aylar üzrə müqayisəsi';
    }

    protected function getData(): array
    {
        [$start, $end] = $this->getDateRange();

        $azMonths = [
            1 => 'Yan', 2 => 'Fev', 3 => 'Mar', 4 => 'Apr',
            5 => 'May', 6 => 'İyn', 7 => 'İyl', 8 => 'Avq',
            9 => 'Sen', 10 => 'Okt', 11 => 'Noy', 12 => 'Dek',
        ];

        $months = [];
        $totalData = [];
        $blogData = [];
        $projectData = [];

        $diffMonths = ($end->year - $start->year) * 12 + ($end->month - $start->month) + 1;

        if ($diffMonths > 1 && $diffMonths <= 24) {
            // Seçilmiş tarix aralığındakı ayları ardıcıl göstər (məsələn, 2025 və ya 2026 tam ili)
            $currentMonth = $start->copy()->startOfMonth();
            while ($currentMonth->lte($end)) {
                $monthStart = $currentMonth->copy()->startOfMonth();
                $monthEnd = $currentMonth->copy()->endOfMonth();

                $months[] = ($azMonths[$monthStart->month] ?? $monthStart->format('M')) . ' ' . $monthStart->year;

                $totalData[] = SiteVisit::whereBetween('created_at', [$monthStart, $monthEnd])->count();
                $blogData[] = SiteVisit::whereBetween('created_at', [$monthStart, $monthEnd])
                    ->where('visitable_type', BlogPost::class)
                    ->count();
                $projectData[] = SiteVisit::whereBetween('created_at', [$monthStart, $monthEnd])
                    ->where('visitable_type', Project::class)
                    ->count();

                $currentMonth->addMonth();
            }
        } else {
            // Tək bir ay və ya 30 günlük dövr seçildikdə, həmin aya qədər olan 12 ayı ardıcıl göstər
            $referenceEnd = $end->copy()->endOfMonth();
            for ($i = 11; $i >= 0; $i--) {
                $monthStart = $referenceEnd->copy()->subMonths($i)->startOfMonth();
                $monthEnd = $referenceEnd->copy()->subMonths($i)->endOfMonth();

                $months[] = ($azMonths[$monthStart->month] ?? $monthStart->format('M')) . ' ' . $monthStart->year;

                $totalData[] = SiteVisit::whereBetween('created_at', [$monthStart, $monthEnd])->count();
                $blogData[] = SiteVisit::whereBetween('created_at', [$monthStart, $monthEnd])
                    ->where('visitable_type', BlogPost::class)
                    ->count();
                $projectData[] = SiteVisit::whereBetween('created_at', [$monthStart, $monthEnd])
                    ->where('visitable_type', Project::class)
                    ->count();
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Ümumi Ziyarətlər',
                    'data' => $totalData,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => 'start',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Bloq Oxunmaları',
                    'data' => $blogData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.05)',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Layihə Baxışları',
                    'data' => $projectData,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.05)',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
            ],
        ];
    }
}

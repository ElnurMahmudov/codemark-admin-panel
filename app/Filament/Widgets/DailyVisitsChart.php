<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasDateRangeFilter;
use App\Models\SiteVisit;
use Filament\Widgets\ChartWidget;

class DailyVisitsChart extends ChartWidget
{
    use HasDateRangeFilter;

    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    protected ?string $maxHeight = '400px; height: 400px; min-height: 400px;';

    public function getHeading(): ?string
    {
        return 'Gündəlik Baxışlar (' . $this->getPeriodLabel() . ')';
    }

    public function getDescription(): ?string
    {
        return 'Seçilmiş dövr üzrə günbəgün sayta giriş sayı';
    }

    protected function getData(): array
    {
        [$start, $end] = $this->getDateRange();
        $daysCount = (int) round($start->diffInDays($end) + 1);

        $labels = [];
        $counts = [];

        // 35 gün və daha azdırsa (məsələn, seçilmiş hər hansı bir ay və ya son 30 gün)
        if ($daysCount <= 35) {
            $current = $start->copy();
            while ($current->lte($end)) {
                $labels[] = $current->format('d.m');
                $counts[] = SiteVisit::whereBetween('created_at', [
                    $current->copy()->startOfDay(),
                    $current->copy()->endOfDay(),
                ])->count();
                $current->addDay();
            }
        } elseif ($daysCount <= 90) {
            // 3 aya qədər dövrlərdə 2 gündən bir intervalla
            $current = $start->copy();
            while ($current->lte($end)) {
                $labels[] = $current->format('d.m');
                $counts[] = SiteVisit::whereBetween('created_at', [
                    $current->copy()->startOfDay(),
                    $current->copy()->endOfDay(),
                ])->count();
                $current->addDays(2);
            }
        } else {
            // Tam il seçildikdə, seçilmiş son 30 günün günbəgün dinamikasını göstər
            $current = $end->copy()->subDays(29)->startOfDay();
            while ($current->lte($end)) {
                $labels[] = $current->format('d.m');
                $counts[] = SiteVisit::whereBetween('created_at', [
                    $current->copy()->startOfDay(),
                    $current->copy()->endOfDay(),
                ])->count();
                $current->addDay();
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Baxış Sayı',
                    'data' => $counts,
                    'backgroundColor' => '#6366f1',
                    'borderRadius' => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }
}

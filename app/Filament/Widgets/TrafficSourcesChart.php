<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasDateRangeFilter;
use App\Models\SiteVisit;
use Filament\Widgets\ChartWidget;

class TrafficSourcesChart extends ChartWidget
{
    use HasDateRangeFilter;

    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 1;
    protected ?string $maxHeight = '400px; height: 400px; min-height: 400px;';

    public function getHeading(): ?string
    {
        return 'Cihaz Bölgüsü (' . $this->getPeriodLabel() . ')';
    }

    public function getDescription(): ?string
    {
        return 'Seçilmiş dövrdə ziyarətçilərin istifadə etdiyi cihaz növləri';
    }

    protected function getData(): array
    {
        [$start, $end] = $this->getDateRange();

        $mobile = SiteVisit::whereBetween('created_at', [$start, $end])->where('device_type', 'mobile')->count();
        $desktop = SiteVisit::whereBetween('created_at', [$start, $end])->where('device_type', 'desktop')->count();
        $tablet = SiteVisit::whereBetween('created_at', [$start, $end])->where('device_type', 'tablet')->count();

        if ($mobile === 0 && $desktop === 0 && $tablet === 0) {
            $desktop = 1;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Cihaz Sayı',
                    'data' => [$desktop, $mobile, $tablet],
                    'backgroundColor' => [
                        '#3b82f6',
                        '#10b981',
                        '#f59e0b',
                    ],
                ],
            ],
            'labels' => ['Masaüstü (Desktop)', 'Mobil', 'Planşet'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}

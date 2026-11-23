<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasDateRangeFilter;
use App\Models\SiteVisit;
use Filament\Widgets\ChartWidget;

class TrafficChannelsChart extends ChartWidget
{
    use HasDateRangeFilter;

    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 1;
    protected ?string $maxHeight = '400px; height: 400px; min-height: 400px;';

    public function getHeading(): ?string
    {
        return 'Trafik Kanalları & AI / Axtarış (' . $this->getPeriodLabel() . ')';
    }

    public function getDescription(): ?string
    {
        return 'Google, AI tövsiyələri (ChatGPT, Perplexity), axtarış sistemləri və birbaşa girişlər';
    }

    protected function getData(): array
    {
        [$start, $end] = $this->getDateRange();

        $googleCount = SiteVisit::whereBetween('created_at', [$start, $end])->fromGoogle()->count();
        $aiCount = SiteVisit::whereBetween('created_at', [$start, $end])->fromAi()->count();
        $otherSearchCount = SiteVisit::whereBetween('created_at', [$start, $end])->fromOtherSearch()->count();
        $socialCount = SiteVisit::whereBetween('created_at', [$start, $end])->fromSocial()->count();
        $directCount = SiteVisit::whereBetween('created_at', [$start, $end])->direct()->count();

        // If no visits recorded yet in this range, provide default view
        if ($googleCount === 0 && $aiCount === 0 && $otherSearchCount === 0 && $socialCount === 0 && $directCount === 0) {
            $directCount = 1;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Ziyarət Sayı',
                    'data' => [
                        $googleCount,
                        $aiCount,
                        $otherSearchCount,
                        $socialCount,
                        $directCount,
                    ],
                    'backgroundColor' => [
                        '#3b82f6', // Google - Blue
                        '#10b981', // AI - Emerald
                        '#f59e0b', // Other Search - Amber
                        '#f43f5e', // Social - Rose
                        '#94a3b8', // Direct - Slate
                    ],
                ],
            ],
            'labels' => [
                'Google Axtarış',
                'AI Tövsiyələri (ChatGPT / Perplexity)',
                'Digər Axtarış (Yandex / Bing)',
                'Sosial Şəbəkələr',
                'Birbaşa Giriş (Direct)',
            ],
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

<?php

namespace App\Filament\Widgets\Concerns;

use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

trait HasDateRangeFilter
{
    use InteractsWithPageFilters;

    /**
     * Clear cached data when filters are updated so charts and tables re-render fresh data.
     */
    public function updatedPageFilters(): void
    {
        if (property_exists($this, 'cachedData')) {
            $this->cachedData = null;
        }

        if (method_exists($this, 'updateChartData')) {
            $this->updateChartData();
        }

        if (method_exists($this, 'resetTable')) {
            $this->resetTable();
        }
    }

    /**
     * Ensure chart cachedData is always freshly computed to reflect reactive pageFilters.
     */
    protected function getCachedData(): array
    {
        return $this->getData();
    }

    /**
     * Returns the Carbon start and end dates based on active pageFilters.
     * Defaults to the last 30 days (29 days ago until today).
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function getDateRange(): array
    {
        $period = $this->pageFilters['period'] ?? null;
        $startDate = $this->pageFilters['startDate'] ?? null;
        $endDate = $this->pageFilters['endDate'] ?? null;

        // If dates are explicitly provided
        if (filled($startDate) && filled($endDate)) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();

            if ($start->gt($end)) {
                $temp = $start;
                $start = $end->copy()->startOfDay();
                $end = $temp->copy()->endOfDay();
            }

            return [$start, $end];
        }

        // Otherwise resolve based on period
        if (filled($period)) {
            if ($period === 'last_30_days') {
                return [Carbon::now()->subDays(29)->startOfDay(), Carbon::now()->endOfDay()];
            }
            if ($period === 'this_month') {
                return [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()];
            }
            if ($period === 'last_month') {
                return [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()];
            }
            if ($period === 'this_year') {
                return [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()];
            }
            if ($period === 'last_year') {
                return [Carbon::now()->subYear()->startOfYear(), Carbon::now()->subYear()->endOfYear()];
            }
            if ($period === 'last_365_days') {
                return [Carbon::now()->subDays(365)->startOfDay(), Carbon::now()->endOfDay()];
            }
            if (preg_match('/^(\d{4})-(\d{2})$/', $period, $matches)) {
                $date = Carbon::create((int) $matches[1], (int) $matches[2], 1);
                return [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()];
            }
        }

        // Default: last 30 days
        return [Carbon::now()->subDays(29)->startOfDay(), Carbon::now()->endOfDay()];
    }

    /**
     * Human-friendly label for current period (e.g. "Son 30 gün", "Noyabr 2026").
     */
    protected function getPeriodLabel(): string
    {
        [$start, $end] = $this->getDateRange();
        $diffDays = (int) round($start->diffInDays($end));

        $azMonths = [
            1 => 'Yanvar', 2 => 'Fevral', 3 => 'Mart', 4 => 'Aprel',
            5 => 'May', 6 => 'İyun', 7 => 'İyul', 8 => 'Avqust',
            9 => 'Sentyabr', 10 => 'Oktyabr', 11 => 'Noyabr', 12 => 'Dekabr',
        ];

        // If the date range is a full calendar month
        if ($start->day === 1 && $end->day === $end->copy()->endOfMonth()->day && $start->isSameMonth($end)) {
            return ($azMonths[$start->month] ?? $start->format('F')) . ' ' . $start->year;
        }

        // If the date range is roughly 30 days up to today
        if (($diffDays === 29 || $diffDays === 30) && $end->isToday()) {
            return 'Son 30 gün';
        }

        // If the date range is a full calendar year
        if ($start->isSameYear($end) && $start->month === 1 && $start->day === 1 && $end->month === 12 && $end->day === 31) {
            return $start->year . '-ci il';
        }

        return $start->format('d.m.Y') . ' — ' . $end->format('d.m.Y');
    }
}

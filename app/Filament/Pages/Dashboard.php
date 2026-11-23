<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DailyVisitsChart;
use App\Filament\Widgets\MonthlyVisitsChart;
use App\Filament\Widgets\ReferrersBreakdownWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\TopBlogPostsWidget;
use App\Filament\Widgets\TopPagesWidget;
use App\Filament\Widgets\TopProjectsWidget;
use App\Filament\Widgets\TrafficChannelsChart;
use App\Filament\Widgets\TrafficSourcesChart;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    protected static ?string $navigationLabel = 'İdarəetmə Paneli';

    public function getTitle(): string|Htmlable
    {
        return 'İdarəetmə Paneli';
    }

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    public function getColumns(): int|array
    {
        return 2;
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Statistika Dövrü və Tarix Filtri')
                    ->columnSpanFull()
                    ->description('Bütün vidcetlər seçilmiş dövrə (aylar, illər və ya xüsusi tarixlər) əsasən avtomatik yenilənir.')
                    ->icon('heroicon-o-funnel')
                    ->collapsible()
                    ->schema([
                        Select::make('period')
                            ->label('Dövr Seçimi (Aylar və İllər)')
                            ->options(self::getPeriodOptions())
                            ->default('last_30_days')
                            ->native(false)
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                self::applyPeriodDates($state, $set);
                            }),

                        DatePicker::make('startDate')
                            ->label('Başlanğıc Tarixi')
                            ->default(Carbon::now()->subDays(29)->toDateString())
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('period', 'custom')),

                        DatePicker::make('endDate')
                            ->label('Bitmə Tarixi')
                            ->default(Carbon::now()->toDateString())
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('period', 'custom')),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 3,
                    ]),
            ]);
    }

    public static function getPeriodOptions(): array
    {
        $azMonths = [
            1 => 'Yanvar', 2 => 'Fevral', 3 => 'Mart', 4 => 'Aprel',
            5 => 'May', 6 => 'İyun', 7 => 'İyul', 8 => 'Avqust',
            9 => 'Sentyabr', 10 => 'Oktyabr', 11 => 'Noyabr', 12 => 'Dekabr',
        ];

        $now = Carbon::now();
        $currentMonthName = ($azMonths[$now->month] ?? $now->format('F')) . ' ' . $now->year;
        $prevMonth = Carbon::now()->subMonth();
        $prevMonthName = ($azMonths[$prevMonth->month] ?? $prevMonth->format('F')) . ' ' . $prevMonth->year;

        $options = [
            'last_30_days' => 'Son 30 gün (Defolt)',
            'this_month' => 'Cari ay (' . $currentMonthName . ')',
            'last_month' => 'Keçən ay (' . $prevMonthName . ')',
            'this_year' => 'Bütün ' . $now->year . '-ci il',
            'last_year' => 'Bütün ' . ($now->year - 1) . '-ci il',
            'last_365_days' => 'Son 1 il (365 gün)',
        ];

        // Son 12 ayın hər biri tək-tək
        for ($i = 0; $i < 12; $i++) {
            $monthDate = Carbon::now()->subMonths($i);
            $key = $monthDate->format('Y-m');
            $label = ($azMonths[$monthDate->month] ?? $monthDate->format('F')) . ' ' . $monthDate->year;
            $options[$key] = $label;
        }

        $options['custom'] = 'Xüsusi tarix aralığı';

        return $options;
    }

    public static function applyPeriodDates(?string $state, Set $set): void
    {
        if (blank($state)) {
            return;
        }

        if ($state === 'last_30_days') {
            $set('startDate', Carbon::now()->subDays(29)->toDateString());
            $set('endDate', Carbon::now()->toDateString());
        } elseif ($state === 'this_month') {
            $set('startDate', Carbon::now()->startOfMonth()->toDateString());
            $set('endDate', Carbon::now()->endOfMonth()->toDateString());
        } elseif ($state === 'last_month') {
            $set('startDate', Carbon::now()->subMonth()->startOfMonth()->toDateString());
            $set('endDate', Carbon::now()->subMonth()->endOfMonth()->toDateString());
        } elseif ($state === 'this_year') {
            $set('startDate', Carbon::now()->startOfYear()->toDateString());
            $set('endDate', Carbon::now()->endOfYear()->toDateString());
        } elseif ($state === 'last_year') {
            $set('startDate', Carbon::now()->subYear()->startOfYear()->toDateString());
            $set('endDate', Carbon::now()->subYear()->endOfYear()->toDateString());
        } elseif ($state === 'last_365_days') {
            $set('startDate', Carbon::now()->subDays(365)->toDateString());
            $set('endDate', Carbon::now()->toDateString());
        } elseif (preg_match('/^\d{4}-\d{2}$/', $state)) {
            $date = Carbon::parse($state . '-01');
            $set('startDate', $date->startOfMonth()->toDateString());
            $set('endDate', $date->endOfMonth()->toDateString());
        }
    }

    public function getWidgets(): array
    {
        return [
            StatsOverviewWidget::class,
            MonthlyVisitsChart::class,
            DailyVisitsChart::class,
            TrafficSourcesChart::class,
            TrafficChannelsChart::class,
            ReferrersBreakdownWidget::class,
            TopPagesWidget::class,
            TopBlogPostsWidget::class,
            TopProjectsWidget::class,
        ];
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\Contract1;
use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class FinancialOverviewChart extends ChartWidget
{
    protected ?string $heading = '💰 Financial Overview - Monthly Comparison';
    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        // Get the last 6 months for comparison
        $months = collect(range(5, 0))->map(function ($monthsBack) {
            return Carbon::now()->subMonths($monthsBack);
        });

        // Expected revenue from active contracts
        $expectedRevenue = $months->map(function ($month) {
            return Contract1::whereHas('unit')
                ->where('contract1s.status', 'active')
                ->where('contract1s.start_date', '<=', $month->endOfMonth())
                ->where('contract1s.end_date', '>=', $month->startOfMonth())
                ->join('units', 'contract1s.unit_id', '=', 'units.id')
                ->sum('units.rental_price') ?: 0;
        });

        // Actual payments received
        $actualPayments = $months->map(function ($month) {
            return Payment::whereYear('payment_date', $month->year)
                ->whereMonth('payment_date', $month->month)
                ->sum('amount') ?: 0;
        });

        // Collection rate calculation
        $collectionRate = $months->map(function ($month) use ($expectedRevenue, $actualPayments, $months) {
            $index = $months->search($month);
            $expected = $expectedRevenue[$index];
            $actual = $actualPayments[$index];
            
            return $expected > 0 ? round(($actual / $expected) * 100, 1) : 0;
        });

        return [
            'datasets' => [
                [
                    'label' => 'Expected Revenue (JOD)',
                    'data' => $expectedRevenue->toArray(),
                    'backgroundColor' => 'rgba(79, 70, 229, 0.2)',
                    'borderColor' => 'rgb(79, 70, 229)',
                    'borderWidth' => 3,
                    'type' => 'bar',
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Actual Payments (JOD)',
                    'data' => $actualPayments->toArray(),
                    'backgroundColor' => 'rgba(34, 197, 94, 0.2)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 3,
                    'type' => 'bar',
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Collection Rate (%)',
                    'data' => $collectionRate->toArray(),
                    'backgroundColor' => 'rgba(251, 146, 60, 0.8)',
                    'borderColor' => 'rgb(251, 146, 60)',
                    'borderWidth' => 2,
                    'type' => 'line',
                    'yAxisID' => 'y1',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $months->map(fn($month) => $month->format('M Y'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                    'callbacks' => [
                        'label' => 'function(context) {
                            let label = context.dataset.label || "";
                            if (label) {
                                label += ": ";
                            }
                            if (context.dataset.yAxisID === "y1") {
                                label += context.parsed.y + "%";
                            } else {
                                label += context.parsed.y.toLocaleString() + " JOD";
                            }
                            return label;
                        }'
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'display' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Month',
                    ],
                ],
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'title' => [
                        'display' => true,
                        'text' => 'Amount (JOD)',
                    ],
                    'ticks' => [
                        'callback' => 'function(value) { return value.toLocaleString() + " JOD"; }',
                    ],
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'title' => [
                        'display' => true,
                        'text' => 'Collection Rate (%)',
                    ],
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                    'ticks' => [
                        'callback' => 'function(value) { return value + "%"; }',
                    ],
                ],
            ],
            'interaction' => [
                'mode' => 'nearest',
                'axis' => 'x',
                'intersect' => false,
            ],
        ];
    }

    /**
     * Update every 10 minutes
     */
    protected static ?string $pollingInterval = '10m';
}

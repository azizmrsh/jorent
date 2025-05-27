<?php

namespace App\Filament\Widgets;

use App\Models\Contract1;
use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class RevenueAnalyticsChart extends ChartWidget
{
    protected ?string $heading = '💰 Revenue Analytics (Last 6 Months)';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        // Get the last 6 months
        $months = collect(range(5, 0))->map(function ($monthsBack) {
            return Carbon::now()->subMonths($monthsBack);
        });

        // Calculate monthly revenue from active contracts
        $monthlyRevenue = $months->map(function ($month) {
            // Revenue from contracts that were active during this month
            $revenue = Contract1::whereHas('unit')
                ->where('contract1s.status', 'active')
                ->where('contract1s.start_date', '<=', $month->endOfMonth())
                ->where('contract1s.end_date', '>=', $month->startOfMonth())
                ->join('units', 'contract1s.unit_id', '=', 'units.id')
                ->sum('units.rental_price');
            
            return $revenue ?: 0;
        });

        // Calculate total payments received per month
        $monthlyPayments = $months->map(function ($month) {
            return Payment::whereYear('payment_date', $month->year)
                ->whereMonth('payment_date', $month->month)
                ->sum('amount') ?: 0;
        });

        return [
            'datasets' => [
                [
                    'label' => 'Expected Revenue (JOD)',
                    'data' => $monthlyRevenue->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'fill' => true,
                ],
                [
                    'label' => 'Actual Payments (JOD)',
                    'data' => $monthlyPayments->toArray(),
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                    'fill' => true,
                ],
            ],
            'labels' => $months->map(fn($month) => $month->format('M Y'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
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
                    'display' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Amount (JOD)',
                    ],
                    'ticks' => [
                        'callback' => 'function(value) { return value.toLocaleString() + " JOD"; }',
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
     * Update every 5 minutes
     */
    protected static ?string $pollingInterval = '5m';
}

<?php

namespace App\Filament\Widgets;

use App\Models\Contract1;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class ContractExpirationChart extends ChartWidget
{
    protected ?string $heading = '📅 Contract Expiration Timeline';
    protected static ?int $sort = 8;
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        // Get next 6 months for expiration tracking
        $months = collect(range(0, 5))->map(function ($monthsAhead) {
            return Carbon::now()->addMonths($monthsAhead);
        });

        // Count contracts expiring each month
        $expiringContracts = $months->map(function ($month) {
            return Contract1::where('status', 'active')
                ->whereYear('end_date', $month->year)
                ->whereMonth('end_date', $month->month)
                ->count();
        });

        // Count new contracts starting each month (based on start_date)
        $newContracts = $months->map(function ($month) {
            return Contract1::whereYear('start_date', $month->year)
                ->whereMonth('start_date', $month->month)
                ->count();
        });

        // Calculate net change (new - expiring)
        $netChange = $months->map(function ($month) use ($months, $newContracts, $expiringContracts) {
            $index = $months->search($month);
            return $newContracts[$index] - $expiringContracts[$index];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Contracts Expiring',
                    'data' => $expiringContracts->toArray(),
                    'backgroundColor' => 'rgba(239, 68, 68, 0.6)',
                    'borderColor' => 'rgb(239, 68, 68)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'New Contracts',
                    'data' => $newContracts->toArray(),
                    'backgroundColor' => 'rgba(34, 197, 94, 0.6)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Net Change',
                    'data' => $netChange->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.3)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 3,
                    'type' => 'line',
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
                        'text' => 'Number of Contracts',
                    ],
                    'beginAtZero' => true,
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
     * Update every 24 hours since contract dates don't change frequently
     */
    protected static ?string $pollingInterval = '24h';
}

<?php

namespace App\Filament\Resources\PropertyResource\Widgets;

use App\Models\Property;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class PropertyMonthlyTrendsChart extends ChartWidget
{
    protected static ?string $heading = '📈 Properties Added - Monthly Trends';    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 6;

    protected static ?string $maxHeight = '350px';

    protected function getData(): array
    {
        $months = [];
        $data = [];
        
        // آخر 12 شهر
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');
            
            $count = Property::whereYear('created_at', $date->year)
                           ->whereMonth('created_at', $date->month)
                           ->count();
            $data[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Properties Added',
                    'data' => $data,
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                    'borderWidth' => 3,
                    'pointBackgroundColor' => 'rgba(59, 130, 246, 1)',
                    'pointBorderColor' => '#fff',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 6,
                    'pointHoverRadius' => 8,
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
            'responsive' => true,
            'maintainAspectRatio' => false,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'grid' => [
                        'color' => 'rgba(0, 0, 0, 0.1)',
                    ],
                    'ticks' => [
                        'font' => [
                            'size' => 12,
                        ],
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'font' => [
                            'size' => 12,
                        ],
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                'tooltip' => [
                    'backgroundColor' => 'rgba(0, 0, 0, 0.8)',
                    'titleColor' => '#fff',
                    'bodyColor' => '#fff',
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                    'borderWidth' => 1,
                    'cornerRadius' => 8,
                ],
            ],            'elements' => [
                'point' => [
                    'hoverBorderWidth' => 3,
                ],
            ],
        ];
    }
}

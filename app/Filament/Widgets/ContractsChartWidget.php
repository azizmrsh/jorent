<?php

namespace App\Filament\Widgets;

use App\Models\Contract;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Carbon\Carbon;

class ContractsChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Contracts by Month';
    protected static ?int $sort = 3;
    
    protected function getData(): array
    {
        $data = Trend::model(Contract::class)
            ->between(
                start: Carbon::now()->subMonths(6),
                end: Carbon::now(),
            )
            ->perMonth()
            ->count();
            
        return [
            'datasets' => [
                [
                    'label' => 'Contracts Created',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => [
                        'rgba(255, 159, 64, 0.6)',
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(153, 102, 255, 0.6)',
                        'rgba(201, 203, 207, 0.6)',
                        'rgba(255, 205, 86, 0.6)',
                    ],
                    'borderColor' => [
                        'rgb(255, 159, 64)',
                        'rgb(75, 192, 192)',
                        'rgb(54, 162, 235)',
                        'rgb(153, 102, 255)',
                        'rgb(201, 203, 207)',
                        'rgb(255, 205, 86)',
                    ],
                    'borderWidth' => 1
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }
    
    protected function getType(): string
    {
        return 'bar';
    }
}

<?php

namespace App\Filament\Resources\PropertyResource\Widgets;

use App\Models\Property;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class PropertyTypeDistributionChart extends ChartWidget
{
    protected static ?string $heading = '📊 Property Type Distribution';    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 6;

    protected static ?string $maxHeight = '400px';

    protected function getData(): array
    {
        // Usage Type Data
        $residential = Property::where('type2', 'residential')->count();
        $commercial = Property::where('type2', 'commercial')->count();
        $industrial = Property::where('type2', 'industrial')->count();

        // Primary Type Data
        $villa = Property::where('type1', 'villa')->count();
        $house = Property::where('type1', 'house')->count();
        $building = Property::where('type1', 'building')->count();
        $warehouse = Property::where('type1', 'warehouse')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Usage Type',
                    'data' => [$residential, $commercial, $industrial],
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',   // أزرق للسكني
                        'rgba(34, 197, 94, 0.8)',    // أخضر للتجاري
                        'rgba(249, 115, 22, 0.8)',   // برتقالي للصناعي
                    ],
                    'borderColor' => [
                        'rgba(59, 130, 246, 1)',
                        'rgba(34, 197, 94, 1)',
                        'rgba(249, 115, 22, 1)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => [
                '🏠 Residential (' . $residential . ')',
                '🏢 Commercial (' . $commercial . ')',
                '🏭 Industrial (' . $industrial . ')',
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
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 20,
                        'font' => [
                            'size' => 14,
                            'weight' => 'bold',
                        ],
                    ],
                ],
                'tooltip' => [
                    'backgroundColor' => 'rgba(0, 0, 0, 0.8)',
                    'titleColor' => '#fff',
                    'bodyColor' => '#fff',
                    'borderColor' => '#fff',
                    'borderWidth' => 1,
                ],
            ],
            'cutout' => '60%',
            'elements' => [
                'arc' => [
                    'borderWidth' => 3,                ],
            ],
        ];
    }
}

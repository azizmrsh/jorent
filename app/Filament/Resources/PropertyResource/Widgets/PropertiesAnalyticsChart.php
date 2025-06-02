<?php

namespace App\Filament\Resources\PropertyResource\Widgets;

use App\Models\Property;
use App\Models\Unit;
use App\Models\Contract1;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class PropertiesAnalyticsChart extends ChartWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';
    
    public function getHeading(): string
    {
        return '📊 Properties Analytics';
    }
    
    public ?string $filter = '6months';
    
    protected function getFilters(): ?array
    {
        return [
            '3months' => 'Last 3 months',
            '6months' => 'Last 6 months',
            '12months' => 'Last 12 months',
        ];
    }

    protected function getData(): array
    {
        $months = match($this->filter) {
            '3months' => 3,
            '12months' => 12,
            default => 6,
        };

        // جمع البيانات لكل شهر
        $data = [];
        $labels = [];
        $propertiesData = [];
        $unitsData = [];
        $occupancyData = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();
            
            // العقارات المضافة في هذا الشهر
            $propertiesCount = Property::whereDate('created_at', '<=', $monthEnd)->count();
            
            // الوحدات المضافة في هذا الشهر
            $unitsCount = Unit::whereDate('created_at', '<=', $monthEnd)->count();
            
            // معدل الإشغال في هذا الشهر
            $totalUnits = Unit::whereDate('created_at', '<=', $monthEnd)->count();
            $occupiedUnits = Unit::whereHas('contracts', function ($query) use ($monthEnd) {
                $query->where('status', 'active')
                      ->where('start_date', '<=', $monthEnd);
            })->whereDate('created_at', '<=', $monthEnd)->count();
            
            $occupancyRate = $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100, 1) : 0;
            
            $labels[] = $date->format('M Y');
            $propertiesData[] = $propertiesCount;
            $unitsData[] = $unitsCount;
            $occupancyData[] = $occupancyRate;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Properties',
                    'type' => 'line',
                    'data' => $propertiesData,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'tension' => 0.4,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Total Units',
                    'type' => 'bar',
                    'data' => $unitsData,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.8)',
                    'borderColor' => 'rgb(16, 185, 129)',
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Occupancy Rate (%)',
                    'type' => 'line',
                    'data' => $occupancyData,
                    'borderColor' => 'rgb(245, 158, 11)',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'tension' => 0.4,
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $labels,
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
            'interaction' => [
                'mode' => 'index',
                'intersect' => false,
            ],
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'title' => [
                        'display' => true,
                        'text' => 'Count'
                    ]
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'title' => [
                        'display' => true,
                        'text' => 'Percentage (%)'
                    ],
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                    'max' => 100,
                ],
            ],
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
        ];
    }
}

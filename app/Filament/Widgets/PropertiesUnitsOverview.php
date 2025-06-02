<?php

namespace App\Filament\Widgets;

use App\Models\Property;
use App\Models\Unit;
use App\Models\Contract1;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PropertiesUnitsOverview extends ChartWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';
    
    public function getHeading(): string
    {
        return '🏢 Properties & Units Distribution';
    }
    
    protected function getData(): array
    {
        // إحصائيات أنواع العقارات
        $propertyTypes = Property::select('type1', DB::raw('count(*) as count'))
            ->groupBy('type1')
            ->get();
        
        // إحصائيات نوع الاستخدام
        $usageTypes = Property::select('type2', DB::raw('count(*) as count'))
            ->groupBy('type2')
            ->get();
        
        // إحصائيات حالة الوحدات
        $unitStatuses = Unit::leftJoin('contract1s', function($join) {
                $join->on('units.id', '=', 'contract1s.unit_id')
                     ->where('contract1s.status', '=', 'active');
            })
            ->select(
                DB::raw('CASE WHEN contract1s.id IS NOT NULL THEN "Rented" ELSE "Available" END as status'),
                DB::raw('count(units.id) as count')
            )
            ->groupBy('status')
            ->get();
        
        return [
            'datasets' => [
                [
                    'label' => 'Property Types',
                    'data' => $propertyTypes->pluck('count')->toArray(),
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',   // Blue for buildings
                        'rgba(34, 197, 94, 0.8)',    // Green for villas
                        'rgba(251, 191, 36, 0.8)',   // Yellow for houses
                        'rgba(168, 85, 247, 0.8)',   // Purple for warehouses
                    ],
                    'borderColor' => [
                        'rgba(59, 130, 246, 1)',
                        'rgba(34, 197, 94, 1)',
                        'rgba(251, 191, 36, 1)',
                        'rgba(168, 85, 247, 1)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $propertyTypes->map(function ($item) {
                return match($item->type1) {
                    'building' => '🏢 Buildings',
                    'villa' => '🏡 Villas',
                    'house' => '🏠 Houses',
                    'warehouse' => '🏪 Warehouses',
                    default => ucfirst($item->type1),
                };
            })->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
    
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            let label = context.label || "";
                            let value = context.parsed || 0;
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = ((value / total) * 100).toFixed(1);
                            return label + ": " + value + " (" + percentage + "%)";
                        }',
                    ],
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];
    }
}

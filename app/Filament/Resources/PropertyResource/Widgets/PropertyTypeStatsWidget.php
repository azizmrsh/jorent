<?php

namespace App\Filament\Resources\PropertyResource\Widgets;

use App\Models\Property;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PropertyTypeStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 3;

    protected function getStats(): array
    {
        $totalProperties = Property::count();
        
        // Calculate count for each property type
        $buildingCount = Property::where('type1', 'building')->count();
        $villaCount = Property::where('type1', 'villa')->count();
        $houseCount = Property::where('type1', 'house')->count();
        $warehouseCount = Property::where('type1', 'warehouse')->count();
        
        // Find most common type
        $types = [
            'Buildings' => $buildingCount,
            'Villas' => $villaCount,
            'Houses' => $houseCount,
            'Warehouses' => $warehouseCount
        ];
        
        $mostCommonType = array_keys($types, max($types))[0] ?? 'Mixed';
        $mostCommonCount = max($types);
        $percentage = $totalProperties > 0 ? round(($mostCommonCount / $totalProperties) * 100, 1) : 0;

        return [
            Stat::make('Most Common Type', $mostCommonType)
                ->description("{$mostCommonCount} properties ({$percentage}%)")
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20',
                ]),
        ];
    }
}

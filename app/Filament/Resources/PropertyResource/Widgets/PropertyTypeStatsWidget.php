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
        
        // Combined count
        $combinedCount = $buildingCount + $villaCount + $houseCount + $warehouseCount;
        
        // Create description with breakdown
        $breakdown = [];
        if ($buildingCount > 0) $breakdown[] = "Buildings: {$buildingCount}";
        if ($villaCount > 0) $breakdown[] = "Villas: {$villaCount}";
        if ($houseCount > 0) $breakdown[] = "Houses: {$houseCount}";
        if ($warehouseCount > 0) $breakdown[] = "Warehouses: {$warehouseCount}";
        
        $description = !empty($breakdown) ? implode(' • ', $breakdown) : "No properties by type";

        return [
            Stat::make('Property Types', number_format($combinedCount))
                ->description($description)
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20',
                ]),
        ];
    }
}

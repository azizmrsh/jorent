<?php

namespace App\Filament\Resources\PropertyResource\Widgets;

use App\Models\Property;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PropertyTypeStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';    protected function getStats(): array
    {
        $totalProperties = Property::count();
        
        // Calculate count for each property type
        $buildingCount = Property::where('type1', 'building')->count();
        $villaCount = Property::where('type1', 'villa')->count();
        $houseCount = Property::where('type1', 'house')->count();
        $warehouseCount = Property::where('type1', 'warehouse')->count();

        // Calculate percentages
        $buildingPercentage = $totalProperties > 0 ? round(($buildingCount / $totalProperties) * 100, 1) : 0;
        $villaPercentage = $totalProperties > 0 ? round(($villaCount / $totalProperties) * 100, 1) : 0;
        $housePercentage = $totalProperties > 0 ? round(($houseCount / $totalProperties) * 100, 1) : 0;
        $warehousePercentage = $totalProperties > 0 ? round(($warehouseCount / $totalProperties) * 100, 1) : 0;

        return [
            Stat::make('Buildings', number_format($buildingCount))
                ->description("{$buildingPercentage}% of total properties")
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20',
                ]),

            Stat::make('Villas', number_format($villaCount))
                ->description("{$villaPercentage}% of total properties")
                ->descriptionIcon('heroicon-m-home-modern')
                ->color('success')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20',
                ]),

            Stat::make('Houses', number_format($houseCount))
                ->description("{$housePercentage}% of total properties")
                ->descriptionIcon('heroicon-m-home')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20',
                ]),            Stat::make('Warehouses', number_format($warehouseCount))
                ->description("{$warehousePercentage}% of total properties")
                ->descriptionIcon('heroicon-m-cube')
                ->color('danger')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20',
                ]),
        ];
    }
}

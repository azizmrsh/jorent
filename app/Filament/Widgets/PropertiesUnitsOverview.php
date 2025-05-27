<?php

namespace App\Filament\Widgets;

use App\Models\Property;
use App\Models\Unit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PropertiesUnitsOverview extends BaseWidget
{
    protected static ?string $heading = '🏢 Properties & Units Overview';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        // Properties statistics
        $totalProperties = Property::count();
        $buildingCount = Property::where('type1', 'building')->count();
        $villaCount = Property::where('type1', 'villa')->count();
        $commercialProperties = Property::where('type2', 'commercial')->count();
        
        // Units statistics
        $totalUnits = Unit::count();
        $availableUnits = Unit::where('status', 'available')->count();
        $rentedUnits = Unit::where('status', 'rented')->count();
        $maintenanceUnits = Unit::where('status', 'under_maintenance')->count();
        
        // Calculate percentages
        $buildingPercentage = $totalProperties > 0 ? round(($buildingCount / $totalProperties) * 100, 1) : 0;
        $occupancyRate = $totalUnits > 0 ? round(($rentedUnits / $totalUnits) * 100, 1) : 0;
        $availabilityRate = $totalUnits > 0 ? round(($availableUnits / $totalUnits) * 100, 1) : 0;

        return [
            // 1. Buildings Count
            Stat::make('🏢 Buildings', number_format($buildingCount))
                ->description("{$buildingPercentage}% of total properties")
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary')
                ->chart([2, 4, 6, 8, 10, 12, $buildingCount])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20',
                ]),

            // 2. Commercial Properties
            Stat::make('🏪 Commercial', number_format($commercialProperties))
                ->description("Commercial properties")
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('warning')
                ->chart([1, 2, 3, 4, 5, 6, $commercialProperties])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20',
                ]),

            // 3. Unit Occupancy Rate
            Stat::make('📊 Occupancy Rate', $occupancyRate . '%')
                ->description("{$rentedUnits} rented out of {$totalUnits}")
                ->descriptionIcon('heroicon-m-chart-pie')
                ->color($occupancyRate >= 80 ? 'success' : ($occupancyRate >= 60 ? 'warning' : 'danger'))
                ->chart([60, 65, 70, 75, 80, 85, $occupancyRate])
                ->extraAttributes([
                    'class' => $occupancyRate >= 80 
                        ? 'bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20'
                        : ($occupancyRate >= 60 
                            ? 'bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20'
                            : 'bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20'),
                ]),

            // 4. Available Units
            Stat::make('🟢 Available Units', number_format($availableUnits))
                ->description("{$availabilityRate}% availability rate")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([5, 8, 12, 15, 18, 22, $availableUnits])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20',
                ]),
        ];
    }

    /**
     * Update every 5 minutes
     */
    protected static ?string $pollingInterval = '5m';
}

<?php

namespace App\Filament\Resources\PropertyResource\Widgets;

use App\Models\Property;
use App\Models\Unit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MaintenanceCostsWidget extends BaseWidget
{
    protected static ?int $sort = 8;
    protected int | string | array $columnSpan = 3;

    protected function getStats(): array
    {
        // Simulate maintenance costs calculation
        // In a real system, you would have a maintenance_costs table
        $totalProperties = Property::count();
        $propertiesWithUnits = Property::has('units')->count();
        
        // Estimate maintenance costs (approximately 2-3% of property value annually)
        $totalValue = Unit::whereHas('property')->sum('price');
        $estimatedAnnualMaintenance = $totalValue * 0.025; // 2.5% of total value
        $monthlyMaintenance = $estimatedAnnualMaintenance / 12;
        
        // Calculate average maintenance per property
        $avgMaintenancePerProperty = $propertiesWithUnits > 0 ? $monthlyMaintenance / $propertiesWithUnits : 0;

        return [
            Stat::make('Monthly Maintenance', '$' . number_format($monthlyMaintenance, 0))
                ->description("Avg: $" . number_format($avgMaintenancePerProperty, 0) . " per property")
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('danger')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20',
                ]),
        ];
    }
}

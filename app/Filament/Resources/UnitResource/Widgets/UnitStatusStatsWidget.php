<?php

namespace App\Filament\Resources\UnitResource\Widgets;

use App\Models\Unit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UnitStatusStatsWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $totalUnits = Unit::count();
        
        // Calculate count for each status
        $availableCount = Unit::where('status', 'available')->count();
        $rentedCount = Unit::where('status', 'rented')->count();
        $maintenanceCount = Unit::where('status', 'under_maintenance')->count();

        // Calculate percentages
        $availablePercentage = $totalUnits > 0 ? round(($availableCount / $totalUnits) * 100, 1) : 0;
        $rentedPercentage = $totalUnits > 0 ? round(($rentedCount / $totalUnits) * 100, 1) : 0;
        $maintenancePercentage = $totalUnits > 0 ? round(($maintenanceCount / $totalUnits) * 100, 1) : 0;

        return [
            Stat::make('Available', number_format($availableCount))
                ->description("$availablePercentage% of total units")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20',
                ]),

            Stat::make('Rented', number_format($rentedCount))
                ->description("$rentedPercentage% of total units")
                ->descriptionIcon('heroicon-m-key')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20',
                ]),

            Stat::make('Under Maintenance', number_format($maintenanceCount))
                ->description("$maintenancePercentage% of total units")
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('danger')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20',
                ]),
        ];
    }
}

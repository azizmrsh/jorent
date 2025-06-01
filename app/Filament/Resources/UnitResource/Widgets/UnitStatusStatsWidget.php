<?php

namespace App\Filament\Resources\UnitResource\Widgets;

use App\Models\Unit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UnitStatusStatsWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'auto';

    protected function getStats(): array
    {
        $totalUnits = Unit::count();
        
        // Calculate count for each status
        $availableCount = Unit::where('status', 'available')->count();
        $rentedCount = Unit::where('status', 'rented')->count();
        $maintenanceCount = Unit::where('status', 'under_maintenance')->count();
        $unavailableCount = Unit::where('status', 'unavailable')->count();
        $reservedCount = Unit::where('status', 'reserved')->count();

        // Combined count
        $combinedCount = $availableCount + $rentedCount + $maintenanceCount + $unavailableCount + $reservedCount;
        
        // Create description with breakdown
        $breakdown = [];
        if ($availableCount > 0) $breakdown[] = "Available: {$availableCount}";
        if ($rentedCount > 0) $breakdown[] = "Rented: {$rentedCount}";
        if ($maintenanceCount > 0) $breakdown[] = "Maintenance: {$maintenanceCount}";
        if ($unavailableCount > 0) $breakdown[] = "Unavailable: {$unavailableCount}";
        if ($reservedCount > 0) $breakdown[] = "Reserved: {$reservedCount}";
        
        $description = !empty($breakdown) ? implode(' • ', $breakdown) : "No units by status";

        return [
            Stat::make('Unit Status', number_format($combinedCount))
                ->description($description)
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20',
                ]),
        ];
    }
}

<?php

namespace App\Filament\Resources\UnitResource\Widgets;

use App\Models\Unit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UnitTypeStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'auto';

    protected function getStats(): array
    {
        $totalUnits = Unit::count();
        
        // Calculate count for each unit type
        $apartmentCount = Unit::where('unit_type', 'apartment')->count();
        $villaCount = Unit::where('unit_type', 'villa')->count();
        $officeCount = Unit::where('unit_type', 'office')->count();
        $shopCount = Unit::where('unit_type', 'shop')->count();
        $studioCount = Unit::where('unit_type', 'studio')->count();
        $warehouseCount = Unit::where('unit_type', 'warehouse')->count();
        
        // Combined count
        $combinedCount = $apartmentCount + $villaCount + $officeCount + $shopCount + $studioCount + $warehouseCount;
        
        // Create description with breakdown
        $breakdown = [];
        if ($apartmentCount > 0) $breakdown[] = "Apartments: {$apartmentCount}";
        if ($villaCount > 0) $breakdown[] = "Villas: {$villaCount}";
        if ($officeCount > 0) $breakdown[] = "Offices: {$officeCount}";
        if ($shopCount > 0) $breakdown[] = "Shops: {$shopCount}";
        if ($studioCount > 0) $breakdown[] = "Studios: {$studioCount}";
        if ($warehouseCount > 0) $breakdown[] = "Warehouses: {$warehouseCount}";
        
        $description = !empty($breakdown) ? implode(' • ', $breakdown) : "No units by type";

        return [
            Stat::make('Unit Types', number_format($combinedCount))
                ->description($description)
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20',
                ]),
        ];
    }
}

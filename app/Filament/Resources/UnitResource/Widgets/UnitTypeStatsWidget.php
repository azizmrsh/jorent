<?php

namespace App\Filament\Resources\UnitResource\Widgets;

use App\Models\Unit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UnitTypeStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 3;

    protected function getStats(): array
    {
        // Get most common unit type
        $apartmentCount = Unit::where('unit_type', 'apartment')->count();
        $villaCount = Unit::where('unit_type', 'villa')->count();
        $officeCount = Unit::where('unit_type', 'office')->count();
        $shopCount = Unit::where('unit_type', 'shop')->count();
        
        $types = [
            'Apartments' => $apartmentCount,
            'Villas' => $villaCount, 
            'Offices' => $officeCount,
            'Shops' => $shopCount
        ];
        
        $mostCommon = array_keys($types, max($types))[0] ?? 'Mixed';
        $totalTyped = array_sum($types);

        return [
            Stat::make('Unit Types', number_format($totalTyped))
                ->description("Most common: {$mostCommon}")
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20',
                ]),
        ];
    }
}

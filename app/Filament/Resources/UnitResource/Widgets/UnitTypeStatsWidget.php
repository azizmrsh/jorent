<?php

namespace App\Filament\Resources\UnitResource\Widgets;

use App\Models\Unit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UnitTypeStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $totalUnits = Unit::count();
        
        // Calculate count for each unit type
        $apartmentCount = Unit::where('unit_type', 'apartment')->count();
        $villaCount = Unit::where('unit_type', 'villa')->count();
        $officeCount = Unit::where('unit_type', 'office')->count();
        $shopCount = Unit::where('unit_type', 'shop')->count();

        // Calculate percentages
        $apartmentPercentage = $totalUnits > 0 ? round(($apartmentCount / $totalUnits) * 100, 1) : 0;
        $villaPercentage = $totalUnits > 0 ? round(($villaCount / $totalUnits) * 100, 1) : 0;
        $officePercentage = $totalUnits > 0 ? round(($officeCount / $totalUnits) * 100, 1) : 0;
        $shopPercentage = $totalUnits > 0 ? round(($shopCount / $totalUnits) * 100, 1) : 0;

        return [
            Stat::make('Apartments', number_format($apartmentCount))
                ->description("$apartmentPercentage% of total units")
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20',
                ]),

            Stat::make('Villas', number_format($villaCount))
                ->description("$villaPercentage% of total units")
                ->descriptionIcon('heroicon-m-home-modern')
                ->color('success')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20',
                ]),

            Stat::make('Offices', number_format($officeCount))
                ->description("$officePercentage% of total units")
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20',
                ]),

            Stat::make('Shops', number_format($shopCount))
                ->description("$shopPercentage% of total units")
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('info')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-cyan-50 to-cyan-100 dark:from-cyan-900/20 dark:to-cyan-800/20',
                ]),
        ];
    }
}

<?php

namespace App\Filament\Resources\UnitResource\Widgets;

use App\Models\Unit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UnitPriceStatsWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $totalUnits = Unit::count();
        $totalRevenue = Unit::sum('rental_price');
        $averagePrice = $totalUnits > 0 ? round($totalRevenue / $totalUnits, 2) : 0;
        
        // Find highest and lowest priced units
        $highestPrice = Unit::max('rental_price') ?? 0;
        $lowestPrice = Unit::min('rental_price') ?? 0;

        return [
            Stat::make('Average Price', number_format($averagePrice, 2) . ' JOD')
                ->description("Average rental price per unit")
                ->descriptionIcon('heroicon-m-calculator')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/20',
                ]),

            Stat::make('Total Revenue Potential', number_format($totalRevenue, 2) . ' JOD')
                ->description("Sum of all unit prices")
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20',
                ]),

            Stat::make('Highest Price', number_format($highestPrice, 2) . ' JOD')
                ->description("Most expensive unit")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20',
                ]),

            Stat::make('Lowest Price', number_format($lowestPrice, 2) . ' JOD')
                ->description("Most affordable unit")
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('info')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-cyan-50 to-cyan-100 dark:from-cyan-900/20 dark:to-cyan-800/20',
                ]),
        ];
    }
}

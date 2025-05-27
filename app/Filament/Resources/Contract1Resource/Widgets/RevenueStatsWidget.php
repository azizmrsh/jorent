<?php

namespace App\Filament\Resources\Contract1Resource\Widgets;

use App\Models\Contract1;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class RevenueStatsWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';    protected function getStats(): array
    {
        // Get total revenue from active contracts
        $totalRevenue = Contract1::whereHas('unit')
            ->where('contract1s.status', 'active')
            ->join('units', 'contract1s.unit_id', '=', 'units.id')
            ->sum('units.rental_price');
              // Get this month's revenue
        $thisMonthRevenue = Contract1::whereHas('unit')
            ->where('contract1s.status', 'active')
            ->where('contract1s.start_date', '<=', now())
            ->where('contract1s.end_date', '>=', now())
            ->join('units', 'contract1s.unit_id', '=', 'units.id')
            ->sum('units.rental_price');
            
        // Get average rental price
        $averageRent = Contract1::whereHas('unit')
            ->where('contract1s.status', 'active')
            ->join('units', 'contract1s.unit_id', '=', 'units.id')
            ->avg('units.rental_price');
              // Calculate growth percentage
        $lastMonthRevenue = Contract1::whereHas('unit')
            ->where('contract1s.status', 'active')
            ->where('contract1s.start_date', '<=', now()->subMonth())
            ->where('contract1s.end_date', '>=', now()->subMonth())
            ->join('units', 'contract1s.unit_id', '=', 'units.id')
            ->sum('units.rental_price');
            
        $growthPercentage = $lastMonthRevenue > 0 ? 
            round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1) : 0;

        return [
            Stat::make('Total Revenue', 'JOD ' . number_format($totalRevenue, 2))
                ->description("From all active contracts")
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, 3, 5, 8])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20',
                ]),

            Stat::make('Monthly Revenue', 'JOD ' . number_format($thisMonthRevenue, 2))
                ->description($growthPercentage >= 0 ? "+$growthPercentage% from last month" : "$growthPercentage% from last month")
                ->descriptionIcon($growthPercentage >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($growthPercentage >= 0 ? 'success' : 'danger')
                ->chart([4, 6, 5, 8, 7, 9, 6, 7])
                ->extraAttributes([
                    'class' => $growthPercentage >= 0 
                        ? 'bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20'
                        : 'bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20',
                ]),

            Stat::make('Average Rent', 'JOD ' . number_format($averageRent, 2))
                ->description("Per active contract")
                ->descriptionIcon('heroicon-m-calculator')
                ->color('info')
                ->chart([3, 4, 5, 6, 5, 4, 6, 5])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20',
                ]),
        ];
    }
}

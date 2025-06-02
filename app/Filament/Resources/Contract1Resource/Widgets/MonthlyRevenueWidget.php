<?php

namespace App\Filament\Resources\Contract1Resource\Widgets;

use App\Models\Contract1;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MonthlyRevenueWidget extends BaseWidget
{
    protected static ?int $sort = 7;    protected function getStats(): array
    {
        $currentMonth = now()->format('Y-m');
        $monthlyRevenue = Contract1::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$currentMonth])
            ->sum('rent_amount');
        
        $lastMonth = now()->subMonth()->format('Y-m');
        $lastMonthRevenue = Contract1::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$lastMonth])
            ->sum('rent_amount');

        $growthPercentage = $lastMonthRevenue > 0 
            ? round((($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1) 
            : 0;

        return [
            Stat::make('Monthly Revenue', '$' . number_format($monthlyRevenue, 2))
                ->description($growthPercentage > 0 ? "+{$growthPercentage}% from last month" : "From current month")
                ->descriptionIcon($growthPercentage > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-banknotes')
                ->color($growthPercentage > 0 ? 'success' : 'primary')
                ->chart([3, 5, 7, 6, 8, 9, 7, 10])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20',
                ]),
        ];
    }
}

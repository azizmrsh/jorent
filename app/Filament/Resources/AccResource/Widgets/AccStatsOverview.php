<?php

namespace App\Filament\Resources\AccResource\Widgets;

use App\Models\Acc;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class AccStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalAccounts = Acc::count();
        $activeAccounts = Acc::where('status', 'active')->count();
        $thisMonthAccounts = Acc::whereMonth('hired_date', Carbon::now()->month)
            ->whereYear('hired_date', Carbon::now()->year)
            ->count();

        return [
            Stat::make('Total Accounts', $totalAccounts)
                ->description('Total number of registered accounts')
                ->descriptionIcon('heroicon-o-users')
                ->color('primary'),
                
            Stat::make('Active Accounts', $activeAccounts)
                ->description('Currently active accounts')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),
                
            Stat::make('This Month', $thisMonthAccounts)
                ->description('New accounts added this month')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('info'),
        ];
    }
}

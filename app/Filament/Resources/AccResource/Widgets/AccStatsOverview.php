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
        // Current month data
        $totalAccounts = Acc::count();
        $activeAccounts = Acc::where('status', 'active')->count();
        $thisMonthAccounts = Acc::whereMonth('hired_date', Carbon::now()->month)
            ->whereYear('hired_date', Carbon::now()->year)
            ->count();

        // Previous month data for comparison
        $lastMonth = Carbon::now()->subMonth();
        $totalAccountsLastMonth = Acc::where('created_at', '<', Carbon::now()->startOfMonth())->count();
        $activeAccountsLastMonth = Acc::where('status', 'active')
            ->where('created_at', '<', Carbon::now()->startOfMonth())
            ->count();
        $lastMonthAccounts = Acc::whereMonth('hired_date', $lastMonth->month)
            ->whereYear('hired_date', $lastMonth->year)
            ->count();

        // Calculate percentage changes
        $totalChange = $this->calculatePercentageChange($totalAccounts, $totalAccountsLastMonth);
        $activeChange = $this->calculatePercentageChange($activeAccounts, $activeAccountsLastMonth);
        $monthlyChange = $this->calculatePercentageChange($thisMonthAccounts, $lastMonthAccounts);

        // Incomplete accounts (missing phone or address)
        $incompleteAccounts = Acc::where(function($query) {
            $query->whereNull('phone')
                  ->orWhere('phone', '')
                  ->orWhereNull('address')
                  ->orWhere('address', '');
        })->count();
        
        $incompletePercentage = $totalAccounts > 0 ? round(($incompleteAccounts / $totalAccounts) * 100, 1) : 0;
        
        return [
            Stat::make('Total Accounts', number_format($totalAccounts))
                ->description($totalChange['description'])
                ->descriptionIcon($totalChange['icon'])
                ->descriptionColor($totalChange['color'])
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('primary'),
                
            Stat::make('Active Accounts', number_format($activeAccounts))
                ->description($activeChange['description'])
                ->descriptionIcon($activeChange['icon'])
                ->descriptionColor($activeChange['color'])
                ->chart([3, 8, 5, 10, 12, 7, 15])
                ->color('success'),
                
            Stat::make('New This Month', number_format($thisMonthAccounts))
                ->description($monthlyChange['description'])
                ->descriptionIcon($monthlyChange['icon'])
                ->descriptionColor($monthlyChange['color'])
                ->chart([2, 4, 6, 3, 8, 5, 10])
                ->color('info'),

            Stat::make('Incomplete Profiles', number_format($incompleteAccounts))
                ->description($incompletePercentage . '% missing phone or address')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->descriptionColor('warning')
                ->chart([5, 3, 8, 2, 6, 4, 7])
                ->color('warning'),
        ];
    }

    private function calculatePercentageChange($current, $previous): array
    {        if ($previous == 0) {
            if ($current > 0) {
                return [
                    'description' => '100% increase from last period',
                    'icon' => 'heroicon-o-arrow-trending-up',
                    'color' => 'success'
                ];
            }
            return [
                'description' => 'No change from last period',
                'icon' => 'heroicon-o-minus-circle',
                'color' => 'gray'
            ];
        }

        $percentageChange = round((($current - $previous) / $previous) * 100, 1);
        
        if ($percentageChange > 0) {
            return [
                'description' => "+{$percentageChange}% from last period",
                'icon' => 'heroicon-o-arrow-trending-up',
                'color' => 'success'
            ];
        } elseif ($percentageChange < 0) {
            return [
                'description' => "{$percentageChange}% from last period",
                'icon' => 'heroicon-o-arrow-trending-down',
                'color' => 'danger'
            ];
        } else {
            return [
                'description' => 'No change from last period',
                'icon' => 'heroicon-o-minus-circle',
                'color' => 'gray'
            ];
        }
    }
}

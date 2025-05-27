<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Tenant;
use App\Models\Acc;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class UsersTenantsOverview extends BaseWidget
{
    protected ?string $heading = '👥 Users & Tenants Summary';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        // Users/Managers statistics
        $totalManagers = User::count();
        $activeManagers = User::where('status', 'active')->count();
        $newManagersThisMonth = User::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        
        // Account managers statistics
        $totalAccManagers = Acc::count();
        $activeAccManagers = Acc::where('status', 'active')->count();
        
        // Tenants statistics
        $totalTenants = Tenant::count();
        $newTenantsThisMonth = Tenant::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        
        // Calculate percentages
        $activeManagersPercentage = $totalManagers > 0 ? round(($activeManagers / $totalManagers) * 100, 1) : 0;
        $activeAccPercentage = $totalAccManagers > 0 ? round(($activeAccManagers / $totalAccManagers) * 100, 1) : 0;

        return [
            // 1. System Managers
            Stat::make('👔 System Managers', number_format($totalManagers))
                ->description("{$activeManagers} active ({$activeManagersPercentage}%)")
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary')
                ->chart([3, 5, 7, 9, 11, 13, $totalManagers])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20',
                ]),

            // 2. Account Managers
            Stat::make('📋 Account Managers', number_format($totalAccManagers))
                ->description("{$activeAccManagers} active ({$activeAccPercentage}%)")
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('info')
                ->chart([2, 4, 6, 8, 10, 12, $totalAccManagers])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-cyan-50 to-cyan-100 dark:from-cyan-900/20 dark:to-cyan-800/20',
                ]),

            // 3. Total Tenants
            Stat::make('🏠 Total Tenants', number_format($totalTenants))
                ->description("Registered in the system")
                ->descriptionIcon('heroicon-m-users')
                ->color('success')
                ->chart([4, 8, 12, 16, 20, 24, $totalTenants])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20',
                ]),

            // 4. New This Month
            Stat::make('🆕 New This Month', number_format($newManagersThisMonth + $newTenantsThisMonth))
                ->description("{$newTenantsThisMonth} tenants, {$newManagersThisMonth} managers")
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('warning')
                ->chart([1, 2, 3, 4, 5, 6, $newManagersThisMonth + $newTenantsThisMonth])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20',
                ]),
        ];
    }

    /**
     * Update every 5 minutes
     */
    protected static ?string $pollingInterval = '5m';
}

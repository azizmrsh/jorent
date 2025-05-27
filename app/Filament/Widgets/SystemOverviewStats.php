<?php

namespace App\Filament\Widgets;

use App\Models\Property;
use App\Models\Unit;
use App\Models\Contract1;
use App\Models\Tenant;
use App\Models\Payment;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SystemOverviewStats extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        // Core system statistics
        $totalProperties = Property::count();
        $totalUnits = Unit::count();
        $totalContracts = Contract1::count();
        $totalTenants = Tenant::count();
        
        // Recent growth (last 30 days)
        $recentProperties = Property::where('created_at', '>=', now()->subDays(30))->count();
        $recentContracts = Contract1::where('created_at', '>=', now()->subDays(30))->count();
        $recentTenants = Tenant::where('created_at', '>=', now()->subDays(30))->count();
        
        // Active statistics
        $activeContracts = Contract1::where('status', 'active')->count();
        $occupiedUnits = Unit::where('status', 'rented')->count();
        $availableUnits = Unit::where('status', 'available')->count();

        return [
            // 1. Properties Overview
            Stat::make('📊 Total Properties', number_format($totalProperties))
                ->description($recentProperties > 0 ? "+{$recentProperties} this month" : 'No new properties')
                ->descriptionIcon($recentProperties > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-minus')
                ->color('primary')
                ->chart([5, 8, 12, 15, 18, 22, $totalProperties])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20',
                ]),

            // 2. Active Contracts
            Stat::make('📋 Active Contracts', number_format($activeContracts))
                ->description("Out of {$totalContracts} total contracts")
                ->descriptionIcon('heroicon-m-document-check')
                ->color('success')
                ->chart([3, 7, 10, 14, 16, 19, $activeContracts])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20',
                ]),

            // 3. Unit Occupancy
            Stat::make('🏠 Unit Occupancy', number_format($occupiedUnits) . '/' . number_format($totalUnits))
                ->description($availableUnits > 0 ? "{$availableUnits} units available" : 'All units occupied')
                ->descriptionIcon('heroicon-m-home')
                ->color($availableUnits > 0 ? 'warning' : 'success')
                ->chart([2, 4, 6, 8, 10, 12, $occupiedUnits])
                ->extraAttributes([
                    'class' => $availableUnits > 0 
                        ? 'bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20'
                        : 'bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20',
                ]),

            // 4. Tenant Growth
            Stat::make('👥 Total Tenants', number_format($totalTenants))
                ->description($recentTenants > 0 ? "+{$recentTenants} new tenants" : 'No new tenants')
                ->descriptionIcon($recentTenants > 0 ? 'heroicon-m-user-plus' : 'heroicon-m-users')
                ->color('info')
                ->chart([1, 3, 5, 7, 9, 11, $totalTenants])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-cyan-50 to-cyan-100 dark:from-cyan-900/20 dark:to-cyan-800/20',
                ]),
        ];
    }

    /**
     * Update every 2 minutes for real-time stats
     */
    protected static ?string $pollingInterval = '2m';
}

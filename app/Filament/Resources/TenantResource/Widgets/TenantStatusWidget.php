<?php

namespace App\Filament\Resources\TenantResource\Widgets;

use App\Models\Tenant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TenantStatusWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $activeTenants = Tenant::where('status', 'active')->count();
        $inactiveTenants = Tenant::where('status', 'unactive')->count();
        $totalTenants = Tenant::count();
        
        $activePercentage = $totalTenants > 0 ? round(($activeTenants / $totalTenants) * 100, 1) : 0;
        $inactivePercentage = $totalTenants > 0 ? round(($inactiveTenants / $totalTenants) * 100, 1) : 0;

        return [
            Stat::make('Active Tenants', number_format($activeTenants))
                ->description("$activePercentage% of total tenants")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20',
                ]),

            Stat::make('Inactive Tenants', number_format($inactiveTenants))
                ->description("$inactivePercentage% of total tenants")
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger')
                ->chart([3, 1, 4, 3, 2, 1, 3, 2])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20',
                ]),
        ];
    }
}

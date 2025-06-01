<?php

namespace App\Filament\Resources\TenantResource\Widgets;

use App\Models\Tenant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class RecentTenantsWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $todayTenants = Tenant::whereDate('created_at', today())->count();
        $weekTenants = Tenant::where('created_at', '>=', now()->subWeek())->count();
        $monthTenants = Tenant::where('created_at', '>=', now()->subMonth())->count();
        
        // Latest tenant
        $latestTenant = Tenant::latest('created_at')->first();
        $latestTenantName = $latestTenant ? $latestTenant->firstname . ' ' . $latestTenant->lastname : 'None';
        $latestTenantDate = $latestTenant ? $latestTenant->created_at->diffForHumans() : 'N/A';

        return [
            Stat::make('Today\'s Tenants', number_format($todayTenants))
                ->description("New tenants registered today")
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info')
                ->chart([1, 0, 2, 1, 3, 0, 1, 2])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-cyan-50 to-cyan-100 dark:from-cyan-900/20 dark:to-cyan-800/20',
                ]),

            Stat::make('This Week', number_format($weekTenants))
                ->description("Tenants added this week")
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary')
                ->chart([2, 3, 1, 4, 2, 5, 3])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20',
                ]),
        ];
    }
}

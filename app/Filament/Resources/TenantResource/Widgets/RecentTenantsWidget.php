<?php

namespace App\Filament\Resources\TenantResource\Widgets;

use App\Models\Tenant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class RecentTenantsWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 3;

    protected function getStats(): array
    {
        $monthTenants = Tenant::where('created_at', '>=', now()->subMonth())->count();

        return [
            Stat::make('New This Month', number_format($monthTenants))
                ->description("Tenants added this month")
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary')
                ->chart([2, 3, 1, 4, 2, 5, 3])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20',
                ]),
        ];
    }
}

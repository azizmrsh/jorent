<?php

namespace App\Filament\Resources\TenantResource\Widgets;

use App\Models\Tenant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalTenantsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 1;

    protected function getStats(): array
    {
        $totalTenants = Tenant::count();

        return [
            Stat::make('Total Tenants', number_format($totalTenants))
                ->description("Tenants registered in system")
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20',
                ]),
        ];
    }
}

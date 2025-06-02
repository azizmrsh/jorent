<?php

namespace App\Filament\Resources\TenantResource\Widgets;

use App\Models\Tenant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TenantStatusWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 1;

    protected function getStats(): array
    {
        $activeTenants = Tenant::where('status', 'active')->count();
        $totalTenants = Tenant::count();
        $activePercentage = $totalTenants > 0 ? round(($activeTenants / $totalTenants) * 100, 1) : 0;

        return [
            Stat::make('Active Tenants', number_format($activeTenants))
                ->description("$activePercentage% of total tenants")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20',
                ]),
        ];
    }
}

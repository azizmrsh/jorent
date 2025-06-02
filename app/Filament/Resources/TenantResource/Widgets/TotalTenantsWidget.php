<?php

namespace App\Filament\Resources\TenantResource\Widgets;

use App\Models\Tenant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalTenantsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('status', 'active')->count();
        $verifiedDocuments = Tenant::whereNotNull('document_type')
                                  ->whereNotNull('document_number')
                                  ->count();
        $monthTenants = Tenant::where('created_at', '>=', now()->subMonth())->count();

        $activePercentage = $totalTenants > 0 ? round(($activeTenants / $totalTenants) * 100, 1) : 0;
        $verifiedPercentage = $totalTenants > 0 ? round(($verifiedDocuments / $totalTenants) * 100, 1) : 0;

        return [
            Stat::make('Total Tenants', number_format($totalTenants))
                ->description("Tenants registered in system")
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20',
                ]),

            Stat::make('Active Tenants', number_format($activeTenants))
                ->description("$activePercentage% of total tenants")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20',
                ]),

            Stat::make('Verified Documents', number_format($verifiedDocuments))
                ->description("$verifiedPercentage% of all tenants")
                ->descriptionIcon('heroicon-m-document-check')
                ->color('info')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-cyan-50 to-cyan-100 dark:from-cyan-900/20 dark:to-cyan-800/20',
                ]),

            Stat::make('New This Month', number_format($monthTenants))
                ->description("Tenants added this month")
                ->descriptionIcon('heroicon-m-calendar')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20',
                ]),
        ];
    }
}

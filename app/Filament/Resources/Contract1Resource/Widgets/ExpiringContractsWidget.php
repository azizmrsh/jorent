<?php

namespace App\Filament\Resources\Contract1Resource\Widgets;

use App\Models\Contract1;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ExpiringContractsWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        // Contracts expiring in the next 30 days
        $expiringSoon = Contract1::where('end_date', '>=', now())
            ->where('end_date', '<=', now()->addDays(30))
            ->count();
            
        // Contracts expiring in the next 7 days
        $expiringThisWeek = Contract1::where('end_date', '>=', now())
            ->where('end_date', '<=', now()->addDays(7))
            ->count();
            
        // Contracts that already expired
        $alreadyExpired = Contract1::where('end_date', '<', now())
            ->where('status', '!=', 'inactive')
            ->count();
            
        // Contracts renewed this month
        $renewedThisMonth = Contract1::where('created_at', '>=', now()->startOfMonth())
            ->whereExists(function ($query) {
                $query->select('id')
                    ->from('contract1s as c2')
                    ->whereColumn('c2.property_id', 'contract1s.property_id')
                    ->whereColumn('c2.unit_id', 'contract1s.unit_id')
                    ->where('c2.created_at', '<', 'contract1s.created_at');
            })
            ->count();

        return [
            Stat::make('Expiring Soon', number_format($expiringSoon))
                ->description("Contracts ending within 30 days")
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('warning')
                ->chart([2, 3, 5, 4, 6, 3, 4, 5])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20',
                ]),

            Stat::make('Expiring This Week', number_format($expiringThisWeek))
                ->description("Urgent: contracts ending within 7 days")
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger')
                ->chart([1, 2, 1, 3, 2, 1, 2, 3])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20',
                ]),

            Stat::make('Already Expired', number_format($alreadyExpired))
                ->description("Contracts that need attention")
                ->descriptionIcon('heroicon-m-clock')
                ->color('gray')
                ->chart([4, 3, 2, 1, 2, 3, 2, 1])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900/20 dark:to-gray-800/20',
                ]),

            Stat::make('Renewed This Month', number_format($renewedThisMonth))
                ->description("Contract renewals this month")
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('success')
                ->chart([1, 2, 3, 2, 4, 3, 5, 4])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20',
                ]),
        ];
    }
}

<?php

namespace App\Filament\Resources\Contract1Resource\Widgets;

use App\Models\Contract1;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalContractsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $totalContracts = Contract1::count();
        $activeContracts = Contract1::where('status', 'active')->count();
        $totalRevenue = Contract1::where('status', 'active')->sum('rent_amount');
        $expiringSoon = Contract1::where('end_date', '>=', now())
            ->where('end_date', '<=', now()->addDays(30))
            ->count();

        return [
            Stat::make('Total Contracts', number_format($totalContracts))
                ->description("Contracts registered in system")
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20',
                ]),

            Stat::make('Active Contracts', number_format($activeContracts))
                ->description("Currently active contracts")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20',
                ]),

            Stat::make('Total Revenue', 'JOD ' . number_format($totalRevenue, 2))
                ->description("From all active contracts")
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('info')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-cyan-50 to-cyan-100 dark:from-cyan-900/20 dark:to-cyan-800/20',
                ]),

            Stat::make('Expiring Soon', number_format($expiringSoon))
                ->description("Contracts ending within 30 days")
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20',
                ]),
        ];
    }
}

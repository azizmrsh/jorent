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
        $recentContracts = Contract1::where('created_at', '>=', now()->subDays(30))->count();
        $thisMonthGrowth = $recentContracts > 0 ? round(($recentContracts / max($totalContracts - $recentContracts, 1)) * 100, 1) : 0;

        return [
            //
            Stat::make('Total Contracts', number_format($totalContracts))
                ->description("Contracts registered in system")
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20',
                ]),

            Stat::make('New Contracts', number_format($recentContracts))
                ->description("Within the last 30 days")
                ->descriptionIcon('heroicon-m-plus-circle')
                ->color('success')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20',
                ]),

            Stat::make('Monthly Growth Rate', $thisMonthGrowth . '%')
                ->description($thisMonthGrowth > 0 ? "Increase in contracts" : "No growth")
                ->descriptionIcon($thisMonthGrowth > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-minus')
                ->color($thisMonthGrowth > 0 ? 'success' : 'gray')
                ->extraAttributes([
                    'class' => $thisMonthGrowth > 0 
                        ? 'bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20'
                        : 'bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900/20 dark:to-gray-800/20',
                ]),
        ];
    }
}

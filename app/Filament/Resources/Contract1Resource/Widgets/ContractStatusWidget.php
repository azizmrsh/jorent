<?php

namespace App\Filament\Resources\Contract1Resource\Widgets;

use App\Models\Contract1;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContractStatusWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $activeContracts = Contract1::where('status', 'active')->count();
        $inactiveContracts = Contract1::where('status', 'inactive')->count();
        $totalContracts = Contract1::count();
        
        $activePercentage = $totalContracts > 0 ? round(($activeContracts / $totalContracts) * 100, 1) : 0;
        $inactivePercentage = $totalContracts > 0 ? round(($inactiveContracts / $totalContracts) * 100, 1) : 0;

        return [
            Stat::make('Active Contracts', number_format($activeContracts))
                ->description("$activePercentage% of total contracts")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20',
                ]),

            Stat::make('Inactive Contracts', number_format($inactiveContracts))
                ->description("$inactivePercentage% of total contracts")
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger')
                ->chart([3, 1, 4, 3, 2, 1, 3, 2])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20',
                ]),
        ];
    }
}

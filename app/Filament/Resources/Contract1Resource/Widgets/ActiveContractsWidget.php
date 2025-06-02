<?php

namespace App\Filament\Resources\Contract1Resource\Widgets;

use App\Models\Contract1;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ActiveContractsWidget extends BaseWidget
{
    protected static ?int $sort = 5;

    protected function getStats(): array
    {
        $activeContracts = Contract1::where('status', 'active')->count();
        $totalContracts = Contract1::count();
        $activePercentage = $totalContracts > 0 ? round(($activeContracts / $totalContracts) * 100, 1) : 0;

        return [
            Stat::make('Active Contracts', number_format($activeContracts))
                ->description($activePercentage . '% of total contracts')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([4, 6, 8, 5, 7, 9, 6, 8])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20',
                ]),
        ];
    }
}

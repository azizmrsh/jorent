<?php

namespace App\Filament\Resources\Contract1Resource\Widgets;

use App\Models\Contract1;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InactiveContractsWidget extends BaseWidget
{
    protected static ?int $sort = 6;

    protected function getStats(): array
    {
        $inactiveContracts = Contract1::where('status', 'inactive')->count();
        $totalContracts = Contract1::count();
        $inactivePercentage = $totalContracts > 0 ? round(($inactiveContracts / $totalContracts) * 100, 1) : 0;

        return [
            Stat::make('Inactive Contracts', number_format($inactiveContracts))
                ->description($inactivePercentage . '% are inactive')
                ->descriptionIcon('heroicon-m-pause-circle')
                ->color('warning')
                ->chart([2, 4, 3, 6, 4, 5, 3, 4])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20',
                ]),
        ];
    }
}

<?php

namespace App\Filament\Resources\Contract1Resource\Widgets;

use App\Models\Contract1;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class NewContractsWidget extends BaseWidget
{
    protected static ?int $sort = 8;

    protected function getStats(): array
    {
        $todayContracts = Contract1::whereDate('created_at', today())->count();
        $weekContracts = Contract1::where('created_at', '>=', now()->subWeek())->count();
        $weeklyGrowth = $weekContracts > $todayContracts ? 
            round((($weekContracts - $todayContracts) / max($todayContracts, 1)) * 100, 1) : 0;

        return [
            Stat::make('New Today', number_format($todayContracts))
                ->description($weekContracts . ' this week')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('info')
                ->chart([1, 3, 2, 4, 3, 5, 4, 6])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/20',
                ]),
        ];
    }
}

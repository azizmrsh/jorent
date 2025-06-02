<?php

namespace App\Filament\Resources\Contract1Resource\Widgets;

use App\Models\Contract1;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalContractsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 1;

    protected function getStats(): array
    {
        $totalContracts = Contract1::count();

        return [
            Stat::make('Total Contracts', number_format($totalContracts))
                ->description("Contracts registered in system")
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20',
                ]),
        ];
    }
}

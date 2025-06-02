<?php

namespace App\Filament\Resources\Contract1Resource\Widgets;

use App\Models\Contract1;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ExpiringContractsWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 1;

    protected function getStats(): array
    {
        $expiringSoon = Contract1::where('end_date', '>=', now())
            ->where('end_date', '<=', now()->addDays(30))
            ->count();

        return [
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

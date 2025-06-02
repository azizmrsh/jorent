<?php

namespace App\Filament\Resources\Contract1Resource\Widgets;

use App\Models\Contract1;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class RevenueStatsWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 1;    protected function getStats(): array
    {
        $totalRevenue = Contract1::where('status', 'active')->sum('rent_amount');

        return [
            Stat::make('Total Revenue', 'JOD ' . number_format($totalRevenue, 2))
                ->description("From all active contracts")
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20',
                ]),
        ];
    }
}

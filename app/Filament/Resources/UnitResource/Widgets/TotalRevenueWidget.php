<?php

namespace App\Filament\Resources\UnitResource\Widgets;

use App\Models\Unit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalRevenueWidget extends BaseWidget
{
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 3;

    protected function getStats(): array
    {
        $totalRevenue = Unit::sum('rental_price');
        $totalUnits = Unit::count();

        return [
            Stat::make('Total Revenue Potential', number_format($totalRevenue, 2) . ' JOD')
                ->description("From {$totalUnits} units combined")
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20',
                ]),
        ];
    }
}

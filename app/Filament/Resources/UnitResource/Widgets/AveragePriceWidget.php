<?php

namespace App\Filament\Resources\UnitResource\Widgets;

use App\Models\Unit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AveragePriceWidget extends BaseWidget
{
    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 3;

    protected function getStats(): array
    {
        $totalUnits = Unit::count();
        $totalRevenue = Unit::sum('rental_price');
        $averagePrice = $totalUnits > 0 ? round($totalRevenue / $totalUnits, 2) : 0;

        return [
            Stat::make('Average Rental Price', number_format($averagePrice, 2) . ' JOD')
                ->description("Per unit average")
                ->descriptionIcon('heroicon-m-calculator')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20',
                ]),
        ];
    }
}

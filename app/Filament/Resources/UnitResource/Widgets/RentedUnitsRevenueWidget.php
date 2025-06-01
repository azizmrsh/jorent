<?php

namespace App\Filament\Resources\UnitResource\Widgets;

use App\Models\Unit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RentedUnitsRevenueWidget extends BaseWidget
{
    protected static ?int $sort = 8;
    protected int | string | array $columnSpan = 3;

    protected function getStats(): array
    {
        $rentedUnits = Unit::where('status', 'rented');
        $rentedCount = $rentedUnits->count();
        $rentedRevenue = $rentedUnits->sum('rental_price');

        return [
            Stat::make('Rented Units Revenue', number_format($rentedRevenue, 2) . ' JOD')
                ->description("From {$rentedCount} rented units")
                ->descriptionIcon('heroicon-m-key')
                ->color('purple')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20',
                ]),
        ];
    }
}

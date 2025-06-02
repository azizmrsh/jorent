<?php

namespace App\Filament\Resources\UnitResource\Widgets;

use App\Models\Unit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PriceRangesWidget extends BaseWidget
{
    protected static ?int $sort = 7;
    protected int | string | array $columnSpan = 3;

    protected function getStats(): array
    {
        $highestPrice = Unit::max('rental_price') ?? 0;
        $lowestPrice = Unit::min('rental_price') ?? 0;
        $priceRange = $highestPrice - $lowestPrice;

        return [
            Stat::make('Price Range', number_format($priceRange, 2) . ' JOD')
                ->description("High: " . number_format($highestPrice, 2) . " • Low: " . number_format($lowestPrice, 2))
                ->descriptionIcon('heroicon-m-arrows-up-down')
                ->color('info')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-cyan-50 to-cyan-100 dark:from-cyan-900/20 dark:to-cyan-800/20',
                ]),
        ];
    }
}

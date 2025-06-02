<?php

namespace App\Filament\Resources\UnitResource\Widgets;

use App\Models\Unit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AvailableUnitsWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 1;

    protected function getStats(): array
    {
        $totalUnits = Unit::count();
        $availableCount = Unit::where('status', 'available')->count();
        $availablePercentage = $totalUnits > 0 ? round(($availableCount / $totalUnits) * 100, 1) : 0;

        return [
            Stat::make('Available Units', number_format($availableCount))
                ->description("{$availablePercentage}% of total units")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20',
                ]),
        ];
    }
}

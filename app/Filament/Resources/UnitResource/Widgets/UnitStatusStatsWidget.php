<?php

namespace App\Filament\Resources\UnitResource\Widgets;

use App\Models\Unit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UnitStatusStatsWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 3;

    protected function getStats(): array
    {
        $totalUnits = Unit::count();
        $rentedCount = Unit::where('status', 'rented')->count();
        $occupancyRate = $totalUnits > 0 ? round(($rentedCount / $totalUnits) * 100, 1) : 0;

        return [
            Stat::make('Occupancy Rate', $occupancyRate . '%')
                ->description("{$rentedCount} of {$totalUnits} units rented")
                ->descriptionIcon('heroicon-m-chart-pie')
                ->color('success')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20',
                ]),
        ];
    }
}

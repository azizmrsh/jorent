<?php

namespace App\Filament\Resources\PropertyResource\Widgets;

use App\Models\Property;
use App\Models\Unit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UnitsCounterWidget extends BaseWidget
{
    protected static ?int $sort = 4;protected function getStats(): array
    {
        $totalUnits = Unit::count();

        return [
            Stat::make('Total Units', number_format($totalUnits))
                ->description("Units in all properties")
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/20',
                ]),
        ];
    }
}

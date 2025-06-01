<?php

namespace App\Filament\Resources\PropertyResource\Widgets;

use App\Models\Property;
use App\Models\Unit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UnitsCounterWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $totalUnits = Unit::count();
        $totalProperties = Property::count();
        $propertiesWithUnits = Property::has('units')->count();
        
        // Calculate average units per property
        $averageUnitsPerProperty = $totalProperties > 0 ? round($totalUnits / $totalProperties, 1) : 0;

        return [
            Stat::make('Total Units', number_format($totalUnits))
                ->description("Avg: {$averageUnitsPerProperty} per property")
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20',
                ]),
        ];
    }
}

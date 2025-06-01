<?php

namespace App\Filament\Resources\PropertyResource\Widgets;

use App\Models\Property;
use App\Models\Unit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AveragePropertyValueWidget extends BaseWidget
{
    protected static ?int $sort = 7;
    protected int | string | array $columnSpan = 3;

    protected function getStats(): array
    {
        // Calculate average property value based on units
        $propertiesWithUnits = Property::has('units')->count();
        
        if ($propertiesWithUnits == 0) {
            $averageValue = 0;
            $highestValue = 0;
            $lowestValue = 0;
        } else {
            // Get property values by summing their units
            $propertyValues = Property::has('units')
                ->with('units')
                ->get()
                ->map(function ($property) {
                    return $property->units->sum('price');
                });
            
            $averageValue = $propertyValues->avg();
            $highestValue = $propertyValues->max();
            $lowestValue = $propertyValues->min();
        }

        return [
            Stat::make('Avg Property Value', '$' . number_format($averageValue, 0))
                ->description("Range: $" . number_format($lowestValue, 0) . " - $" . number_format($highestValue, 0))
                ->descriptionIcon('heroicon-m-calculator')
                ->color('info')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20',
                ]),
        ];
    }
}

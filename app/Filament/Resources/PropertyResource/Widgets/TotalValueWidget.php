<?php

namespace App\Filament\Resources\PropertyResource\Widgets;

use App\Models\Property;
use App\Models\Unit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalValueWidget extends BaseWidget
{
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 3;

    protected function getStats(): array
    {
        // Calculate total property values (based on associated units)
        $totalValue = Unit::whereHas('property')->sum('price');
        
        // Calculate average property value
        $totalProperties = Property::has('units')->count();
        $averageValue = $totalProperties > 0 ? $totalValue / $totalProperties : 0;
        
        // Properties with high value (above average)
        $highValueProperties = Property::whereHas('units', function ($query) use ($averageValue) {
            $query->havingRaw('AVG(price) > ?', [$averageValue]);
        })->count();

        return [
            Stat::make('Total Portfolio Value', '$' . number_format($totalValue, 0))
                ->description("Avg: $" . number_format($averageValue, 0) . " per property")
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20',
                ]),
        ];
    }
}

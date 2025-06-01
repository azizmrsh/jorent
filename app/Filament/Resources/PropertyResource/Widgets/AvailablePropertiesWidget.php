<?php

namespace App\Filament\Resources\PropertyResource\Widgets;

use App\Models\Property;
use App\Models\Unit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AvailablePropertiesWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 3;

    protected function getStats(): array
    {
        $totalProperties = Property::count();
        
        // Properties with available units
        $propertiesWithAvailableUnits = Property::whereHas('units', function ($query) {
            $query->where('status', 'available');
        })->count();
        
        // Properties that are fully occupied (all units rented)
        $fullyOccupiedProperties = Property::whereHas('units')
            ->whereDoesntHave('units', function ($query) {
                $query->where('status', 'available');
            })->count();
        
        // Calculate availability percentage
        $availabilityPercentage = $totalProperties > 0 ? round(($propertiesWithAvailableUnits / $totalProperties) * 100, 1) : 0;

        return [
            Stat::make('Available Properties', number_format($propertiesWithAvailableUnits))
                ->description("{$availabilityPercentage}% of total properties")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20',
                ]),
        ];
    }
}

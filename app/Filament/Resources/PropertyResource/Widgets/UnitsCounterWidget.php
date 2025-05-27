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
        
        // حساب متوسط الوحدات لكل عقار
        $averageUnitsPerProperty = $totalProperties > 0 ? round($totalUnits / $totalProperties, 1) : 0;
        
        // حساب نسبة العقارات التي تحتوي على وحدات
        $propertiesWithUnitsPercentage = $totalProperties > 0 ? round(($propertiesWithUnits / $totalProperties) * 100, 1) : 0;

        return [
            Stat::make('إجمالي الوحدات', number_format($totalUnits))
                ->description("الوحدات داخل جميع العقارات")
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/20',
                ]),

            Stat::make('متوسط الوحدات', $averageUnitsPerProperty)
                ->description("وحدة لكل عقار")
                ->descriptionIcon('heroicon-m-calculator')
                ->color('info')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-sky-50 to-sky-100 dark:from-sky-900/20 dark:to-sky-800/20',
                ]),

            Stat::make('العقارات بوحدات', number_format($propertiesWithUnits))
                ->description("$propertiesWithUnitsPercentage% من العقارات تحتوي على وحدات")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-teal-50 to-teal-100 dark:from-teal-900/20 dark:to-teal-800/20',
                ]),
        ];
    }
}

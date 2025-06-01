<?php

namespace App\Filament\Resources\PropertyResource\Widgets;

use App\Models\Property;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UsageTypeStatsWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 3;

    protected function getStats(): array
    {
        $totalProperties = Property::count();
        
        // Calculate count for each usage type
        $residentialCount = Property::where('type2', 'residential')->count();
        $commercialCount = Property::where('type2', 'commercial')->count();
        $industrialCount = Property::where('type2', 'industrial')->count();
        
        // Find dominant usage type
        $usageTypes = [
            'Residential' => $residentialCount,
            'Commercial' => $commercialCount,
            'Industrial' => $industrialCount
        ];
        
        $dominantUsage = array_keys($usageTypes, max($usageTypes))[0] ?? 'Mixed';
        $dominantCount = max($usageTypes);
        $percentage = $totalProperties > 0 ? round(($dominantCount / $totalProperties) * 100, 1) : 0;

        return [
            Stat::make('Primary Usage', $dominantUsage)
                ->description("{$dominantCount} properties ({$percentage}%)")
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('success')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20',
                ]),
        ];
    }
}

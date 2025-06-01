<?php

namespace App\Filament\Resources\PropertyResource\Widgets;

use App\Models\Property;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UsageTypeStatsWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 1;

    protected function getStats(): array
    {
        $totalProperties = Property::count();
        
        // Calculate count for each usage type
        $residentialCount = Property::where('type2', 'residential')->count();
        $commercialCount = Property::where('type2', 'commercial')->count();
        $industrialCount = Property::where('type2', 'industrial')->count();
        
        // Combined count
        $combinedCount = $residentialCount + $commercialCount + $industrialCount;
        
        // Create description with breakdown
        $breakdown = [];
        if ($residentialCount > 0) $breakdown[] = "Residential: {$residentialCount}";
        if ($commercialCount > 0) $breakdown[] = "Commercial: {$commercialCount}";
        if ($industrialCount > 0) $breakdown[] = "Industrial: {$industrialCount}";
        
        $description = !empty($breakdown) ? implode(' • ', $breakdown) : "No properties by usage";

        return [
            Stat::make('Usage Types', number_format($combinedCount))
                ->description($description)
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('success')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20',
                ]),
        ];
    }
}

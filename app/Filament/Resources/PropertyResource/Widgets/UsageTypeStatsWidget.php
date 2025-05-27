<?php

namespace App\Filament\Resources\PropertyResource\Widgets;

use App\Models\Property;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UsageTypeStatsWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';    protected function getStats(): array
    {
        $totalProperties = Property::count();
        
        // حساب عدد كل نوع استخدام
        $residentialCount = Property::where('type2', 'residential')->count();
        $commercialCount = Property::where('type2', 'commercial')->count();
        $industrialCount = Property::where('type2', 'industrial')->count();

        // حساب النسب المئوية
        $residentialPercentage = $totalProperties > 0 ? round(($residentialCount / $totalProperties) * 100, 1) : 0;
        $commercialPercentage = $totalProperties > 0 ? round(($commercialCount / $totalProperties) * 100, 1) : 0;
        $industrialPercentage = $totalProperties > 0 ? round(($industrialCount / $totalProperties) * 100, 1) : 0;

        return [
            Stat::make('سكني', number_format($residentialCount))
                ->description("$residentialPercentage% من إجمالي العقارات")
                ->descriptionIcon('heroicon-m-home')
                ->color('success')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20',
                ]),

            Stat::make('تجاري', number_format($commercialCount))
                ->description("$commercialPercentage% من إجمالي العقارات")
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20',
                ]),

            Stat::make('صناعي', number_format($industrialCount))
                ->description("$industrialPercentage% من إجمالي العقارات")
                ->descriptionIcon('heroicon-m-cog-6-tooth')
                ->color('info')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-cyan-50 to-cyan-100 dark:from-cyan-900/20 dark:to-cyan-800/20',
                ]),
        ];
    }
}

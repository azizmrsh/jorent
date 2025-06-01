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

        // إنشاء نص تفصيلي لأنواع الاستخدام
        $detailsText = "🏠 سكني: " . number_format($residentialCount) . " ({$residentialPercentage}%) | " .
                       "🏪 تجاري: " . number_format($commercialCount) . " ({$commercialPercentage}%) | " .
                       "🏭 صناعي: " . number_format($industrialCount) . " ({$industrialPercentage}%)";        return [
            Stat::make('أنواع الاستخدام', number_format($totalProperties))
                ->description($detailsText)
                ->descriptionIcon('heroicon-m-rectangle-group')
                ->color('success')
                ->chart([$residentialCount, $commercialCount, $industrialCount])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20',
                ]),
        ];
    }
}

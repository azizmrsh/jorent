<?php

namespace App\Filament\Resources\PropertyResource\Widgets;

use App\Models\Property;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PropertyTypeStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';    protected function getStats(): array
    {
        $totalProperties = Property::count();
        
        // حساب عدد كل نوع من العقارات
        $buildingCount = Property::where('type1', 'building')->count();
        $villaCount = Property::where('type1', 'villa')->count();
        $houseCount = Property::where('type1', 'house')->count();
        $warehouseCount = Property::where('type1', 'warehouse')->count();

        // حساب النسب المئوية
        $buildingPercentage = $totalProperties > 0 ? round(($buildingCount / $totalProperties) * 100, 1) : 0;
        $villaPercentage = $totalProperties > 0 ? round(($villaCount / $totalProperties) * 100, 1) : 0;
        $housePercentage = $totalProperties > 0 ? round(($houseCount / $totalProperties) * 100, 1) : 0;
        $warehousePercentage = $totalProperties > 0 ? round(($warehouseCount / $totalProperties) * 100, 1) : 0;

        // إنشاء نص تفصيلي للأنواع
        $detailsText = "🏢 مباني: " . number_format($buildingCount) . " ({$buildingPercentage}%) | " .
                       "🏡 فيلات: " . number_format($villaCount) . " ({$villaPercentage}%) | " .
                       "🏠 منازل: " . number_format($houseCount) . " ({$housePercentage}%) | " .
                       "📦 مستودعات: " . number_format($warehouseCount) . " ({$warehousePercentage}%)";

        return [
            Stat::make('أنواع العقارات', number_format($totalProperties))
                ->description($detailsText)
                ->descriptionIcon('heroicon-m-squares-plus')
                ->color('primary')
                ->chart([$buildingCount, $villaCount, $houseCount, $warehouseCount])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/20',
                ]),
        ];
    }
}

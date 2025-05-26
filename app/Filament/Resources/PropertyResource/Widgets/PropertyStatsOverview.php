<?php

namespace App\Filament\Resources\PropertyResource\Widgets;

use App\Models\Property;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class PropertyStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // احصائيات أساسية
        $totalProperties = Property::count();
        $buildingsCount = Property::where('type1', 'building')->count();
        $villasCount = Property::where('type1', 'villa')->count();
        $warehousesCount = Property::where('type1', 'warehouse')->count();
        
        // احصائيات حسب الاستخدام
        $residentialCount = Property::where('type2', 'residential')->count();
        $commercialCount = Property::where('type2', 'commercial')->count();
        $industrialCount = Property::where('type2', 'industrial')->count();

        // العقارات الجديدة هذا الشهر
        $newThisMonth = Property::whereMonth('created_at', Carbon::now()->month)
                               ->whereYear('created_at', Carbon::now()->year)
                               ->count();

        // حساب النسب المئوية
        $buildingsPercentage = $totalProperties > 0 ? round(($buildingsCount / $totalProperties) * 100, 1) : 0;
        $residentialPercentage = $totalProperties > 0 ? round(($residentialCount / $totalProperties) * 100, 1) : 0;

        // مقارنة مع الشهر الماضي
        $lastMonthProperties = Property::whereMonth('created_at', Carbon::now()->subMonth()->month)
                                     ->whereYear('created_at', Carbon::now()->subMonth()->year)
                                     ->count();
        
        $newPropertiesChange = $this->calculatePercentageChange($newThisMonth, $lastMonthProperties);

        // إجمالي المساحة
        $totalArea = Property::sum('total_area');
        $averageArea = $totalProperties > 0 ? round($totalArea / $totalProperties, 2) : 0;

        return [
            // 1. إجمالي العقارات
            Stat::make('Total Properties', number_format($totalProperties))
                ->description('العدد الكلي للعقارات المسجلة')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary')
                ->chart([12, 18, 15, 22, 19, 25, $totalProperties])
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900/20',
                ]),

            // 2. المباني (الأكثر شيوعاً)
            Stat::make('Buildings', number_format($buildingsCount))
                ->description("{$buildingsPercentage}% من إجمالي العقارات")
                ->descriptionIcon('heroicon-m-building-office')
                ->color('success')
                ->chart([8, 12, 10, 15, 14, 18, $buildingsCount])
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:bg-green-50 dark:hover:bg-green-900/20',
                ]),

            // 3. العقارات السكنية
            Stat::make('Residential Properties', number_format($residentialCount))
                ->description("{$residentialPercentage}% من إجمالي العقارات")
                ->descriptionIcon('heroicon-m-home')
                ->color('info')
                ->chart([6, 9, 8, 12, 11, 14, $residentialCount])
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900/20',
                ]),

            // 4. العقارات الجديدة هذا الشهر
            Stat::make('New This Month', number_format($newThisMonth))
                ->description($newPropertiesChange['description'])
                ->descriptionIcon($newPropertiesChange['icon'])
                ->descriptionColor($newPropertiesChange['color'])
                ->color('warning')
                ->chart([1, 2, 3, 1, 4, 2, $newThisMonth])
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:bg-orange-50 dark:hover:bg-orange-900/20',
                ]),
        ];
    }

    /**
     * حساب نسبة التغيير مع الوصف والأيقونة المناسبة
     */
    private function calculatePercentageChange(int $current, int $previous): array
    {
        if ($previous == 0) {
            if ($current > 0) {
                return [
                    'description' => "جديد! {$current} عقارات مضافة",
                    'icon' => 'heroicon-m-arrow-trending-up',
                    'color' => 'success'
                ];
            }
            return [
                'description' => 'لا توجد إضافات جديدة',
                'icon' => 'heroicon-m-minus-circle',
                'color' => 'gray'
            ];
        }

        $percentageChange = round((($current - $previous) / $previous) * 100, 1);
        
        if ($percentageChange > 0) {
            return [
                'description' => "+{$percentageChange}% عن الشهر الماضي",
                'icon' => 'heroicon-m-arrow-trending-up',
                'color' => 'success'
            ];
        } elseif ($percentageChange < 0) {
            return [
                'description' => "{$percentageChange}% عن الشهر الماضي",
                'icon' => 'heroicon-m-arrow-trending-down',
                'color' => 'danger'
            ];
        } else {
            return [
                'description' => 'نفس عدد الشهر الماضي',
                'icon' => 'heroicon-m-minus-circle',
                'color' => 'gray'
            ];
        }
    }

    /**
     * تحديث الويدجت كل 30 ثانية
     */
    protected static ?string $pollingInterval = '30s';

    /**
     * تخصيص ارتفاع الويدجت
     */
    protected static ?string $maxHeight = '200px';
}

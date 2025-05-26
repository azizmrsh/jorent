<?php

namespace App\Filament\Resources\PropertyResource\Widgets;

use App\Models\Property;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class PropertyOverviewStats extends BaseWidget
{    protected function getStats(): array
    {
        // إحصائيات Usage Type (الاستخدام)
        $residentialCount = Property::where('type2', 'residential')->count();
        $commercialCount = Property::where('type2', 'commercial')->count();
        $industrialCount = Property::where('type2', 'industrial')->count();
        
        // إجمالي العقارات
        $totalProperties = Property::count();
        
        // احصائيات إضافية
        $averageArea = Property::where('total_area', '>', 0)->avg('total_area');
        $totalArea = Property::sum('total_area');
        
        // العقارات الجديدة هذا الشهر
        $newThisMonth = Property::whereMonth('created_at', Carbon::now()->month)
                               ->whereYear('created_at', Carbon::now()->year)
                               ->count();
        
        // حساب النسب المئوية لنوع الاستخدام
        $residentialPercentage = $totalProperties > 0 ? round(($residentialCount / $totalProperties) * 100, 1) : 0;
        $commercialPercentage = $totalProperties > 0 ? round(($commercialCount / $totalProperties) * 100, 1) : 0;
        $industrialPercentage = $totalProperties > 0 ? round(($industrialCount / $totalProperties) * 100, 1) : 0;        return [
            // 1. العقارات السكنية - أزرق
            Stat::make('🏠 Residential Properties', number_format($residentialCount))
                ->description("{$residentialPercentage}% من إجمالي العقارات")
                ->descriptionIcon('heroicon-m-home')
                ->color('primary')
                ->chart([8, 12, 15, 18, 14, 22, $residentialCount])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 border-l-4 border-blue-500',
                ]),

            // 2. العقارات التجارية - أخضر
            Stat::make('🏢 Commercial Properties', number_format($commercialCount))
                ->description("{$commercialPercentage}% من إجمالي العقارات")
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('success')
                ->chart([5, 8, 12, 10, 15, 18, $commercialCount])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 border-l-4 border-green-500',
                ]),

            // 3. العقارات الصناعية - برتقالي
            Stat::make('🏭 Industrial Properties', number_format($industrialCount))
                ->description("{$industrialPercentage}% من إجمالي العقارات")
                ->descriptionIcon('heroicon-m-building-office')
                ->color('warning')
                ->chart([2, 4, 6, 5, 8, 10, $industrialCount])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 border-l-4 border-orange-500',
                ]),

            // 4. إجمالي العقارات - أزرق داكن
            Stat::make('🏘️ Total Properties', number_format($totalProperties))
                ->description('إجمالي العقارات المسجلة')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('info')
                ->chart([15, 20, 25, 23, 30, 35, $totalProperties])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-blue-900/20 dark:to-indigo-800/20 border-l-4 border-indigo-500',
                ]),

            // 5. إجمالي المساحة - سماوي
            Stat::make('📐 Total Area', number_format($totalArea) . ' m²')
                ->description('إجمالي مساحة العقارات')
                ->descriptionIcon('heroicon-m-square-3-stack-3d')
                ->color('sky')
                ->chart([100, 150, 200, 180, 250, 300, $totalArea / 1000])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-sky-50 to-sky-100 dark:from-sky-900/20 dark:to-sky-800/20 border-l-4 border-sky-500',
                ]),

            // 6. العقارات الجديدة - أخضر زمردي
            Stat::make('✨ New This Month', number_format($newThisMonth))
                ->description('عقارات مضافة هذا الشهر')
                ->descriptionIcon('heroicon-m-plus-circle')
                ->color('emerald')
                ->chart([1, 2, 3, 2, 4, 5, $newThisMonth])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20 border-l-4 border-emerald-500',
                ]),
        ];
    }

    /**
     * تحديث الويدجت كل 30 ثانية
     */
    protected static ?string $pollingInterval = '30s';

    /**
     * عدد الأعمدة في الشبكة
     */
    protected int | string | array $columnSpan = 'full';

    /**
     * ترتيب الويدجت
     */
    protected static ?int $sort = 2;    /**
     * تخصيص الارتفاع
     */
    protected static ?string $maxHeight = '300px';

    /**
     * عنوان الويدجت
     */
    protected ?string $heading = 'Property Usage Statistics';
}

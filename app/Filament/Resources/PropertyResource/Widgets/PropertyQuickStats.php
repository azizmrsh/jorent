<?php

namespace App\Filament\Resources\PropertyResource\Widgets;

use App\Models\Property;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class PropertyQuickStats extends BaseWidget
{
    protected function getStats(): array
    {
        // احصائيات إضافية
        $totalArea = Property::sum('total_area');
        $averageArea = Property::where('total_area', '>', 0)->avg('total_area');
        
        // العقارات الجديدة هذا الشهر
        $newThisMonth = Property::whereMonth('created_at', Carbon::now()->month)
                               ->whereYear('created_at', Carbon::now()->year)
                               ->count();
        
        // العقارات الجديدة هذا الأسبوع
        $newThisWeek = Property::whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->count();

        return [
            // 1. إجمالي المساحة - سماوي
            Stat::make('📐 Total Area', number_format($totalArea) . ' m²')
                ->description('إجمالي مساحة العقارات')
                ->descriptionIcon('heroicon-m-square-3-stack-3d')
                ->color('sky')
                ->chart([100, 150, 200, 180, 250, 300, $totalArea / 1000])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-sky-50 to-sky-100 dark:from-sky-900/20 dark:to-sky-800/20 border-l-4 border-sky-500',
                ]),

            // 2. متوسط المساحة - أخضر فاتح
            Stat::make('📏 Avg Area', number_format($averageArea, 1) . ' m²')
                ->description('متوسط مساحة العقار')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('teal')
                ->chart([80, 120, 100, 140, 110, 130, $averageArea / 10])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-teal-50 to-teal-100 dark:from-teal-900/20 dark:to-teal-800/20 border-l-4 border-teal-500',
                ]),

            // 3. العقارات الجديدة هذا الشهر - أخضر زمردي
            Stat::make('✨ New This Month', number_format($newThisMonth))
                ->description('عقارات مضافة هذا الشهر')
                ->descriptionIcon('heroicon-m-plus-circle')
                ->color('emerald')
                ->chart([1, 2, 3, 2, 4, 5, $newThisMonth])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20 border-l-4 border-emerald-500',
                ]),

            // 4. العقارات الجديدة هذا الأسبوع - بنفسجي فاتح
            Stat::make('🆕 New This Week', number_format($newThisWeek))
                ->description('عقارات مضافة هذا الأسبوع')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('violet')
                ->chart([0, 1, 0, 2, 1, 3, $newThisWeek])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-violet-50 to-violet-100 dark:from-violet-900/20 dark:to-violet-800/20 border-l-4 border-violet-500',
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
    protected int | string | array $columnSpan = 'full';    /**
     * ترتيب الويدجت
     */
    protected static ?int $sort = 2;

    /**
     * عنوان الويدجت
     */
    protected ?string $heading = 'Property Metrics';
}

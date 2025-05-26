<?php

namespace App\Filament\Resources\PropertyResource\Widgets;

use App\Models\Property;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class PropertyAdvancedStats extends BaseWidget
{
    protected function getStats(): array
    {
        // إحصائيات متقدمة
        $totalProperties = Property::count();
        $averageArea = Property::where('total_area', '>', 0)->avg('total_area');
        $largestProperty = Property::max('total_area');
        $smallestProperty = Property::where('total_area', '>', 0)->min('total_area');
        
        // إحصائيات الطوابق
        $averageFloors = Property::where('floors_count', '>', 0)->avg('floors_count');
        $maxFloors = Property::max('floors_count');
        
        // إحصائيات حسب العقد المسؤول
        $accWithMostProperties = Property::selectRaw('acc_id, COUNT(*) as count')
            ->groupBy('acc_id')
            ->orderBy('count', 'desc')
            ->with('acc')
            ->first();
        
        // العقارات القديمة والجديدة
        $oldestProperty = Property::where('birth_date', '!=', null)
            ->orderBy('birth_date', 'asc')
            ->first();
        
        $newestProperty = Property::where('birth_date', '!=', null)
            ->orderBy('birth_date', 'desc')
            ->first();

        return [
            // 1. متوسط المساحة
            Stat::make('📏 Average Area', number_format($averageArea, 0) . ' m²')
                ->description('متوسط مساحة العقارات')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('indigo')
                ->chart([100, 150, 120, 180, 160, 200, $averageArea / 10])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/20 border-l-4 border-indigo-500',
                ]),

            // 2. أكبر عقار
            Stat::make('🏗️ Largest Property', number_format($largestProperty) . ' m²')
                ->description('أكبر عقار من حيث المساحة')
                ->descriptionIcon('heroicon-m-arrows-pointing-out')
                ->color('emerald')
                ->chart([50, 100, 150, 200, 180, 220, $largestProperty / 100])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20 border-l-4 border-emerald-500',
                ]),

            // 3. متوسط الطوابق
            Stat::make('🏢 Average Floors', number_format($averageFloors, 1))
                ->description('متوسط عدد الطوابق')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('cyan')
                ->chart([1, 2, 3, 2, 4, 3, $averageFloors])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-cyan-50 to-cyan-100 dark:from-cyan-900/20 dark:to-cyan-800/20 border-l-4 border-cyan-500',
                ]),

            // 4. أعلى مبنى
            Stat::make('🏗️ Tallest Building', $maxFloors . ' floors')
                ->description('أعلى مبنى')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('amber')
                ->chart([1, 3, 5, 4, 6, 8, $maxFloors])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20 border-l-4 border-amber-500',
                ]),

            // 5. مدير الحساب الأكثر إنتاجية
            Stat::make('👨‍💼 Top Account Manager', $accWithMostProperties ? $accWithMostProperties->acc->firstname : 'N/A')
                ->description($accWithMostProperties ? "يدير {$accWithMostProperties->count} عقارات" : 'لا توجد بيانات')
                ->descriptionIcon('heroicon-m-user-circle')
                ->color('violet')
                ->chart([1, 2, 3, 4, 3, 5, $accWithMostProperties ? $accWithMostProperties->count : 0])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-violet-50 to-violet-100 dark:from-violet-900/20 dark:to-violet-800/20 border-l-4 border-violet-500',
                ]),

            // 6. أقدم عقار
            Stat::make('🏛️ Oldest Property', $oldestProperty ? Carbon::parse($oldestProperty->birth_date)->format('Y') : 'N/A')
                ->description($oldestProperty ? 'سنة البناء الأقدم' : 'لا توجد بيانات')
                ->descriptionIcon('heroicon-m-clock')
                ->color('stone')
                ->chart([1950, 1960, 1970, 1980, 1990, 2000, $oldestProperty ? Carbon::parse($oldestProperty->birth_date)->year : 2000])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-stone-50 to-stone-100 dark:from-stone-900/20 dark:to-stone-800/20 border-l-4 border-stone-500',
                ]),
        ];
    }

    /**
     * تحديث الويدجت كل دقيقة
     */
    protected static ?string $pollingInterval = '60s';

    /**
     * عدد الأعمدة في الشبكة
     */
    protected int | string | array $columnSpan = 'full';

    /**
     * ترتيب الويدجت
     */
    protected static ?int $sort = 5;
}

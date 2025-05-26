<?php

namespace App\Filament\Resources\PropertyResource\Widgets;

use App\Models\Property;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PropertyTypesStats extends BaseWidget
{
    protected function getStats(): array
    {
        // إحصائيات Primary Type (النوع الأساسي)
        $villaCount = Property::where('type1', 'villa')->count();
        $houseCount = Property::where('type1', 'house')->count();
        $warehouseCount = Property::where('type1', 'warehouse')->count();
        $buildingCount = Property::where('type1', 'building')->count();
        $apartmentCount = Property::where('type1', 'apartment')->count();
        $shopCount = Property::where('type1', 'shop')->count();
        
        // إجمالي العقارات
        $totalProperties = Property::count();
        
        // حساب النسب المئوية للأنواع الأساسية
        $villaPercentage = $totalProperties > 0 ? round(($villaCount / $totalProperties) * 100, 1) : 0;
        $housePercentage = $totalProperties > 0 ? round(($houseCount / $totalProperties) * 100, 1) : 0;
        $warehousePercentage = $totalProperties > 0 ? round(($warehouseCount / $totalProperties) * 100, 1) : 0;
        $buildingPercentage = $totalProperties > 0 ? round(($buildingCount / $totalProperties) * 100, 1) : 0;
        $apartmentPercentage = $totalProperties > 0 ? round(($apartmentCount / $totalProperties) * 100, 1) : 0;
        $shopPercentage = $totalProperties > 0 ? round(($shopCount / $totalProperties) * 100, 1) : 0;

        return [            // 1. الفلل - بنفسجي
            Stat::make('🏰 Villas', number_format($villaCount))
                ->description("{$villaPercentage}% - فلل فاخرة")
                ->descriptionIcon('heroicon-m-star')
                ->color('purple')
                ->chart([3, 5, 7, 6, 9, 12, $villaCount])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 border-l-4 border-purple-500',
                ]),

            // 2. المنازل - وردي
            Stat::make('🏡 Houses', number_format($houseCount))
                ->description("{$housePercentage}% - بيوت سكنية")
                ->descriptionIcon('heroicon-m-home-modern')
                ->color('pink')
                ->chart([4, 6, 8, 7, 10, 13, $houseCount])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-pink-50 to-pink-100 dark:from-pink-900/20 dark:to-pink-800/20 border-l-4 border-pink-500',
                ]),

            // 3. المستودعات - رمادي
            Stat::make('🏪 Warehouses', number_format($warehouseCount))
                ->description("{$warehousePercentage}% - مستودعات تخزين")
                ->descriptionIcon('heroicon-m-cube')
                ->color('gray')
                ->chart([1, 3, 4, 3, 5, 7, $warehouseCount])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900/20 dark:to-gray-800/20 border-l-4 border-gray-500',
                ]),

            // 4. المباني - أزرق داكن
            Stat::make('🏢 Buildings', number_format($buildingCount))
                ->description("{$buildingPercentage}% - مباني متعددة الطوابق")
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('slate')
                ->chart([2, 4, 5, 4, 6, 8, $buildingCount])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-900/20 dark:to-slate-800/20 border-l-4 border-slate-500',
                ]),

            // 5. الشقق - أزرق فاتح
            Stat::make('🏠 Apartments', number_format($apartmentCount))
                ->description("{$apartmentPercentage}% - شقق سكنية")
                ->descriptionIcon('heroicon-m-building-office')
                ->color('cyan')
                ->chart([5, 7, 9, 8, 11, 14, $apartmentCount])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-cyan-50 to-cyan-100 dark:from-cyan-900/20 dark:to-cyan-800/20 border-l-4 border-cyan-500',
                ]),

            // 6. المحلات التجارية - أصفر
            Stat::make('🏬 Shops', number_format($shopCount))
                ->description("{$shopPercentage}% - محلات تجارية")
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('amber')
                ->chart([2, 3, 4, 3, 5, 6, $shopCount])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20 border-l-4 border-amber-500',
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
    protected static ?int $sort = 3;

    /**
     * عنوان الويدجت
     */
    protected ?string $heading = 'Property Types Breakdown';
}

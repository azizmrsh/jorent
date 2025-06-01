<?php

namespace App\Filament\Resources\PropertyResource\Widgets;

use App\Models\Property;
use App\Models\Unit;
use App\Models\Contract1;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class PropertyStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        // ===== البيانات الأساسية =====
        $totalProperties = Property::count();
        $recentProperties = Property::where('created_at', '>=', now()->subDays(30))->count();
        
        // ===== إحصائيات أنواع العقارات =====
        $buildingCount = Property::where('type1', 'building')->count();
        $villaCount = Property::where('type1', 'villa')->count();
        $houseCount = Property::where('type1', 'house')->count();
        $warehouseCount = Property::where('type1', 'warehouse')->count();
        
        $types = [
            'Buildings' => $buildingCount,
            'Villas' => $villaCount,
            'Houses' => $houseCount,
            'Warehouses' => $warehouseCount
        ];
        $mostCommonType = array_keys($types, max($types))[0] ?? 'Mixed';
        $mostCommonCount = max($types);
        $typePercentage = $totalProperties > 0 ? round(($mostCommonCount / $totalProperties) * 100, 1) : 0;

        // ===== إحصائيات أنواع الاستخدام =====
        $residentialCount = Property::where('type2', 'residential')->count();
        $commercialCount = Property::where('type2', 'commercial')->count();
        $industrialCount = Property::where('type2', 'industrial')->count();
        
        $usageTypes = [
            'Residential' => $residentialCount,
            'Commercial' => $commercialCount,
            'Industrial' => $industrialCount
        ];
        $dominantUsage = array_keys($usageTypes, max($usageTypes))[0] ?? 'Mixed';
        $dominantCount = max($usageTypes);
        $usagePercentage = $totalProperties > 0 ? round(($dominantCount / $totalProperties) * 100, 1) : 0;

        // ===== العقارات المتاحة =====
        $propertiesWithAvailableUnits = Property::whereHas('units', function ($query) {
            $query->where('status', 'available');
        })->count();
        $availabilityPercentage = $totalProperties > 0 ? round(($propertiesWithAvailableUnits / $totalProperties) * 100, 1) : 0;

        // ===== الإحصائيات المالية =====
        // القيمة الإجمالية
        $totalValue = Unit::whereHas('property')->sum('price');
        $propertiesWithUnits = Property::has('units')->count();
        $averageValue = $propertiesWithUnits > 0 ? $totalValue / $propertiesWithUnits : 0;

        // الإيرادات الشهرية
        $monthlyRevenue = Contract1::where('status', 'active')
            ->whereHas('unit.property')
            ->sum('monthly_rent');
        $annualRevenue = $monthlyRevenue * 12;

        // متوسط قيمة العقار ونطاق الأسعار
        if ($propertiesWithUnits == 0) {
            $avgPropertyValue = 0;
            $highestValue = 0;
            $lowestValue = 0;
        } else {
            $propertyValues = Property::has('units')
                ->with('units')
                ->get()
                ->map(function ($property) {
                    return $property->units->sum('price');
                });
            
            $avgPropertyValue = $propertyValues->avg();
            $highestValue = $propertyValues->max();
            $lowestValue = $propertyValues->min();
        }

        // تكاليف الصيانة المقدرة (2.5% من القيمة الإجمالية سنوياً)
        $estimatedAnnualMaintenance = $totalValue * 0.025;
        $monthlyMaintenance = $estimatedAnnualMaintenance / 12;
        $avgMaintenancePerProperty = $propertiesWithUnits > 0 ? $monthlyMaintenance / $propertiesWithUnits : 0;

        // حساب نسبة النمو
        $thisMonthGrowth = $recentProperties > 0 ? round(($recentProperties / max($totalProperties - $recentProperties, 1)) * 100, 1) : 0;

        return [
            // ============ الصف الأول: الإحصائيات العامة ============
            
            // 1. إجمالي العقارات
            Stat::make('Total Properties', number_format($totalProperties))
                ->description("Recent: {$recentProperties} (+{$thisMonthGrowth}%)")
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('info')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-cyan-50 to-cyan-100 dark:from-cyan-900/20 dark:to-cyan-800/20',
                ]),

            // 2. النوع الأكثر شيوعاً
            Stat::make('Most Common Type', $mostCommonType)
                ->description("{$mostCommonCount} properties ({$typePercentage}%)")
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20',
                ]),

            // 3. نوع الاستخدام الأساسي
            Stat::make('Primary Usage', $dominantUsage)
                ->description("{$dominantCount} properties ({$usagePercentage}%)")
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('success')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20',
                ]),

            // 4. العقارات المتاحة
            Stat::make('Available Properties', number_format($propertiesWithAvailableUnits))
                ->description("{$availabilityPercentage}% of total properties")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20',
                ]),

            // ============ الصف الثاني: الإحصائيات المالية ============

            // 5. القيمة الإجمالية للمحفظة
            Stat::make('Total Portfolio Value', '$' . number_format($totalValue, 0))
                ->description("Avg: $" . number_format($averageValue, 0) . " per property")
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20',
                ]),

            // 6. الإيرادات الشهرية
            Stat::make('Monthly Revenue', '$' . number_format($monthlyRevenue, 0))
                ->description("Annual: $" . number_format($annualRevenue, 0))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20',
                ]),

            // 7. متوسط قيمة العقار
            Stat::make('Avg Property Value', '$' . number_format($avgPropertyValue, 0))
                ->description("Range: $" . number_format($lowestValue, 0) . " - $" . number_format($highestValue, 0))
                ->descriptionIcon('heroicon-m-calculator')
                ->color('info')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20',
                ]),

            // 8. تكاليف الصيانة
            Stat::make('Monthly Maintenance', '$' . number_format($monthlyMaintenance, 0))
                ->description("Avg: $" . number_format($avgMaintenancePerProperty, 0) . " per property")
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('danger')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20',
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
                    'icon' => 'heroicon-m-arrow-trending-up'
                ];
            }
            return [
                'description' => 'لا توجد إضافات جديدة',
                'icon' => 'heroicon-m-minus-circle'
            ];
        }

        $percentageChange = round((($current - $previous) / $previous) * 100, 1);
        
        if ($percentageChange > 0) {
            return [
                'description' => "+{$percentageChange}% عن الشهر الماضي",
                'icon' => 'heroicon-m-arrow-trending-up'
            ];
        } elseif ($percentageChange < 0) {
            return [
                'description' => "{$percentageChange}% عن الشهر الماضي",
                'icon' => 'heroicon-m-arrow-trending-down'
            ];
        } else {
            return [
                'description' => 'نفس عدد الشهر الماضي',
                'icon' => 'heroicon-m-minus-circle'
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
    protected static ?string $maxHeight = '400px';
}

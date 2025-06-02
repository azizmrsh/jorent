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
        $totalProperties = Property::count();
        $recentProperties = Property::where('created_at', '>=', now()->subDays(30))->count();
        
        $propertiesWithAvailableUnits = Property::whereHas('units', function ($query) {
            $query->where('status', 'available');
        })->count();
        
        $monthlyRevenue = Contract1::where('status', 'active')
            ->whereHas('unit.property')
            ->sum('rent_amount');
        
        $thisMonthGrowth = $recentProperties > 0 ? round(($recentProperties / max($totalProperties - $recentProperties, 1)) * 100, 1) : 0;

        return [
            Stat::make('Total Properties', number_format($totalProperties))
                ->description("New: {$recentProperties} (+{$thisMonthGrowth}%)")
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('info'),

            Stat::make('Available Properties', number_format($propertiesWithAvailableUnits))
                ->description($totalProperties > 0 ? round(($propertiesWithAvailableUnits / $totalProperties) * 100, 1) . '% available' : '0% available')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Monthly Revenue', '$' . number_format($monthlyRevenue, 0))
                ->description('From active contracts')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),

            Stat::make('Property Types', $this->getMostCommonType())
                ->description('Most common type')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('primary'),
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
    protected static ?string $pollingInterval = '30s';    /**
     * تخصيص ارتفاع الويدجت
     */
    protected static ?string $maxHeight = '400px';
    
    /**
     * Set the number of columns for the stats grid
     * This ensures 4 widgets per row (12/3 = 4 widgets)
     */
    protected function getColumns(): int
    {
        return 4; // 4 widgets per row
    }

    protected function getMostCommonType(): string
    {
        $types = [
            'Buildings' => Property::where('type1', 'building')->count(),
            'Villas' => Property::where('type1', 'villa')->count(),
            'Houses' => Property::where('type1', 'house')->count(),
            'Warehouses' => Property::where('type1', 'warehouse')->count()
        ];
        
        $mostCommonType = array_keys($types, max($types))[0] ?? 'Mixed';
        $count = max($types);
        
        return "{$mostCommonType} ({$count})";
    }
}

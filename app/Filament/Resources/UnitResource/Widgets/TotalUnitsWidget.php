<?php

namespace App\Filament\Resources\UnitResource\Widgets;

use App\Models\Unit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalUnitsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 1;

    protected function getStats(): array
    {
        // إحصائيات الوحدات المجمعة
        $totalUnits = Unit::count();
        $recentUnits = Unit::where('created_at', '>=', now()->subDays(30))->count();
        $rentedCount = Unit::where('status', 'rented')->count();
        $availableCount = Unit::where('status', 'available')->count();
        $totalRevenue = Unit::sum('rental_price');
        
        // حساب النسب
        $occupancyRate = $totalUnits > 0 ? round(($rentedCount / $totalUnits) * 100, 1) : 0;
        $availablePercentage = $totalUnits > 0 ? round(($availableCount / $totalUnits) * 100, 1) : 0;

        return [
            // 📊 إجمالي الوحدات
            Stat::make('Total Units', number_format($totalUnits))
                ->description("Recent: {$recentUnits} units")
                ->descriptionIcon('heroicon-m-building-office')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20',
                ]),

            // 📈 نسبة الإشغال
            Stat::make('Occupancy Rate', $occupancyRate . '%')
                ->description("{$rentedCount} of {$totalUnits} units rented")
                ->descriptionIcon('heroicon-m-chart-pie')
                ->color('success')
                ->chart([65, 70, 68, 72, 75, 73, 77, 78])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20',
                ]),

            // ✅ الوحدات المتاحة  
            Stat::make('Available Units', number_format($availableCount))
                ->description("{$availablePercentage}% of total units")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('warning')
                ->chart([12, 15, 14, 18, 20, 16, 19, 22])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20',
                ]),

            // 💰 إجمالي الإيرادات المحتملة
            Stat::make('Revenue Potential', number_format($totalRevenue, 0) . ' JOD')
                ->description("From {$totalUnits} units monthly")
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart([850, 900, 920, 980, 1050, 1100, 1180, 1250])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20',
                ]),
        ];
    }
}

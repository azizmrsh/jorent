<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class UserStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // احصائيات أساسية
        $totalManagers = User::count();
        $activeManagers = User::where('status', 'active')->count();
        $inactiveManagers = User::where('status', '!=', 'active')->count();
        $newThisMonth = User::whereMonth('created_at', Carbon::now()->month)
                           ->whereYear('created_at', Carbon::now()->year)
                           ->count();

        // حساب النسب المئوية
        $activePercentage = $totalManagers > 0 ? round(($activeManagers / $totalManagers) * 100, 1) : 0;
        $inactivePercentage = $totalManagers > 0 ? round(($inactiveManagers / $totalManagers) * 100, 1) : 0;

        // حساب نسبة التغيير للشهر الماضي
        $lastMonthTotal = User::whereMonth('created_at', Carbon::now()->subMonth()->month)
                             ->whereYear('created_at', Carbon::now()->subMonth()->year)
                             ->count();
        
        $newManagersChange = $this->calculatePercentageChange($newThisMonth, $lastMonthTotal);

        return [            // 1. إجمالي المديرين
            Stat::make('Total Managers', $totalManagers)
                ->description('Total number of managers registered in the system')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary')
                ->chart([7, 12, 8, 15, 11, 18, $totalManagers])
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800',
                ]),

            // 2. المديرين النشطين
            Stat::make('Active Managers', $activeManagers)
                ->description("{$activePercentage}%  of Total Managers")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([5, 8, 12, 15, 18, 22, $activeManagers])
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:bg-green-50 dark:hover:bg-green-900/20',
                ]),

            // 3. المديرين غير النشطين/المعلقين
            Stat::make('Inactive/Pending', $inactiveManagers)
                ->description("{$inactivePercentage}% of Total Managers")
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('warning')
                ->chart([2, 3, 1, 4, 2, 1, $inactiveManagers])
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:bg-orange-50 dark:hover:bg-orange-900/20',
                ]),

            // 4. المديرين الجدد هذا الشهر
            Stat::make('New This Month', $newThisMonth)
                ->description($newManagersChange['description'])
                ->descriptionIcon($newManagersChange['icon'])
                ->color('info')
                ->chart([0, 1, 2, 1, 3, 2, $newThisMonth])
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900/20',
                ]),
        ];
    }

    /**
     * حساب نسبة التغيير مع الوصف والأيقونة المناسبة
     */    private function calculatePercentageChange(int $current, int $previous): array
    {
        if ($previous == 0) {
            if ($current > 0) {
                return [
                    'description' => "جديد! {$current} مديرين مضافين",
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
    protected static ?string $maxHeight = '200px';
}

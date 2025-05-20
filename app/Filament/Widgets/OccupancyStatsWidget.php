<?php

namespace App\Filament\Widgets;

use App\Models\Property;
use App\Models\Unit;
use App\Models\Tenant;
use App\Models\Contract;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class OccupancyStatsWidget extends BaseWidget
{
    protected static ?int $sort = 7;

    protected function getStats(): array
    {
        // إجمالي عدد الوحدات
        $totalUnits = Unit::count();
        
        // عدد الوحدات المشغولة (التي لها عقود نشطة)
        $occupiedUnits = Unit::whereHas('contracts', function ($query) {
            $query->where('status', 'active');
        })->count();
        
        // حساب نسبة الإشغال
        $occupancyRate = $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100, 1) : 0;
        
        // حساب عدد المستأجرين مقارنة بالعام الماضي
        $tenantsThisYear = Tenant::whereYear('created_at', Carbon::now()->year)->count();
        $tenantsLastYear = Tenant::whereYear('created_at', Carbon::now()->subYear()->year)->count();
        $tenantGrowth = $tenantsLastYear > 0 ? round((($tenantsThisYear - $tenantsLastYear) / $tenantsLastYear) * 100, 1) : 0;
        
        // العقود المنتهية قريباً
        $expiringContracts = Contract::where('status', 'active')
            ->whereBetween('end_date', [Carbon::now(), Carbon::now()->addDays(30)])
            ->count();
        
        return [
            Stat::make('نسبة إشغال الوحدات', $occupancyRate . '%')
                ->description($occupiedUnits . ' من ' . $totalUnits . ' وحدة مشغولة')
                ->descriptionIcon('heroicon-m-home')
                ->color($occupancyRate > 75 ? 'success' : ($occupancyRate > 50 ? 'warning' : 'danger')),
                
            Stat::make('نمو المستأجرين', $tenantGrowth > 0 ? '+' . $tenantGrowth . '%' : $tenantGrowth . '%')
                ->description('مقارنة بالعام الماضي')
                ->descriptionIcon($tenantGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($tenantGrowth > 0 ? 'success' : 'danger'),
                
            Stat::make('عقود تنتهي قريباً', $expiringContracts)
                ->description('خلال الـ 30 يوم القادمة')
                ->descriptionIcon('heroicon-m-clock')
                ->color($expiringContracts > 5 ? 'danger' : ($expiringContracts > 0 ? 'warning' : 'success')),
        ];
    }
}

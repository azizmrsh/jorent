<?php

namespace App\Filament\Tenant\Widgets;

use App\Models\Contract1;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TenantStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $tenantId = auth('tenant')->id();
        
        // العقود النشطة
        $activeContracts = Contract1::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->count();
            
        // إجمالي العقود
        $totalContracts = Contract1::where('tenant_id', $tenantId)->count();
        
        // إجمالي المدفوعات
        $totalPayments = Payment::whereHas('contract', function ($query) use ($tenantId) {
            $query->where('tenant_id', $tenantId);
        })->where('status', 'completed')->sum('amount');
        
        // المدفوعات المعلقة
        $pendingPayments = Payment::whereHas('contract', function ($query) use ($tenantId) {
            $query->where('tenant_id', $tenantId);
        })->where('status', 'pending')->count();
        
        return [
            Stat::make('العقود النشطة', $activeContracts)
                ->description('العقود الحالية السارية')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success')
                ->chart([1, 3, 5, 10, 20, 40]),

            Stat::make('إجمالي العقود', $totalContracts)
                ->description('جميع العقود المبرمة')
                ->descriptionIcon('heroicon-m-document-duplicate')
                ->color('primary')
                ->chart([2, 4, 6, 8, 10, 12]),

            Stat::make('إجمالي المدفوعات', number_format($totalPayments, 2) . ' ريال')
                ->description('المبلغ الإجمالي المدفوع')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart([10, 20, 30, 40, 50, 60]),

            Stat::make('المدفوعات المعلقة', $pendingPayments)
                ->description('في انتظار المعالجة')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingPayments > 0 ? 'warning' : 'success')
                ->chart([8, 6, 4, 2, 1, 0]),
        ];
    }
    
    protected function getColumns(): int
    {
        return 4;
    }
}

<?php

namespace App\Filament\Resources\Contract1Resource\Widgets;

use App\Models\Contract1;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class ContractsOverviewStats extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';
    
    protected function getStats(): array
    {
        // إجمالي العقود
        $totalContracts = Contract1::count();
        $activeContracts = Contract1::where('status', 'active')->count();
        $expiredContracts = Contract1::where('status', 'expired')->count();
        $terminatedContracts = Contract1::where('status', 'terminated')->count();
        
        // العقود الجديدة هذا الشهر
        $newContractsThisMonth = Contract1::where('created_at', '>=', Carbon::now()->startOfMonth())->count();
        $lastMonthContracts = Contract1::where('created_at', '>=', Carbon::now()->subMonth()->startOfMonth())
                                     ->where('created_at', '<', Carbon::now()->startOfMonth())->count();
        $contractsGrowth = $lastMonthContracts > 0 ? 
            round((($newContractsThisMonth - $lastMonthContracts) / $lastMonthContracts) * 100, 1) : 0;

        // العقود المنتهية قريباً (خلال 30 يوم)
        $expiringSoon = Contract1::where('status', 'active')
                                ->where('end_date', '<=', Carbon::now()->addDays(30))
                                ->where('end_date', '>=', Carbon::now())
                                ->count();

        // إجمالي قيمة العقود النشطة
        $totalActiveValue = Contract1::where('status', 'active')->sum('rent_amount');
        
        // متوسط قيمة العقد
        $avgContractValue = $activeContracts > 0 ? round($totalActiveValue / $activeContracts, 2) : 0;

        // معدل التجديد
        $renewedContracts = Contract1::where('created_at', '>=', Carbon::now()->subYear())
                                   ->whereNotNull('previous_contract_id')->count();
        $expiredLastYear = Contract1::where('end_date', '>=', Carbon::now()->subYear())
                                  ->where('end_date', '<', Carbon::now())
                                  ->count();
        $renewalRate = $expiredLastYear > 0 ? round(($renewedContracts / $expiredLastYear) * 100, 1) : 0;

        // العقود المتأخرة في الدفع
        $overdueContracts = Contract1::where('status', 'active')
                                   ->whereHas('payments', function ($query) {
                                       $query->where('due_date', '<', Carbon::now())
                                             ->where('status', '!=', 'paid');
                                   })->count();

        return [
            // الصف الأول - الإحصائيات الأساسية
            Stat::make('📋 Total Contracts', number_format($totalContracts))
                ->description($newContractsThisMonth . ' added this month')
                ->descriptionIcon($contractsGrowth > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($contractsGrowth > 0 ? 'success' : ($contractsGrowth < 0 ? 'danger' : 'gray'))
                ->chart([3, 5, 4, 6, 8, 7, 9, 6, 8, 10, 12, $newContractsThisMonth])
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;'
                ]),

            Stat::make('✅ Active Contracts', number_format($activeContracts))
                ->description(round(($activeContracts / max($totalContracts, 1)) * 100, 1) . '% of total')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white;'
                ]),

            Stat::make('⚠️ Expiring Soon', number_format($expiringSoon))
                ->description('Within next 30 days')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($expiringSoon > 5 ? 'danger' : ($expiringSoon > 0 ? 'warning' : 'success'))
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #ffeaa7 0%, #fab1a0 100%); color: #2d3436;'
                ]),

            Stat::make('💰 Total Active Value', '$' . number_format($totalActiveValue))
                ->description('Monthly rental income')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #00b09b 0%, #96c93d 100%); color: white;'
                ]),

            // الصف الثاني - تفاصيل إضافية
            Stat::make('📊 Average Contract Value', '$' . number_format($avgContractValue))
                ->description('Per active contract')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('info')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;'
                ]),

            Stat::make('🔄 Renewal Rate', $renewalRate . '%')
                ->description($renewedContracts . ' renewed contracts')
                ->descriptionIcon($renewalRate > 70 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($renewalRate > 70 ? 'success' : ($renewalRate < 50 ? 'danger' : 'warning'))
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;'
                ]),

            Stat::make('❌ Expired Contracts', number_format($expiredContracts))
                ->description(round(($expiredContracts / max($totalContracts, 1)) * 100, 1) . '% of total')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #fc4a1a 0%, #f7b733 100%); color: white;'
                ]),

            Stat::make('🚨 Overdue Payments', number_format($overdueContracts))
                ->description('Contracts with overdue payments')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color($overdueContracts > 0 ? 'danger' : 'success')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%); color: white;'
                ]),
        ];
    }
}

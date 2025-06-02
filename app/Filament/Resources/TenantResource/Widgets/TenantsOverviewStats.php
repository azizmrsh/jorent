<?php

namespace App\Filament\Resources\TenantResource\Widgets;

use App\Models\Tenant;
use App\Models\Contract1;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class TenantsOverviewStats extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';
    
    protected function getStats(): array
    {
        // إجمالي المستأجرين
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::whereHas('contracts', function ($query) {
            $query->where('status', 'active');
        })->count();
        
        // المستأجرين الجدد هذا الشهر
        $newTenantsThisMonth = Tenant::where('created_at', '>=', Carbon::now()->startOfMonth())->count();
        $lastMonthTenants = Tenant::where('created_at', '>=', Carbon::now()->subMonth()->startOfMonth())
                                  ->where('created_at', '<', Carbon::now()->startOfMonth())->count();
        $tenantsGrowth = $lastMonthTenants > 0 ? 
            round((($newTenantsThisMonth - $lastMonthTenants) / $lastMonthTenants) * 100, 1) : 0;

        // المستأجرين المتأخرين في الدفع
        $latePaymentTenants = Tenant::whereHas('contracts.payments', function ($query) {
            $query->where('due_date', '<', Carbon::now())
                  ->where('status', '!=', 'paid');
        })->count();

        // المستأجرين طويلي الأمد (أكثر من سنة)
        $longTermTenants = Tenant::whereHas('contracts', function ($query) {
            $query->where('status', 'active')
                  ->where('start_date', '<=', Carbon::now()->subYear());
        })->count();

        // متوسط فترة الإقامة
        $avgTenancyDuration = Tenant::whereHas('contracts', function ($query) {
            $query->where('status', 'active');
        })->with(['contracts' => function ($query) {
            $query->where('status', 'active')->select('tenant_id', 'start_date');
        }])->get()->map(function ($tenant) {
            $oldestContract = $tenant->contracts->sortBy('start_date')->first();
            return $oldestContract ? Carbon::now()->diffInDays($oldestContract->start_date) : 0;
        })->average();
        
        $avgTenancyMonths = $avgTenancyDuration ? round($avgTenancyDuration / 30, 1) : 0;

        // معدل الاحتفاظ بالمستأجرين
        $expiredContracts = Contract1::where('end_date', '>=', Carbon::now()->subYear())
                                   ->where('end_date', '<', Carbon::now())
                                   ->count();
        $renewedContracts = Contract1::where('created_at', '>=', Carbon::now()->subYear())
                                   ->whereHas('tenant.contracts', function ($query) {
                                       $query->where('end_date', '<', Carbon::now())
                                             ->where('end_date', '>=', Carbon::now()->subYear());
                                   })->count();
        $retentionRate = $expiredContracts > 0 ? round(($renewedContracts / $expiredContracts) * 100, 1) : 0;

        // أفضل المستأجرين (الذين لا يتأخرون في الدفع)
        $excellentTenants = Tenant::whereHas('contracts', function ($query) {
            $query->where('status', 'active');
        })->whereDoesntHave('contracts.payments', function ($query) {
            $query->where('due_date', '<', Carbon::now())
                  ->where('status', '!=', 'paid');
        })->count();

        return [
            // الصف الأول
            Stat::make('👥 Total Tenants', number_format($totalTenants))
                ->description($newTenantsThisMonth . ' added this month')
                ->descriptionIcon($tenantsGrowth > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($tenantsGrowth > 0 ? 'success' : ($tenantsGrowth < 0 ? 'danger' : 'gray'))
                ->chart([5, 3, 7, 4, 6, 8, 5, 9, 7, 6, 8, $newTenantsThisMonth])
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;'
                ]),

            Stat::make('✅ Active Tenants', number_format($activeTenants))
                ->description(round(($activeTenants / max($totalTenants, 1)) * 100, 1) . '% of total')
                ->descriptionIcon('heroicon-m-user-check')
                ->color('success')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white;'
                ]),

            Stat::make('⏰ Late Payments', number_format($latePaymentTenants))
                ->description('Tenants with overdue payments')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($latePaymentTenants > 0 ? 'danger' : 'success')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #fc4a1a 0%, #f7b733 100%); color: white;'
                ]),

            Stat::make('🏆 Excellent Tenants', number_format($excellentTenants))
                ->description('No payment delays')
                ->descriptionIcon('heroicon-m-star')
                ->color('success')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #ffeaa7 0%, #fab1a0 100%); color: #2d3436;'
                ]),

            // الصف الثاني
            Stat::make('📅 Average Tenancy', $avgTenancyMonths . ' months')
                ->description('Average duration per tenant')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;'
                ]),

            Stat::make('🔄 Retention Rate', $retentionRate . '%')
                ->description($renewedContracts . ' renewals from ' . $expiredContracts . ' expired')
                ->descriptionIcon($retentionRate > 70 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($retentionRate > 70 ? 'success' : ($retentionRate < 50 ? 'danger' : 'warning'))
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #374151;'
                ]),

            Stat::make('⭐ Long-term Tenants', number_format($longTermTenants))
                ->description('More than 1 year')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('success')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #d299c2 0%, #fef9d7 100%); color: #374151;'
                ]),

            Stat::make('📊 Occupancy Status', round(($activeTenants / max($totalTenants, 1)) * 100, 1) . '%')
                ->description($activeTenants . ' active of ' . $totalTenants . ' total')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($activeTenants > ($totalTenants * 0.8) ? 'success' : 'warning')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); color: #374151;'
                ]),
        ];
    }
}

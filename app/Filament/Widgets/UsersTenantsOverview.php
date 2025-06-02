<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Acc;
use App\Models\Tenant;
use App\Models\Contract1;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class UsersTenantsOverview extends BaseWidget
{
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';
    
    protected function getHeading(): string
    {
        return '👥 Users & Tenants Overview';
    }
    
    protected function getStats(): array
    {
        // إحصائيات المستخدمين
        $totalUsers = User::count();
        $totalAccountManagers = Acc::count();
        $activeAccountManagers = Acc::where('status', 'active')->count();
        
        // إحصائيات المستأجرين
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::whereHas('contracts', function ($query) {
            $query->where('status', 'active');
        })->count();
        $newTenantsThisMonth = Tenant::where('created_at', '>=', Carbon::now()->startOfMonth())->count();
          // إحصائيات التحقق - استخدام الأعمدة الموجودة
        $verifiedTenants = Tenant::whereNotNull('document_type')
            ->whereNotNull('document_number')
            ->count();
        $verificationRate = $totalTenants > 0 ? round(($verifiedTenants / $totalTenants) * 100, 1) : 0;
        
        // متوسط العقود للمستأجر
        $avgContractsPerTenant = $totalTenants > 0 ? round(Contract1::count() / $totalTenants, 1) : 0;
        
        return [
            // الصف الأول - المستخدمين والإدارة
            Stat::make('👤 System Users', number_format($totalUsers))
                ->description("Platform administrators")
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20',
                ]),

            Stat::make('🏢 Account Managers', number_format($totalAccountManagers))
                ->description("{$activeAccountManagers} active managers")
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('info')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-cyan-50 to-cyan-100 dark:from-cyan-900/20 dark:to-cyan-800/20',
                ]),

            Stat::make('👥 Total Tenants', number_format($totalTenants))
                ->description("{$activeTenants} with active contracts")
                ->descriptionIcon('heroicon-m-users')
                ->color('success')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20',
                ]),

            Stat::make('📋 Avg Contracts/Tenant', $avgContractsPerTenant)
                ->description("Contracts per tenant ratio")
                ->descriptionIcon('heroicon-m-document-duplicate')
                ->color('purple')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20',
                ]),

            // الصف الثاني - نشاط المستأجرين
            Stat::make('✅ Verified Tenants', number_format($verifiedTenants))
                ->description("{$verificationRate}% verification rate")
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('emerald')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20',
                ]),

            Stat::make('🆕 New This Month', number_format($newTenantsThisMonth))
                ->description("Tenants joined this month")
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20',
                ]),

            Stat::make('🔄 Active Tenants', number_format($activeTenants))
                ->description("Currently under contract")
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('indigo')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/20',
                ]),

            Stat::make('📊 User Activity', round(($activeTenants / max($totalTenants, 1)) * 100, 1) . '%')
                ->description("Tenant engagement rate")
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('rose')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-rose-50 to-rose-100 dark:from-rose-900/20 dark:to-rose-800/20',
                ]),
        ];
    }
    
    protected function getColumns(): int
    {
        return 4; // 4 widgets في كل صف
    }
}

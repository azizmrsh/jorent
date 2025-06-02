<?php

namespace App\Filament\Widgets;

use App\Models\Property;
use App\Models\Unit;
use App\Models\Contract1;
use App\Models\Tenant;
use App\Models\Payment;
use App\Models\User;
use App\Models\Acc;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class SystemOverviewStats extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';
    
    protected function getStats(): array
    {
        // إجمالي البيانات الأساسية
        $totalProperties = Property::count();
        $totalUnits = Unit::count();
        $totalContracts = Contract1::count();
        $totalTenants = Tenant::count();
        $totalPayments = Payment::count();
        $totalUsers = User::count();
        $totalAccountManagers = Acc::count();
        
        // العقود النشطة
        $activeContracts = Contract1::where('status', 'active')->count();
        $activeContractsPercentage = $totalContracts > 0 ? round(($activeContracts / $totalContracts) * 100, 1) : 0;
        
        // الوحدات المؤجرة
        $rentedUnits = Unit::whereHas('contracts', function ($query) {
            $query->where('status', 'active');
        })->count();
        $occupancyRate = $totalUnits > 0 ? round(($rentedUnits / $totalUnits) * 100, 1) : 0;
        
        // إجمالي الإيرادات الشهرية
        $monthlyRevenue = Contract1::where('status', 'active')->sum('rent_amount');
        
        // المدفوعات هذا الشهر
        $thisMonthPayments = Payment::where('payment_date', '>=', Carbon::now()->startOfMonth())->count();
        $thisMonthAmount = Payment::where('payment_date', '>=', Carbon::now()->startOfMonth())->sum('amount');
        
        return [
            // الصف الأول - الإحصائيات الأساسية
            Stat::make('🏢 Total Properties', number_format($totalProperties))
                ->description("With {$totalUnits} units total")
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20',
                ]),

            Stat::make('📋 Active Contracts', number_format($activeContracts))
                ->description("{$activeContractsPercentage}% of all contracts")
                ->descriptionIcon('heroicon-m-document-check')
                ->color('success')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20',
                ]),

            Stat::make('👥 Total Tenants', number_format($totalTenants))
                ->description("Managed by {$totalAccountManagers} managers")
                ->descriptionIcon('heroicon-m-users')
                ->color('info')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-cyan-50 to-cyan-100 dark:from-cyan-900/20 dark:to-cyan-800/20',
                ]),

            Stat::make('📈 Occupancy Rate', $occupancyRate . '%')
                ->description("{$rentedUnits} of {$totalUnits} units rented")
                ->descriptionIcon('heroicon-m-chart-pie')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20',
                ]),

            // الصف الثاني - الإحصائيات المالية
            Stat::make('💰 Monthly Revenue', 'JOD ' . number_format($monthlyRevenue, 2))
                ->description("From active contracts")
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('emerald')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20',
                ]),

            Stat::make('💳 This Month Payments', number_format($thisMonthPayments))
                ->description('JOD ' . number_format($thisMonthAmount, 2) . ' collected')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('violet')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-violet-50 to-violet-100 dark:from-violet-900/20 dark:to-violet-800/20',
                ]),

            Stat::make('⚙️ System Users', number_format($totalUsers))
                ->description("Including {$totalAccountManagers} property managers")
                ->descriptionIcon('heroicon-m-cog-6-tooth')
                ->color('indigo')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/20',
                ]),

            Stat::make('📊 Total Records', number_format($totalProperties + $totalContracts + $totalTenants + $totalPayments))
                ->description("Across all system modules")
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('gray')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900/20 dark:to-gray-800/20',
                ]),
        ];
    }
    
    protected function getColumns(): int
    {
        return 4; // 4 widgets في كل صف
    }
}

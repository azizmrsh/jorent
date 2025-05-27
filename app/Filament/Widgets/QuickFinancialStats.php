<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use App\Models\Contract1;
use App\Models\Unit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class QuickFinancialStats extends BaseWidget
{
    protected static ?string $heading = '💼 Quick Financial Statistics';
    protected static ?int $sort = 9;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        // Monthly revenue calculations
        $thisMonthRevenue = Payment::whereMonth('payment_date', Carbon::now()->month)
            ->whereYear('payment_date', Carbon::now()->year)
            ->sum('amount');

        $lastMonthRevenue = Payment::whereMonth('payment_date', Carbon::now()->subMonth()->month)
            ->whereYear('payment_date', Carbon::now()->subMonth()->year)
            ->sum('amount');

        // Expected vs actual revenue this month
        $expectedThisMonth = Contract1::whereHas('unit')
            ->where('contract1s.status', 'active')
            ->where('contract1s.start_date', '<=', Carbon::now()->endOfMonth())
            ->where('contract1s.end_date', '>=', Carbon::now()->startOfMonth())
            ->join('units', 'contract1s.unit_id', '=', 'units.id')
            ->sum('units.rental_price');

        // Calculate collection rate
        $collectionRate = $expectedThisMonth > 0 ? round(($thisMonthRevenue / $expectedThisMonth) * 100, 1) : 0;

        // Average rental price
        $averageRent = Unit::where('status', 'rented')->avg('rental_price') ?: 0;

        // Growth calculation
        $revenueGrowth = $lastMonthRevenue > 0 
            ? round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : 0;

        return [
            // 1. This Month Revenue
            Stat::make('💰 This Month Revenue', number_format($thisMonthRevenue, 0) . ' JOD')
                ->description($revenueGrowth >= 0 ? "+{$revenueGrowth}% from last month" : "{$revenueGrowth}% from last month")
                ->descriptionIcon($revenueGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueGrowth >= 0 ? 'success' : 'danger')
                ->chart([
                    $lastMonthRevenue * 0.7, 
                    $lastMonthRevenue * 0.8, 
                    $lastMonthRevenue * 0.9, 
                    $lastMonthRevenue, 
                    $thisMonthRevenue * 0.8, 
                    $thisMonthRevenue * 0.9, 
                    $thisMonthRevenue
                ])
                ->extraAttributes([
                    'class' => $revenueGrowth >= 0 
                        ? 'bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20'
                        : 'bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20',
                ]),

            // 2. Collection Rate
            Stat::make('📊 Collection Rate', $collectionRate . '%')
                ->description("Collected {$thisMonthRevenue} of {$expectedThisMonth} JOD")
                ->descriptionIcon($collectionRate >= 80 ? 'heroicon-m-check-circle' : 'heroicon-m-exclamation-triangle')
                ->color($collectionRate >= 80 ? 'success' : ($collectionRate >= 60 ? 'warning' : 'danger'))
                ->chart([60, 65, 70, 75, 80, 85, $collectionRate])
                ->extraAttributes([
                    'class' => $collectionRate >= 80 
                        ? 'bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20'
                        : ($collectionRate >= 60 
                            ? 'bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20'
                            : 'bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20'),
                ]),

            // 3. Average Rent
            Stat::make('🏠 Average Rent', number_format($averageRent, 0) . ' JOD')
                ->description("Average rent per rented unit")
                ->descriptionIcon('heroicon-m-home')
                ->color('info')
                ->chart([
                    $averageRent * 0.8, 
                    $averageRent * 0.85, 
                    $averageRent * 0.9, 
                    $averageRent * 0.95, 
                    $averageRent, 
                    $averageRent * 1.05, 
                    $averageRent
                ])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20',
                ]),

            // 4. Expected vs Actual
            Stat::make('📈 Expected This Month', number_format($expectedThisMonth, 0) . ' JOD')
                ->description($thisMonthRevenue <= $expectedThisMonth ? "Target achievable" : "Target exceeded!")
                ->descriptionIcon($thisMonthRevenue <= $expectedThisMonth ? 'heroicon-m-target' : 'heroicon-m-trophy')
                ->color($thisMonthRevenue >= $expectedThisMonth * 0.8 ? 'success' : 'warning')
                ->chart([
                    $expectedThisMonth * 0.7, 
                    $expectedThisMonth * 0.8, 
                    $expectedThisMonth * 0.9, 
                    $expectedThisMonth, 
                    $expectedThisMonth, 
                    $expectedThisMonth, 
                    $expectedThisMonth
                ])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20',
                ]),
        ];
    }

    /**
     * Update every hour for financial data
     */
    protected static ?string $pollingInterval = '1h';
}

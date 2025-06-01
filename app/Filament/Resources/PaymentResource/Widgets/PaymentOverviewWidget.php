<?php

namespace App\Filament\Resources\PaymentResource\Widgets;

use App\Models\Payment;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PaymentOverviewWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';
    
    protected function getStats(): array
    {
        // Get current date ranges
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();
        
        // Today's payments
        $todayPayments = Payment::whereDate('payment_date', $today)->sum('amount');
        $todayCount = Payment::whereDate('payment_date', $today)->count();
        
        // This month's payments
        $thisMonthPayments = Payment::where('payment_date', '>=', $thisMonth)->sum('amount');
        $thisMonthCount = Payment::where('payment_date', '>=', $thisMonth)->count();
        
        // Last month's payments for comparison
        $lastMonthPayments = Payment::whereBetween('payment_date', [$lastMonth, $lastMonthEnd])->sum('amount');
        
        // Calculate percentage change
        $monthlyChange = $lastMonthPayments > 0 
            ? (($thisMonthPayments - $lastMonthPayments) / $lastMonthPayments) * 100 
            : 0;
        
        // Total payments
        $totalPayments = Payment::sum('amount');
        $totalCount = Payment::count();
        
        // Average payment
        $averagePayment = $totalCount > 0 ? $totalPayments / $totalCount : 0;
        
        return [
            Stat::make('Today\'s Collections', number_format($todayPayments, 2) . ' JOD')
                ->description($todayCount . ' payment' . ($todayCount !== 1 ? 's' : '') . ' received today')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                ->color('success'),
                
            Stat::make('This Month\'s Revenue', number_format($thisMonthPayments, 2) . ' JOD')
                ->description($thisMonthCount . ' payment' . ($thisMonthCount !== 1 ? 's' : '') . ' this month')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3, 4, 6, 7, 10])
                ->color($monthlyChange >= 0 ? 'success' : 'danger')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),
                
            Stat::make('Total Collections', number_format($totalPayments, 2) . ' JOD')
                ->description('From ' . $totalCount . ' total payments • Avg: ' . number_format($averagePayment, 2) . ' JOD')
                ->descriptionIcon('heroicon-m-credit-card')
                ->chart([3, 5, 7, 8, 6, 9, 10, 11, 9, 12, 14, 15])
                ->color('primary'),
        ];
    }
    
    protected function getColumns(): int
    {
        return 3;
    }
}

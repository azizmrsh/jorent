<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PaymentStatsWidget extends BaseWidget
{
    protected static ?int $sort = 5;
    protected function getStats(): array
    {
        // Get current month payments
        $currentMonthPayments = Payment::whereMonth('payment_date', Carbon::now()->month)
            ->whereYear('payment_date', Carbon::now()->year)
            ->sum('amount');
            
        // Get previous month payments
        $previousMonthPayments = Payment::whereMonth('payment_date', Carbon::now()->subMonth()->month)
            ->whereYear('payment_date', Carbon::now()->subMonth()->year)
            ->sum('amount');
            
        // Calculate month-over-month change
        $percentageChange = $previousMonthPayments > 0 
            ? (($currentMonthPayments - $previousMonthPayments) / $previousMonthPayments) * 100 
            : 0;
        
        // Get total payments
        $totalPayments = Payment::sum('amount');
        
        // Get recent completed payments
        $completedPayments = Payment::where('status', 'completed')->count();
        
        // Get pending payments
        $pendingPayments = Payment::where('status', 'pending')->count();
        
        return [
            Stat::make('Current Month Revenue', '$' . number_format($currentMonthPayments, 2))
                ->description($percentageChange >= 0 
                    ? $percentageChange . '% increase' 
                    : abs($percentageChange) . '% decrease')
                ->descriptionIcon($percentageChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($percentageChange >= 0 ? 'success' : 'danger'),
                
            Stat::make('Total Revenue', '$' . number_format($totalPayments, 2))
                ->description('All-time revenue')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
                
            Stat::make('Completed Payments', $completedPayments)
                ->description('Successful transactions')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
                
            Stat::make('Pending Payments', $pendingPayments)
                ->description('Awaiting processing')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}

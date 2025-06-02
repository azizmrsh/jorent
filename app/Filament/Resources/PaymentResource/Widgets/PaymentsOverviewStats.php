<?php

namespace App\Filament\Resources\PaymentResource\Widgets;

use App\Models\Payment;
use App\Models\Contract1;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class PaymentsOverviewStats extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';
    
    protected function getStats(): array
    {
        // إجمالي المدفوعات
        $totalPayments = Payment::count();
        $paidPayments = Payment::where('status', 'paid')->count();
        $pendingPayments = Payment::where('status', 'pending')->count();
        $overduePayments = Payment::where('due_date', '<', Carbon::now())
                                 ->where('status', '!=', 'paid')->count();

        // المدفوعات هذا الشهر
        $thisMonthPayments = Payment::where('payment_date', '>=', Carbon::now()->startOfMonth())
                                   ->where('status', 'paid')->count();
        $thisMonthAmount = Payment::where('payment_date', '>=', Carbon::now()->startOfMonth())
                                 ->where('status', 'paid')->sum('amount');

        // مقارنة مع الشهر السابق
        $lastMonthPayments = Payment::where('payment_date', '>=', Carbon::now()->subMonth()->startOfMonth())
                                   ->where('payment_date', '<', Carbon::now()->startOfMonth())
                                   ->where('status', 'paid')->count();
        $paymentsGrowth = $lastMonthPayments > 0 ? 
            round((($thisMonthPayments - $lastMonthPayments) / $lastMonthPayments) * 100, 1) : 0;

        // المبلغ الإجمالي المحصل
        $totalCollected = Payment::where('status', 'paid')->sum('amount');
        $totalExpected = Payment::sum('amount');
        $collectionRate = $totalExpected > 0 ? round(($totalCollected / $totalExpected) * 100, 1) : 0;

        // متوسط المدفوعات
        $avgPaymentAmount = $paidPayments > 0 ? round($totalCollected / $paidPayments, 2) : 0;

        // طرق الدفع
        $cashPayments = Payment::where('payment_method', 'cash')->where('status', 'paid')->count();
        $bankPayments = Payment::where('payment_method', 'bank_transfer')->where('status', 'paid')->count();
        $onlinePayments = Payment::where('payment_method', 'online')->where('status', 'paid')->count();

        // أداء الدفع هذا الأسبوع
        $thisWeekPayments = Payment::where('payment_date', '>=', Carbon::now()->startOfWeek())
                                  ->where('status', 'paid')->sum('amount');

        return [
            // الصف الأول - الإحصائيات الأساسية
            Stat::make('💳 Total Payments', number_format($totalPayments))
                ->description($thisMonthPayments . ' payments this month')
                ->descriptionIcon($paymentsGrowth > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($paymentsGrowth > 0 ? 'success' : ($paymentsGrowth < 0 ? 'danger' : 'gray'))
                ->chart([8, 6, 10, 7, 9, 12, 8, 11, 9, 14, 12, $thisMonthPayments])
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;'
                ]),

            Stat::make('✅ Paid Payments', number_format($paidPayments))
                ->description(round(($paidPayments / max($totalPayments, 1)) * 100, 1) . '% success rate')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white;'
                ]),

            Stat::make('⏳ Pending Payments', number_format($pendingPayments))
                ->description('Awaiting payment')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #ffeaa7 0%, #fab1a0 100%); color: #2d3436;'
                ]),

            Stat::make('🚨 Overdue Payments', number_format($overduePayments))
                ->description('Past due date')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($overduePayments > 0 ? 'danger' : 'success')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #fc4a1a 0%, #f7b733 100%); color: white;'
                ]),

            // الصف الثاني - التفاصيل المالية
            Stat::make('💰 Total Collected', '$' . number_format($totalCollected))
                ->description('From all paid payments')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;'
                ]),

            Stat::make('📊 Collection Rate', $collectionRate . '%')
                ->description('$' . number_format($totalCollected) . ' of $' . number_format($totalExpected))
                ->descriptionIcon($collectionRate > 80 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($collectionRate > 80 ? 'success' : ($collectionRate < 60 ? 'danger' : 'warning'))
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;'
                ]),

            Stat::make('💵 Average Payment', '$' . number_format($avgPaymentAmount))
                ->description('Per successful payment')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('info')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;'
                ]),

            Stat::make('📅 This Week Revenue', '$' . number_format($thisWeekPayments))
                ->description('Weekly performance')
                ->descriptionIcon('heroicon-m-chart-bar-square')
                ->color('primary')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #374151;'
                ]),
        ];
    }
}

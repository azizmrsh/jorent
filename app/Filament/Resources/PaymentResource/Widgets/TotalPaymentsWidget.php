<?php

namespace App\Filament\Resources\PaymentResource\Widgets;

use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class TotalPaymentsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        // إجمالي المدفوعات
        $totalPayments = Payment::count();
        $totalAmount = Payment::sum('amount');
        
        // المدفوعات هذا الشهر
        $thisMonthPayments = Payment::whereMonth('payment_date', Carbon::now()->month)
            ->whereYear('payment_date', Carbon::now()->year)
            ->count();
            
        $thisMonthAmount = Payment::whereMonth('payment_date', Carbon::now()->month)
            ->whereYear('payment_date', Carbon::now()->year)
            ->sum('amount');
        
        // المدفوعات الشهر الماضي للمقارنة
        $lastMonthPayments = Payment::whereMonth('payment_date', Carbon::now()->subMonth()->month)
            ->whereYear('payment_date', Carbon::now()->subMonth()->year)
            ->count();
            
        // حساب نسبة النمو
        $growthPercentage = $lastMonthPayments > 0 
            ? round((($thisMonthPayments - $lastMonthPayments) / $lastMonthPayments) * 100, 1)
            : 0;

        return [
            // إجمالي المدفوعات
            Stat::make('💰 إجمالي المدفوعات', number_format($totalPayments))
                ->description("مجموع القيم: " . number_format($totalAmount, 2) . " JOD")
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success')
                ->chart([1, 3, 5, 10, 20, 40, $totalPayments])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20',
                ]),

            // المدفوعات هذا الشهر
            Stat::make('📊 مدفوعات هذا الشهر', number_format($thisMonthPayments))
                ->description("القيمة: " . number_format($thisMonthAmount, 2) . " JOD")
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary')
                ->chart([2, 4, 6, 8, 10, 15, $thisMonthPayments])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20',
                ]),

            // النمو الشهري
            Stat::make('📈 النمو الشهري', $thisMonthPayments)
                ->description(
                    $growthPercentage > 0 
                        ? "+{$growthPercentage}% عن الشهر الماضي" 
                        : ($growthPercentage < 0 
                            ? "{$growthPercentage}% عن الشهر الماضي"
                            : "لا يوجد تغيير عن الشهر الماضي")
                )
                ->descriptionIcon(
                    $growthPercentage > 0 
                        ? 'heroicon-m-arrow-trending-up' 
                        : ($growthPercentage < 0 
                            ? 'heroicon-m-arrow-trending-down'
                            : 'heroicon-m-minus')
                )
                ->color($growthPercentage > 0 ? 'success' : ($growthPercentage < 0 ? 'danger' : 'gray'))
                ->chart([5, 10, 15, 20, $lastMonthPayments, $thisMonthPayments])
                ->extraAttributes([
                    'class' => $growthPercentage > 0 
                        ? 'bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20'
                        : ($growthPercentage < 0 
                            ? 'bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20'
                            : 'bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900/20 dark:to-gray-800/20'),
                ]),

            // متوسط القيمة اليومية
            Stat::make('💵 متوسط يومي', 
                $totalPayments > 0 
                    ? number_format($totalAmount / max($totalPayments, 1), 2) . ' JOD'
                    : '0 JOD'
            )
                ->description("متوسط قيمة المدفوعة الواحدة")
                ->descriptionIcon('heroicon-m-calculator')
                ->color('warning')
                ->chart([10, 20, 30, 25, 35, 40, 45])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20',
                ]),
        ];
    }

    /**
     * تحديث الويدجت كل 30 ثانية
     */
    protected static ?string $pollingInterval = '30s';
}

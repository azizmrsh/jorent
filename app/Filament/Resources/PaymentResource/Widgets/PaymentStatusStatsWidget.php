<?php

namespace App\Filament\Resources\PaymentResource\Widgets;

use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PaymentStatusStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $totalPayments = Payment::count();
        
        // حساب عدد كل حالة دفع
        $completedCount = Payment::where('payment_status', 'completed')->count();
        $pendingCount = Payment::where('payment_status', 'pending')->count();
        $failedCount = Payment::where('payment_status', 'failed')->count();
        $cancelledCount = Payment::where('payment_status', 'cancelled')->count();

        // حساب النسب المئوية
        $completedPercentage = $totalPayments > 0 ? round(($completedCount / $totalPayments) * 100, 1) : 0;
        $pendingPercentage = $totalPayments > 0 ? round(($pendingCount / $totalPayments) * 100, 1) : 0;
        $failedPercentage = $totalPayments > 0 ? round(($failedCount / $totalPayments) * 100, 1) : 0;
        $cancelledPercentage = $totalPayments > 0 ? round(($cancelledCount / $totalPayments) * 100, 1) : 0;

        // حساب قيم المبالغ لكل حالة
        $completedAmount = Payment::where('payment_status', 'completed')->sum('amount');
        $pendingAmount = Payment::where('payment_status', 'pending')->sum('amount');

        return [
            // المدفوعات المكتملة
            Stat::make('✅ مدفوعات مكتملة', number_format($completedCount))
                ->description("نسبة {$completedPercentage}% - قيمة " . number_format($completedAmount, 2) . " JOD")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([10, 20, 30, 40, $completedCount])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20',
                ]),

            // المدفوعات قيد الانتظار
            Stat::make('⏳ قيد الانتظار', number_format($pendingCount))
                ->description("نسبة {$pendingPercentage}% - قيمة " . number_format($pendingAmount, 2) . " JOD")
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart([5, 8, 12, 10, $pendingCount])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20',
                ]),

            // المدفوعات الفاشلة
            Stat::make('❌ مدفوعات فاشلة', number_format($failedCount))
                ->description("نسبة {$failedPercentage}% من إجمالي المدفوعات")
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger')
                ->chart([2, 4, 6, 3, $failedCount])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20',
                ]),

            // المدفوعات الملغية
            Stat::make('🚫 مدفوعات ملغية', number_format($cancelledCount))
                ->description("نسبة {$cancelledPercentage}% من إجمالي المدفوعات")
                ->descriptionIcon('heroicon-m-no-symbol')
                ->color('gray')
                ->chart([1, 2, 3, 2, $cancelledCount])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900/20 dark:to-gray-800/20',
                ]),
        ];
    }

    /**
     * تحديث الويدجت كل دقيقة
     */
    protected static ?string $pollingInterval = '60s';
}

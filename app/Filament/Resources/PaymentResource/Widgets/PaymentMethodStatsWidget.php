<?php

namespace App\Filament\Resources\PaymentResource\Widgets;

use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PaymentMethodStatsWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $totalPayments = Payment::count();
        
        // حساب عدد كل طريقة دفع
        $cashCount = Payment::where('payment_method', 'cash')->count();
        $bankTransferCount = Payment::where('payment_method', 'bank_transfer')->count();
        $walletCount = Payment::where('payment_method', 'wallet')->count();
        $cliqCount = Payment::where('payment_method', 'cliq')->count();

        // حساب النسب المئوية
        $cashPercentage = $totalPayments > 0 ? round(($cashCount / $totalPayments) * 100, 1) : 0;
        $bankTransferPercentage = $totalPayments > 0 ? round(($bankTransferCount / $totalPayments) * 100, 1) : 0;
        $walletPercentage = $totalPayments > 0 ? round(($walletCount / $totalPayments) * 100, 1) : 0;
        $cliqPercentage = $totalPayments > 0 ? round(($cliqCount / $totalPayments) * 100, 1) : 0;

        // حساب قيم المبالغ لكل طريقة دفع
        $cashAmount = Payment::where('payment_method', 'cash')->sum('amount');
        $bankTransferAmount = Payment::where('payment_method', 'bank_transfer')->sum('amount');
        $walletAmount = Payment::where('payment_method', 'wallet')->sum('amount');
        $cliqAmount = Payment::where('payment_method', 'cliq')->sum('amount');

        return [
            // المدفوعات نقداً
            Stat::make('💵 مدفوعات نقدية', number_format($cashCount))
                ->description("نسبة {$cashPercentage}% - قيمة " . number_format($cashAmount, 2) . " JOD")
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart([5, 10, 15, 20, $cashCount])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20',
                ]),

            // التحويلات البنكية
            Stat::make('🏦 تحويلات بنكية', number_format($bankTransferCount))
                ->description("نسبة {$bankTransferPercentage}% - قيمة " . number_format($bankTransferAmount, 2) . " JOD")
                ->descriptionIcon('heroicon-m-building-library')
                ->color('primary')
                ->chart([3, 6, 9, 12, $bankTransferCount])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20',
                ]),

            // المحافظ الإلكترونية
            Stat::make('📱 محافظ إلكترونية', number_format($walletCount))
                ->description("نسبة {$walletPercentage}% - قيمة " . number_format($walletAmount, 2) . " JOD")
                ->descriptionIcon('heroicon-m-device-phone-mobile')
                ->color('warning')
                ->chart([2, 4, 6, 8, $walletCount])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20',
                ]),

            // مدفوعات كليك
            Stat::make('⚡ مدفوعات كليك', number_format($cliqCount))
                ->description("نسبة {$cliqPercentage}% - قيمة " . number_format($cliqAmount, 2) . " JOD")
                ->descriptionIcon('heroicon-m-bolt')
                ->color('info')
                ->chart([1, 3, 5, 7, $cliqCount])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-cyan-50 to-cyan-100 dark:from-cyan-900/20 dark:to-cyan-800/20',
                ]),
        ];
    }

    /**
     * تحديث الويدجت كل دقيقة
     */
    protected static ?string $pollingInterval = '60s';
}

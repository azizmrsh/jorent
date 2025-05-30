<?php

namespace App\Filament\Resources\PaymentResource\Widgets;

use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class PaymentCollectionRateWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        // إجمالي المدفوعات
        $totalPayments = Payment::count();
        
        // المدفوعات المكتملة
        $completedPayments = Payment::where('payment_status', 'completed')->count();
        
        // المدفوعات قيد الانتظار
        $pendingPayments = Payment::where('payment_status', 'pending')->count();
        
        // المدفوعات الفاشلة
        $failedPayments = Payment::where('payment_status', 'failed')->count();

        // حساب معدل التحصيل
        $collectionRate = $totalPayments > 0 ? 
            round(($completedPayments / $totalPayments) * 100, 1) : 0;

        // المدفوعات المتأخرة (قيد الانتظار لأكثر من 3 أيام)
        $overduePayments = Payment::where('payment_status', 'pending')
            ->where('created_at', '<', Carbon::now()->subDays(3))
            ->count();

        // معدل الفشل
        $failureRate = $totalPayments > 0 ? 
            round(($failedPayments / $totalPayments) * 100, 1) : 0;

        // المدفوعات اليوم
        $todayPayments = Payment::whereDate('created_at', Carbon::today())
            ->where('payment_status', 'completed')
            ->count();

        // قيمة المدفوعات المعلقة
        $pendingValue = Payment::where('payment_status', 'pending')
            ->sum('amount');

        return [
            Stat::make('معدل التحصيل', $collectionRate . '%')
                ->description("من إجمالي {$totalPayments} دفعة")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($collectionRate >= 80 ? 'success' : ($collectionRate >= 60 ? 'warning' : 'danger'))
                ->extraAttributes([
                    'style' => $collectionRate >= 80 ? 
                        'background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;' :
                        ($collectionRate >= 60 ? 
                            'background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); color: #1f2937;' :
                            'background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;'
                        )
                ]),

            Stat::make('المدفوعات المعلقة', $pendingPayments)
                ->description('قيد الانتظار: ' . number_format($pendingValue, 2))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;'
                ]),

            Stat::make('المدفوعات المتأخرة', $overduePayments)
                ->description('معلقة لأكثر من 3 أيام')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($overduePayments > 0 ? 'danger' : 'success')
                ->extraAttributes([
                    'style' => $overduePayments > 0 ? 
                        'background: linear-gradient(135deg, #ff6b6b 0%, #ffa500 100%); color: white;' :
                        'background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #1f2937;'
                ]),

            Stat::make('معدل الفشل', $failureRate . '%')
                ->description("من إجمالي {$totalPayments} دفعة")
                ->descriptionIcon($failureRate > 10 ? 'heroicon-m-x-circle' : 'heroicon-m-shield-check')
                ->color($failureRate <= 5 ? 'success' : ($failureRate <= 10 ? 'warning' : 'danger'))
                ->extraAttributes([
                    'style' => $failureRate <= 5 ? 
                        'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;' :
                        ($failureRate <= 10 ? 
                            'background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); color: #1f2937;' :
                            'background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;'
                        )
                ]),
        ];
    }
}

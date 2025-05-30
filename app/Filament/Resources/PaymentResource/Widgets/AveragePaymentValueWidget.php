<?php

namespace App\Filament\Resources\PaymentResource\Widgets;

use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class AveragePaymentValueWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '60s';
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $completedPayments = Payment::where('payment_status', 'completed');

        // متوسط قيمة المدفوعات
        $averageAmount = $completedPayments->avg('amount') ?? 0;
        
        // أعلى دفعة
        $maxPayment = $completedPayments->max('amount') ?? 0;
        
        // أقل دفعة
        $minPayment = $completedPayments->min('amount') ?? 0;
        
        // الوسيط (المتوسط الوسطي)
        $medianPayment = $this->calculateMedian();

        // متوسط المدفوعات النقدية مقابل التحويلات
        $cashAverage = Payment::where('payment_status', 'completed')
            ->where('payment_method', 'cash')
            ->avg('amount') ?? 0;

        $transferAverage = Payment::where('payment_status', 'completed')
            ->where('payment_method', 'bank_transfer')
            ->avg('amount') ?? 0;

        // عدد المدفوعات فوق المتوسط
        $aboveAverageCount = Payment::where('payment_status', 'completed')
            ->where('amount', '>', $averageAmount)
            ->count();

        $totalCompleted = $completedPayments->count();
        $aboveAveragePercentage = $totalCompleted > 0 ? 
            round(($aboveAverageCount / $totalCompleted) * 100, 1) : 0;

        return [
            Stat::make('متوسط قيمة الدفعة', number_format($averageAmount, 2))
                ->description('من جميع المدفوعات المكتملة')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('primary')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;'
                ]),

            Stat::make('أعلى دفعة', number_format($maxPayment, 2))
                ->description('أكبر مبلغ تم دفعه')
                ->descriptionIcon('heroicon-m-arrow-up')
                ->color('success')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;'
                ]),

            Stat::make('أقل دفعة', number_format($minPayment, 2))
                ->description('أصغر مبلغ تم دفعه')
                ->descriptionIcon('heroicon-m-arrow-down')
                ->color('info')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #1f2937;'
                ]),

            Stat::make('القيمة الوسطية', number_format($medianPayment, 2))
                ->description('الوسيط الإحصائي للمدفوعات')
                ->descriptionIcon('heroicon-m-scale')
                ->color('warning')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;'
                ]),

            Stat::make('فوق المتوسط', $aboveAveragePercentage . '%')
                ->description("من إجمالي {$totalCompleted} دفعة مكتملة")
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($aboveAveragePercentage >= 50 ? 'success' : 'warning')
                ->extraAttributes([
                    'style' => $aboveAveragePercentage >= 50 ? 
                        'background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); color: #1f2937;' :
                        'background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;'
                ]),

            Stat::make('مقارنة الطرق', 
                'نقدي: ' . number_format($cashAverage, 0) . ' | تحويل: ' . number_format($transferAverage, 0)
            )
                ->description($cashAverage > $transferAverage ? 
                    'النقدي أعلى من التحويل' : 
                    'التحويل أعلى من النقدي'
                )
                ->descriptionIcon('heroicon-m-arrows-right-left')
                ->color('info')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;'
                ]),
        ];
    }

    private function calculateMedian(): float
    {
        $amounts = Payment::where('payment_status', 'completed')
            ->orderBy('amount')
            ->pluck('amount')
            ->toArray();

        if (empty($amounts)) {
            return 0;
        }

        $count = count($amounts);
        $middle = floor($count / 2);

        if ($count % 2 == 0) {
            return ($amounts[$middle - 1] + $amounts[$middle]) / 2;
        } else {
            return $amounts[$middle];
        }
    }
}

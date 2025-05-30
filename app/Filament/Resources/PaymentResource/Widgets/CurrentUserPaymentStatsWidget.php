<?php

namespace App\Filament\Resources\PaymentResource\Widgets;

use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class CurrentUserPaymentStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $user = auth()->user();
        
        if (!$user) {
            return [
                Stat::make('المستخدم', 'غير مسجل دخول')
                    ->description('يرجى تسجيل الدخول لعرض الإحصائيات')
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->color('danger')
                    ->extraAttributes([
                        'style' => 'background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;'
                    ]),
            ];
        }

        // إنشاء الاسم الكامل
        $nameParts = array_filter([$user->name, $user->midname, $user->lastname]);
        $fullName = implode(' ', $nameParts) ?: 'غير محدد';

        // إحصائيات المدفوعات التي استلمها هذا المستخدم
        $receivedPayments = Payment::where('receiver_name', $fullName)->where('payment_status', 'completed');
        $totalReceived = $receivedPayments->sum('amount');
        $countReceived = $receivedPayments->count();

        // إحصائيات هذا الشهر
        $thisMonthReceived = Payment::where('receiver_name', $fullName)
            ->where('payment_status', 'completed')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');

        $thisMonthCount = Payment::where('receiver_name', $fullName)
            ->where('payment_status', 'completed')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // متوسط قيمة المدفوعات المستلمة
        $averageReceived = $countReceived > 0 ? $totalReceived / $countReceived : 0;

        // آخر دفعة استلمها
        $lastPayment = Payment::where('receiver_name', $fullName)
            ->where('payment_status', 'completed')
            ->latest('payment_date')
            ->first();

        return [
            Stat::make('المستخدم الحالي', $fullName)
                ->description("دور: {$user->role} | حالة: {$user->status}")
                ->descriptionIcon('heroicon-m-user-circle')
                ->color('primary')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;'
                ]),

            Stat::make('إجمالي المدفوعات المستلمة', number_format($totalReceived, 2))
                ->description("من {$countReceived} دفعة")
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;'
                ]),

            Stat::make('مدفوعات هذا الشهر', number_format($thisMonthReceived, 2))
                ->description("من {$thisMonthCount} دفعة")
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('warning')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;'
                ]),

            Stat::make('متوسط قيمة الدفعة', number_format($averageReceived, 2))
                ->description('متوسط المدفوعات المستلمة')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('info')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #1f2937;'
                ]),

            Stat::make('آخر دفعة مستلمة', 
                $lastPayment ? number_format($lastPayment->amount, 2) . ' ' . $lastPayment->currency : 'لا توجد'
            )
                ->description($lastPayment ? 
                    'في ' . $lastPayment->payment_date->format('Y-m-d') : 
                    'لم يتم استلام أي دفعات بعد'
                )
                ->descriptionIcon($lastPayment ? 'heroicon-m-check-circle' : 'heroicon-m-x-circle')
                ->color($lastPayment ? 'success' : 'gray')
                ->extraAttributes([
                    'style' => $lastPayment ? 
                        'background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); color: #1f2937;' :
                        'background: linear-gradient(135deg, #d1d5db 0%, #9ca3af 100%); color: white;'
                ]),
        ];
    }
}

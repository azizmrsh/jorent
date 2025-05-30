<?php

namespace App\Filament\Resources\PaymentResource\Widgets;

use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonthlyPaymentTrendsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '60s';
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $currentMonth = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();
        $last3Months = Carbon::now()->subMonths(3);

        // إحصائيات هذا الشهر
        $thisMonthPayments = Payment::whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->where('payment_status', 'completed');

        $thisMonthTotal = $thisMonthPayments->sum('amount');
        $thisMonthCount = $thisMonthPayments->count();

        // إحصائيات الشهر الماضي
        $lastMonthPayments = Payment::whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->where('payment_status', 'completed');

        $lastMonthTotal = $lastMonthPayments->sum('amount');
        $lastMonthCount = $lastMonthPayments->count();

        // حساب النمو الشهري
        $valueGrowth = $lastMonthTotal > 0 ? 
            round((($thisMonthTotal - $lastMonthTotal) / $lastMonthTotal) * 100, 1) : 0;
        
        $countGrowth = $lastMonthCount > 0 ? 
            round((($thisMonthCount - $lastMonthCount) / $lastMonthCount) * 100, 1) : 0;

        // متوسط المدفوعات في آخر 3 أشهر
        $last3MonthsAvg = Payment::where('created_at', '>=', $last3Months)
            ->where('payment_status', 'completed')
            ->avg('amount');

        // أعلى يوم في الشهر الحالي
        $bestDayThisMonth = Payment::select(DB::raw('DAY(created_at) as day, SUM(amount) as total'))
            ->whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->where('payment_status', 'completed')
            ->groupBy(DB::raw('DAY(created_at)'))
            ->orderBy('total', 'desc')
            ->first();

        return [
            Stat::make('مدفوعات هذا الشهر', number_format($thisMonthTotal, 2))
                ->description("من {$thisMonthCount} دفعة")
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;'
                ]),

            Stat::make('النمو الشهري (القيمة)', 
                ($valueGrowth >= 0 ? '+' : '') . $valueGrowth . '%'
            )
                ->description($valueGrowth >= 0 ? 
                    'نمو إيجابي مقارنة بالشهر الماضي' : 
                    'انخفاض مقارنة بالشهر الماضي'
                )
                ->descriptionIcon($valueGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($valueGrowth >= 0 ? 'success' : 'danger')
                ->extraAttributes([
                    'style' => $valueGrowth >= 0 ? 
                        'background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;' :
                        'background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;'
                ]),

            Stat::make('النمو الشهري (العدد)', 
                ($countGrowth >= 0 ? '+' : '') . $countGrowth . '%'
            )
                ->description($countGrowth >= 0 ? 
                    'زيادة في عدد المدفوعات' : 
                    'انخفاض في عدد المدفوعات'
                )
                ->descriptionIcon($countGrowth >= 0 ? 'heroicon-m-arrow-up' : 'heroicon-m-arrow-down')
                ->color($countGrowth >= 0 ? 'success' : 'warning')
                ->extraAttributes([
                    'style' => $countGrowth >= 0 ? 
                        'background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #1f2937;' :
                        'background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); color: #1f2937;'
                ]),

            Stat::make('أفضل يوم هذا الشهر', 
                $bestDayThisMonth ? 
                    'يوم ' . $bestDayThisMonth->day . ' (' . number_format($bestDayThisMonth->total, 2) . ')' : 
                    'لا توجد مدفوعات'
            )
                ->description('أعلى إيرادات يومية في الشهر الحالي')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('warning')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;'
                ]),
        ];
    }
}

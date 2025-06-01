<?php

namespace App\Filament\Resources\PaymentResource\Widgets;

use App\Models\Payment;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class PaymentOverviewWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';
    
    protected function getStats(): array
    {
        $currentUser = Auth::user();
        $currentUserName = $currentUser?->name ?? 'Unknown User';
        
        // Get current date ranges
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        
        // 1. عدد الدفعات الكلي (Total Payments Count)
        $totalPaymentsCount = Payment::count();
        
        // 2. عدد الدفعات هذا الشهر (This Month Payments Count)
        $thisMonthPaymentsCount = Payment::where('payment_date', '>=', $thisMonth)->count();
        
        // 3. مجموع الدفعات هذا الشهر (This Month Total Amount)
        $thisMonthTotal = Payment::where('payment_date', '>=', $thisMonth)->sum('amount');
        
        // 4. مجموع الدفعات اليوم (Today Total Amount)
        $todayTotal = Payment::whereDate('payment_date', $today)->sum('amount');
        
        // 5. المبلغ المجمع هذا الشهر للمستخدم الحالي (User's This Month Collections)
        $userThisMonthTotal = Payment::where('payment_date', '>=', $thisMonth)
            ->where('receiver_name', $currentUserName)
            ->sum('amount');
        
        // 6. المبلغ المجمع اليوم للمستخدم الحالي (User's Today Collections)
        $userTodayTotal = Payment::whereDate('payment_date', $today)
            ->where('receiver_name', $currentUserName)
            ->sum('amount');
        
        // 7. عدد الدفعات التي جمعها المستخدم الحالي (User's Total Collections Count)
        $userTotalCollections = Payment::where('receiver_name', $currentUserName)->count();
        
        // 8. متوسط الدفعة (Average Payment Amount)
        $averagePayment = $totalPaymentsCount > 0 ? Payment::avg('amount') : 0;
        
        return [
            // الصف الأول (First Row)
            Stat::make('إجمالي الدفعات', $totalPaymentsCount)
                ->description('عدد جميع الدفعات في النظام')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),
                
            Stat::make('دفعات هذا الشهر', $thisMonthPaymentsCount)
                ->description('عدد الدفعات المستلمة هذا الشهر')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info')
                ->chart([3, 5, 7, 8, 6, 9, 10]),
                
            Stat::make('مجموع هذا الشهر', number_format($thisMonthTotal, 2) . ' JOD')
                ->description('إجمالي المبالغ المستلمة هذا الشهر')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart([5, 7, 9, 8, 6, 10, 12]),
                
            Stat::make('مجموع اليوم', number_format($todayTotal, 2) . ' JOD')
                ->description('إجمالي المبالغ المستلمة اليوم')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart([2, 4, 6, 8, 5, 7, 9]),
                
            // الصف الثاني (Second Row) - للمستخدم الحالي
            Stat::make('مجموعي هذا الشهر', number_format($userThisMonthTotal, 2) . ' JOD')
                ->description('المبلغ الذي جمعته هذا الشهر')
                ->descriptionIcon('heroicon-m-user')
                ->color('emerald')
                ->chart([4, 6, 8, 7, 5, 9, 11]),
                
            Stat::make('مجموعي اليوم', number_format($userTodayTotal, 2) . ' JOD')
                ->description('المبلغ الذي جمعته اليوم')
                ->descriptionIcon('heroicon-m-user-circle')
                ->color('cyan')
                ->chart([1, 3, 5, 7, 4, 6, 8]),
                
            Stat::make('دفعاتي الكلية', $userTotalCollections)
                ->description('عدد الدفعات التي استلمتها')
                ->descriptionIcon('heroicon-m-hand-raised')
                ->color('violet')
                ->chart([3, 4, 6, 8, 5, 7, 9, 6]),
                
            Stat::make('متوسط الدفعة', number_format($averagePayment, 2) . ' JOD')
                ->description('متوسط قيمة الدفعة الواحدة')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('amber')
                ->chart([6, 8, 5, 9, 7, 6, 8, 10]),
        ];
    }
    
    protected function getColumns(): int
    {
        return 4; // 4 widgets في كل صف
    }
}

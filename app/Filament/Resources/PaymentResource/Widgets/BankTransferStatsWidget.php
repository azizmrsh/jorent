<?php

namespace App\Filament\Resources\PaymentResource\Widgets;

use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class BankTransferStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '45s';
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        // إحصائيات التحويلات البنكية
        $bankTransfers = Payment::where('payment_method', 'bank_transfer');
        $completedTransfers = $bankTransfers->where('payment_status', 'completed');
        
        $totalTransfers = $bankTransfers->count();
        $completedTransfersCount = $completedTransfers->count();
        $transfersValue = $completedTransfers->sum('amount');
        $averageTransferValue = $completedTransfers->avg('amount') ?? 0;

        // معدل نجاح التحويلات البنكية
        $transferSuccessRate = $totalTransfers > 0 ? 
            round(($completedTransfersCount / $totalTransfers) * 100, 1) : 0;

        // البنوك الأكثر استخداماً (من bank_name)
        $topBanks = Payment::where('payment_method', 'bank_transfer')
            ->where('payment_status', 'completed')
            ->whereNotNull('bank_name')
            ->select('bank_name', DB::raw('COUNT(*) as count, SUM(amount) as total'))
            ->groupBy('bank_name')
            ->orderBy('count', 'desc')
            ->limit(3)
            ->get();

        $mostUsedBank = $topBanks->first();

        // التحويلات حسب الوقت (صباحاً مقابل مساءً)
        $morningTransfers = Payment::where('payment_method', 'bank_transfer')
            ->where('payment_status', 'completed')
            ->whereTime('created_at', '>=', '06:00:00')
            ->whereTime('created_at', '<', '12:00:00')
            ->count();

        $eveningTransfers = Payment::where('payment_method', 'bank_transfer')
            ->where('payment_status', 'completed')
            ->whereTime('created_at', '>=', '18:00:00')
            ->whereTime('created_at', '<=', '23:59:59')
            ->count();

        // أكبر تحويل بنكي
        $largestTransfer = $completedTransfers->max('amount') ?? 0;

        return [
            Stat::make('إجمالي التحويلات البنكية', number_format($transfersValue, 2))
                ->description("من {$completedTransfersCount} تحويل مكتمل")
                ->descriptionIcon('heroicon-m-building-library')
                ->color('primary')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;'
                ]),

            Stat::make('معدل نجاح التحويلات', $transferSuccessRate . '%')
                ->description("من إجمالي {$totalTransfers} تحويل")
                ->descriptionIcon($transferSuccessRate >= 90 ? 'heroicon-m-check-circle' : 'heroicon-m-exclamation-circle')
                ->color($transferSuccessRate >= 90 ? 'success' : ($transferSuccessRate >= 75 ? 'warning' : 'danger'))
                ->extraAttributes([
                    'style' => $transferSuccessRate >= 90 ? 
                        'background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;' :
                        ($transferSuccessRate >= 75 ? 
                            'background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); color: #1f2937;' :
                            'background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;'
                        )
                ]),

            Stat::make('متوسط التحويل البنكي', number_format($averageTransferValue, 2))
                ->description('متوسط قيمة التحويل الواحد')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('info')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #1f2937;'
                ]),

            Stat::make('البنك الأكثر استخداماً', 
                $mostUsedBank ? $mostUsedBank->bank_name : 'لا يوجد'
            )
                ->description($mostUsedBank ? 
                    "استُخدم في {$mostUsedBank->count} تحويل" : 
                    'لا توجد تحويلات مسجلة'
                )
                ->descriptionIcon('heroicon-m-star')
                ->color('warning')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;'
                ]),

            Stat::make('أكبر تحويل', number_format($largestTransfer, 2))
                ->description('أعلى قيمة تحويل بنكي')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('success')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;'
                ]),

            Stat::make('التوقيت المفضل', 
                $morningTransfers > $eveningTransfers ? 
                    "صباحاً ({$morningTransfers})" : 
                    "مساءً ({$eveningTransfers})"
            )
                ->description($morningTransfers > $eveningTransfers ? 
                    'أغلب التحويلات صباحاً' : 
                    'أغلب التحويلات مساءً'
                )
                ->descriptionIcon($morningTransfers > $eveningTransfers ? 'heroicon-m-sun' : 'heroicon-m-moon')
                ->color('info')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); color: #1f2937;'
                ]),
        ];
    }
}

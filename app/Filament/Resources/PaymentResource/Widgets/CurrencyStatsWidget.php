<?php

namespace App\Filament\Resources\PaymentResource\Widgets;

use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class CurrencyStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        // إحصائيات العملات
        $currencyStats = Payment::select('currency', DB::raw('COUNT(*) as count, SUM(amount) as total'))
            ->where('payment_status', 'completed')
            ->groupBy('currency')
            ->get()
            ->keyBy('currency');

        // العملة الأكثر استخداماً
        $mostUsedCurrency = $currencyStats->sortByDesc('count')->first();
        
        // أعلى مبلغ إجمالي بعملة واحدة
        $highestValueCurrency = $currencyStats->sortByDesc('total')->first();

        // إجمالي العملات المختلفة
        $totalCurrencies = $currencyStats->count();

        // أسماء العملات بالعربية
        $currencyNames = [
            'JOD' => 'دينار أردني',
            'USD' => 'دولار أمريكي', 
            'EUR' => 'يورو',
            'SAR' => 'ريال سعودي',
            'AED' => 'درهم إماراتي',
        ];

        return [
            Stat::make('العملات المفعلة', $totalCurrencies)
                ->description('عدد العملات المستخدمة في النظام')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('info')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;'
                ]),            Stat::make('العملة الأكثر استخداماً', 
                $mostUsedCurrency ? ($currencyNames[$mostUsedCurrency->currency] ?? $mostUsedCurrency->currency) : 'لا يوجد'
            )
                ->description($mostUsedCurrency ? 
                    "استُخدمت في {$mostUsedCurrency->count} دفعة" : 
                    'لا توجد مدفوعات مكتملة'
                )
                ->descriptionIcon('heroicon-m-star')
                ->color('warning')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;'
                ]),            Stat::make('أعلى قيمة إجمالية', 
                $highestValueCurrency ? number_format($highestValueCurrency->total, 2) : '0'
            )                ->description($currencyStats->get('JOD') ? 
                    "من {$currencyStats->get('JOD')->count} دفعة" : 
                    'لا توجد مدفوعات'
                )
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;'
                ]),

            // تفاصيل الدينار الأردني (العملة الأساسية)
            Stat::make('المدفوعات بالدينار الأردني',
                $currencyStats->get('JOD') ? number_format($currencyStats->get('JOD')->total, 2) . ' JOD' : '0 JOD'
            )
                ->description($currencyStats->get('JOD') ? 
                    "من {$currencyStats->get('JOD')->count} دفعة" : 
                    'لا توجد مدفوعات'
                )
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #1f2937;'
                ]),
        ];
    }
}

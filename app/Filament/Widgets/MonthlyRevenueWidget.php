<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use App\Models\Contract;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class MonthlyRevenueWidget extends ChartWidget
{
    protected static ?string $heading = 'الإيرادات الشهرية';
    protected static ?int $sort = 8;
    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        // الحصول على بيانات 12 شهر الماضية
        $months = collect(range(0, 11))->map(function ($month) {
            $date = Carbon::now()->subMonths($month);
            
            // الحصول على مجموع الدفعات لكل شهر
            $revenue = Payment::whereMonth('payment_date', $date->month)
                ->whereYear('payment_date', $date->year)
                ->where('status', 'completed')
                ->sum('amount');
                
            // الحصول على مجموع الإيجارات المتوقعة من العقود النشطة
            $expected = Contract::where('status', 'active')
                ->whereMonth('due_date', $date->month)
                ->whereYear('due_date', $date->year)
                ->sum('rent_amount');
            
            return [
                'month' => $date->format('M Y'),
                'revenue' => $revenue,
                'expected' => $expected,
            ];
        })->reverse()->values();

        return [
            'datasets' => [
                [
                    'label' => 'الإيرادات المحصلة',
                    'data' => $months->pluck('revenue')->toArray(),
                    'backgroundColor' => 'rgba(34, 197, 94, 0.6)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'الإيرادات المتوقعة',
                    'data' => $months->pluck('expected')->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 1,
                    'borderDash' => [5, 5],
                ],
            ],
            'labels' => $months->pluck('month')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

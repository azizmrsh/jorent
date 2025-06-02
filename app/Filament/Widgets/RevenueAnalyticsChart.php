<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use App\Models\Contract1;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RevenueAnalyticsChart extends ChartWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    
    protected function getHeading(): string
    {
        return '📈 Revenue Analytics - Last 6 Months';
    }
    
    protected function getData(): array
    {
        $months = [];
        $paymentsData = [];
        $contractsData = [];
        
        // الحصول على آخر 6 أشهر
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M Y');
            $months[] = $monthName;
            
            // إجمالي المدفوعات للشهر
            $monthlyPayments = Payment::whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->sum('amount');
            $paymentsData[] = round($monthlyPayments, 2);
            
            // إجمالي الإيرادات المتوقعة من العقود النشطة
            $monthlyContracts = Contract1::where('status', 'active')
                ->where('start_date', '<=', $date->endOfMonth())
                ->where(function($query) use ($date) {
                    $query->where('end_date', '>=', $date->startOfMonth())
                          ->orWhereNull('end_date');
                })
                ->sum('rent_amount');
            $contractsData[] = round($monthlyContracts, 2);
        }
        
        return [
            'datasets' => [
                [
                    'label' => '💰 Actual Payments (JOD)',
                    'data' => $paymentsData,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'borderColor' => 'rgba(34, 197, 94, 1)',
                    'borderWidth' => 3,
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => '📋 Expected Revenue (JOD)',
                    'data' => $contractsData,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                    'borderWidth' => 3,
                    'fill' => true,
                    'tension' => 0.4,
                    'borderDash' => [5, 5],
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
    
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Amount (JOD)',
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Month',
                    ],
                ],
            ],
            'interaction' => [
                'mode' => 'nearest',
                'axis' => 'x',
                'intersect' => false,
            ],
        ];
    }
}

<?php

namespace App\Filament\Resources\PaymentResource\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class PaymentsMethodsChart extends ChartWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    
    public function getHeading(): string
    {
        return '💳 Payment Methods Distribution';
    }
    
    public ?string $filter = 'thisMonth';
    
    protected function getFilters(): ?array
    {
        return [
            'thisMonth' => 'This Month',
            'lastMonth' => 'Last Month',
            'thisYear' => 'This Year',
            'allTime' => 'All Time',
        ];
    }

    protected function getData(): array
    {
        $query = Payment::where('status', 'paid');
        
        switch ($this->filter) {
            case 'thisMonth':
                $query->where('payment_date', '>=', Carbon::now()->startOfMonth());
                break;
            case 'lastMonth':
                $query->where('payment_date', '>=', Carbon::now()->subMonth()->startOfMonth())
                      ->where('payment_date', '<', Carbon::now()->startOfMonth());
                break;
            case 'thisYear':
                $query->where('payment_date', '>=', Carbon::now()->startOfYear());
                break;
            // allTime - no additional filter
        }

        // حساب المدفوعات حسب طريقة الدفع
        $cashPayments = (clone $query)->where('payment_method', 'cash')->count();
        $bankPayments = (clone $query)->where('payment_method', 'bank_transfer')->count();
        $onlinePayments = (clone $query)->where('payment_method', 'online')->count();
        $checkPayments = (clone $query)->where('payment_method', 'check')->count();
        $otherPayments = (clone $query)->whereNotIn('payment_method', ['cash', 'bank_transfer', 'online', 'check'])->count();

        // حساب المبالغ حسب طريقة الدفع
        $cashAmount = (clone $query)->where('payment_method', 'cash')->sum('amount');
        $bankAmount = (clone $query)->where('payment_method', 'bank_transfer')->sum('amount');
        $onlineAmount = (clone $query)->where('payment_method', 'online')->sum('amount');
        $checkAmount = (clone $query)->where('payment_method', 'check')->sum('amount');
        $otherAmount = (clone $query)->whereNotIn('payment_method', ['cash', 'bank_transfer', 'online', 'check'])->sum('amount');

        return [
            'datasets' => [
                [
                    'label' => 'Number of Payments',
                    'data' => [$cashPayments, $bankPayments, $onlinePayments, $checkPayments, $otherPayments],
                    'backgroundColor' => [
                        'rgba(34, 197, 94, 0.8)',   // Green for cash
                        'rgba(59, 130, 246, 0.8)',  // Blue for bank
                        'rgba(168, 85, 247, 0.8)',  // Purple for online
                        'rgba(245, 158, 11, 0.8)',  // Orange for check
                        'rgba(156, 163, 175, 0.8)', // Gray for others
                    ],
                    'borderColor' => [
                        'rgb(34, 197, 94)',
                        'rgb(59, 130, 246)',
                        'rgb(168, 85, 247)',
                        'rgb(245, 158, 11)',
                        'rgb(156, 163, 175)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => [
                'Cash (' . number_format($cashAmount) . '$)',
                'Bank Transfer (' . number_format($bankAmount) . '$)',
                'Online (' . number_format($onlineAmount) . '$)',
                'Check (' . number_format($checkAmount) . '$)',
                'Other (' . number_format($otherAmount) . '$)',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
    
    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 20,
                    ]
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ": " + context.parsed + " payments (" + percentage + "%)";
                        }'
                    ]
                ],
            ],
        ];
    }
}

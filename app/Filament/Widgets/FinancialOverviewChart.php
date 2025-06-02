<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use App\Models\Contract1;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialOverviewChart extends ChartWidget
{
    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 'full';
    
    public function getHeading(): string
    {
        return '💰 Financial Overview - Payment Methods & Trends';
    }
    
    protected function getData(): array
    {
        // إحصائيات طرق الدفع للشهر الحالي
        $paymentMethods = Payment::select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
            ->where('payment_date', '>=', Carbon::now()->startOfMonth())
            ->groupBy('payment_method')
            ->get();
        
        $methodLabels = $paymentMethods->map(function ($item) {
            return match($item->payment_method) {
                'cash' => '💵 Cash',
                'bank_transfer' => '🏦 Bank Transfer',
                'wallet' => '📱 Digital Wallet',
                'cliq' => '⚡ CliQ',
                default => ucfirst($item->payment_method),
            };
        })->toArray();
        
        $methodCounts = $paymentMethods->pluck('count')->toArray();
        $methodAmounts = $paymentMethods->pluck('total')->toArray();
        
        return [
            'datasets' => [
                [
                    'label' => 'Number of Transactions',
                    'data' => $methodCounts,
                    'backgroundColor' => [
                        'rgba(34, 197, 94, 0.8)',    // Green for cash
                        'rgba(59, 130, 246, 0.8)',   // Blue for bank transfer
                        'rgba(251, 191, 36, 0.8)',   // Yellow for wallet
                        'rgba(168, 85, 247, 0.8)',   // Purple for cliq
                    ],
                    'borderColor' => [
                        'rgba(34, 197, 94, 1)',
                        'rgba(59, 130, 246, 1)',
                        'rgba(251, 191, 36, 1)',
                        'rgba(168, 85, 247, 1)',
                    ],
                    'borderWidth' => 2,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Total Amount (JOD)',
                    'data' => $methodAmounts,
                    'type' => 'line',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'borderColor' => 'rgba(239, 68, 68, 1)',
                    'borderWidth' => 3,
                    'fill' => false,
                    'tension' => 0.4,
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $methodLabels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
    
    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'interaction' => [
                'mode' => 'index',
                'intersect' => false,
            ],
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'title' => [
                        'display' => true,
                        'text' => 'Number of Transactions',
                    ],
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'title' => [
                        'display' => true,
                        'text' => 'Amount (JOD)',
                    ],
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                    'callbacks' => [
                        'label' => 'function(context) {
                            let label = context.dataset.label || "";
                            if (label) {
                                label += ": ";
                            }
                            if (context.dataset.label === "Total Amount (JOD)") {
                                label += "JOD " + context.parsed.y.toLocaleString();
                            } else {
                                label += context.parsed.y;
                            }
                            return label;
                        }',
                    ],
                ],
            ],
        ];
    }
}

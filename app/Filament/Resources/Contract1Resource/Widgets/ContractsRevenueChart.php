<?php

namespace App\Filament\Resources\Contract1Resource\Widgets;

use App\Models\Contract1;
use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class ContractsRevenueChart extends ChartWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    
    public function getHeading(): string
    {
        return '💰 Contracts Revenue Analysis';
    }
    
    public ?string $filter = '6months';
    
    protected function getFilters(): ?array
    {
        return [
            '3months' => 'Last 3 months',
            '6months' => 'Last 6 months',
            '12months' => 'Last 12 months',
        ];
    }

    protected function getData(): array
    {
        $months = match($this->filter) {
            '3months' => 3,
            '12months' => 12,
            default => 6,
        };

        $labels = [];
        $expectedRevenue = [];
        $actualPayments = [];
        $newContracts = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();
            
            // الإيرادات المتوقعة (من العقود النشطة)
            $expected = Contract1::where('status', 'active')
                               ->where('start_date', '<=', $monthEnd)
                               ->where(function ($query) use ($monthStart) {
                                   $query->whereNull('end_date')
                                         ->orWhere('end_date', '>=', $monthStart);
                               })
                               ->sum('rent_amount');
                               
            // المدفوعات الفعلية
            $actual = Payment::where('payment_date', '>=', $monthStart)
                           ->where('payment_date', '<=', $monthEnd)
                           ->where('status', 'paid')
                           ->sum('amount');
                           
            // العقود الجديدة
            $newContractsCount = Contract1::where('created_at', '>=', $monthStart)
                                        ->where('created_at', '<=', $monthEnd)
                                        ->count();
            
            $labels[] = $date->format('M Y');
            $expectedRevenue[] = round($expected, 2);
            $actualPayments[] = round($actual, 2);
            $newContracts[] = $newContractsCount;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Expected Revenue ($)',
                    'type' => 'line',
                    'data' => $expectedRevenue,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'tension' => 0.4,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Actual Payments ($)',
                    'type' => 'bar',
                    'data' => $actualPayments,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.8)',
                    'borderColor' => 'rgb(16, 185, 129)',
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'New Contracts',
                    'type' => 'line',
                    'data' => $newContracts,
                    'borderColor' => 'rgb(245, 158, 11)',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'tension' => 0.4,
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $labels,
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
                        'text' => 'Amount ($)'
                    ]
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'title' => [
                        'display' => true,
                        'text' => 'Number of Contracts'
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
                            if (context.dataset.yAxisID === "y") {
                                label += "$" + context.parsed.y.toLocaleString();
                            } else {
                                label += context.parsed.y;
                            }
                            return label;
                        }'
                    ]
                ],
            ],
        ];
    }
}

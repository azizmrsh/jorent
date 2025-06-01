<?php

namespace App\Filament\Resources\PaymentResource\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;

class PaymentMethodsWidget extends ChartWidget
{
    protected static ?string $heading = 'Payment Methods Distribution';
    
    protected static ?string $description = 'Breakdown of payment methods used by tenants';
    
    protected static ?int $sort = 2;
    
    protected static ?string $pollingInterval = '30s';
    
    protected function getData(): array
    {
        // Get payment method counts and amounts
        $paymentMethods = Payment::selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total_amount')
            ->groupBy('payment_method')
            ->get();
            
        $labels = [];
        $data = [];
        $colors = [];
        
        // Define colors and labels for each payment method
        $methodConfig = [
            'cash' => ['label' => '💵 Cash', 'color' => '#10B981'], // Green
            'bank_transfer' => ['label' => '🏦 Bank Transfer', 'color' => '#3B82F6'], // Blue
            'wallet' => ['label' => '📱 Digital Wallet', 'color' => '#F59E0B'], // Amber
            'cliq' => ['label' => '⚡ CliQ', 'color' => '#EF4444'], // Red
        ];
        
        foreach ($paymentMethods as $method) {
            $config = $methodConfig[$method->payment_method] ?? [
                'label' => ucfirst($method->payment_method), 
                'color' => '#6B7280'
            ];
            
            $labels[] = $config['label'] . ' (' . $method->count . ')';
            $data[] = floatval($method->total_amount);
            $colors[] = $config['color'];
        }
        
        return [
            'datasets' => [
                [
                    'label' => 'Amount (JOD)',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderColor' => $colors,
                    'borderWidth' => 2,
                ]
            ],
            'labels' => $labels,
        ];
    }
    
    protected function getType(): string
    {
        return 'doughnut';
    }
    
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            return context.label + ": " + context.parsed + " JOD";
                        }'
                    ]
                ]
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];
    }
}

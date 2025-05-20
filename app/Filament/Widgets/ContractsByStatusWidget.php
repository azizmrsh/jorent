<?php

namespace App\Filament\Widgets;

use App\Models\Contract;
use Filament\Widgets\ChartWidget;

class ContractsByStatusWidget extends ChartWidget
{
    protected static ?string $heading = 'Contracts by Status';
    protected static ?int $sort = 4;
    
    protected function getData(): array
    {
        $contracts = Contract::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        
        // Ensure all statuses have a value even if zero
        $statuses = [
            'active' => $contracts['active'] ?? 0,
            'pending' => $contracts['pending'] ?? 0,
            'completed' => $contracts['completed'] ?? 0,
            'terminated' => $contracts['terminated'] ?? 0,
        ];
        
        return [
            'datasets' => [
                [
                    'label' => 'Contracts by Status',
                    'data' => array_values($statuses),
                    'backgroundColor' => [
                        'rgba(34, 197, 94, 0.7)', // green for active
                        'rgba(234, 179, 8, 0.7)',  // yellow for pending
                        'rgba(107, 114, 128, 0.7)', // gray for completed
                        'rgba(239, 68, 68, 0.7)',  // red for terminated
                    ],
                ],
            ],
            'labels' => array_keys($statuses),
        ];
    }
    
    protected function getType(): string
    {
        return 'pie';
    }
}

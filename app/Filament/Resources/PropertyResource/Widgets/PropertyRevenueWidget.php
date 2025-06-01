<?php

namespace App\Filament\Resources\PropertyResource\Widgets;

use App\Models\Property;
use App\Models\Unit;
use App\Models\Contract1;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PropertyRevenueWidget extends BaseWidget
{
    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 3;

    protected function getStats(): array
    {
        // Calculate monthly revenue from active contracts
        $monthlyRevenue = Contract1::where('status', 'active')
            ->whereHas('unit.property')
            ->sum('monthly_rent');
        
        // Calculate annual revenue potential
        $annualRevenue = $monthlyRevenue * 12;
        
        // Count properties generating revenue
        $revenueGeneratingProperties = Property::whereHas('units.contracts', function ($query) {
            $query->where('status', 'active');
        })->count();

        return [
            Stat::make('Monthly Revenue', '$' . number_format($monthlyRevenue, 0))
                ->description("Annual: $" . number_format($annualRevenue, 0))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20',
                ]),
        ];
    }
}

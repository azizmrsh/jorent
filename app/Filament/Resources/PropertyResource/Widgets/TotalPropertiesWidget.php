<?php

namespace App\Filament\Resources\PropertyResource\Widgets;

use App\Models\Property;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalPropertiesWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 1;

    protected function getStats(): array
    {
        $totalProperties = Property::count();
        $recentProperties = Property::where('created_at', '>=', now()->subDays(30))->count();
        
        // Calculate growth percentage
        $thisMonthGrowth = $recentProperties > 0 ? round(($recentProperties / max($totalProperties - $recentProperties, 1)) * 100, 1) : 0;

        return [
            Stat::make('Total Properties', number_format($totalProperties))
                ->description("Recent: {$recentProperties} (+{$thisMonthGrowth}%)")
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('info')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-cyan-50 to-cyan-100 dark:from-cyan-900/20 dark:to-cyan-800/20',
                ]),
        ];
    }
}

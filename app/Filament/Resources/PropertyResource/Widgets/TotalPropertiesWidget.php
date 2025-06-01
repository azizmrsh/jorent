<?php

namespace App\Filament\Resources\PropertyResource\Widgets;

use App\Models\Property;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalPropertiesWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';    protected function getStats(): array
    {
        $totalProperties = Property::count();        return [
            Stat::make('Total Properties', number_format($totalProperties))
                ->description("Properties registered in system")
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20',
                ]),
        ];
    }
}

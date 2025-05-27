<?php

namespace App\Filament\Resources\PropertyResource\Widgets;

use App\Models\Property;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalPropertiesWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $totalProperties = Property::count();
        $recentProperties = Property::where('created_at', '>=', now()->subDays(30))->count();
        $thisMonthGrowth = $recentProperties > 0 ? round(($recentProperties / max($totalProperties - $recentProperties, 1)) * 100, 1) : 0;

        return [
            Stat::make('إجمالي العقارات', number_format($totalProperties))
                ->description("العقارات المسجلة في النظام")
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20',
                ]),

            Stat::make('العقارات الجديدة', number_format($recentProperties))
                ->description("خلال آخر 30 يوم")
                ->descriptionIcon('heroicon-m-plus-circle')
                ->color('success')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20',
                ]),

            Stat::make('معدل النمو الشهري', $thisMonthGrowth . '%')
                ->description($thisMonthGrowth > 0 ? "زيادة في العقارات" : "لا توجد زيادة")
                ->descriptionIcon($thisMonthGrowth > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-minus')
                ->color($thisMonthGrowth > 0 ? 'success' : 'gray')
                ->extraAttributes([
                    'class' => $thisMonthGrowth > 0 
                        ? 'bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20'
                        : 'bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900/20 dark:to-gray-800/20',
                ]),
        ];
    }
}

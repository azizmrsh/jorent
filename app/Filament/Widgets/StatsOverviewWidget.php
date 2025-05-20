<?php

namespace App\Filament\Widgets;

use App\Models\Contract;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\Property;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected function getStats(): array
    {
        return [
            Stat::make('Total Properties', Property::count())
                ->description('Total properties managed in the system')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('success'),
                
            Stat::make('Total Units', Unit::count())
                ->description('Total rental units available')
                ->descriptionIcon('heroicon-m-home')
                ->color('info'),
                
            Stat::make('Total Tenants', Tenant::count())
                ->description('Active tenant accounts')
                ->descriptionIcon('heroicon-m-users')
                ->color('warning'),
                
            Stat::make('Active Contracts', Contract::where('status', 'active')->count())
                ->description('Currently active rental contracts')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),
        ];
    }
}

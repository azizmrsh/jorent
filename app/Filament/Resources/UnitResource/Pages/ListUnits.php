<?php

namespace App\Filament\Resources\UnitResource\Pages;

use App\Filament\Resources\UnitResource;
use App\Filament\Resources\UnitResource\Widgets\TotalUnitsWidget;
use App\Filament\Resources\UnitResource\Widgets\UnitStatusStatsWidget;
use App\Filament\Resources\UnitResource\Widgets\AvailableUnitsWidget;
use App\Filament\Resources\UnitResource\Widgets\TotalRevenueWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUnits extends ListRecords
{
    protected static string $resource = UnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // 📊 4 Simple Widgets on Same Line
            TotalUnitsWidget::class,              // Total units count
            UnitStatusStatsWidget::class,         // Occupancy rate
            AvailableUnitsWidget::class,          // Available units count
            TotalRevenueWidget::class,            // Total revenue potential
        ];
    }
}

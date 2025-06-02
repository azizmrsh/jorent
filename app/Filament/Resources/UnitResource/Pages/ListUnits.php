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
            // First row of widgets
            TotalUnitsWidget::class,              // Total units count
            UnitStatusStatsWidget::class,         // Occupancy rate
            AvailableUnitsWidget::class,          // Available units count
            TotalRevenueWidget::class,            // Total revenue potential

            // Second row of widgets
            WidgetOne::class,                     // Placeholder for a new widget
            WidgetTwo::class,                     // Placeholder for a new widget
            WidgetThree::class,                   // Placeholder for a new widget
            WidgetFour::class,                    // Placeholder for a new widget
        ];
    }
}

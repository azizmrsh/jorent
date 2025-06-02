<?php

namespace App\Filament\Resources\UnitResource\Pages;

use App\Filament\Resources\UnitResource;
use App\Filament\Resources\UnitResource\Widgets\TotalUnitsWidget;
use App\Filament\Resources\UnitResource\Widgets\UnitTypeStatsWidget;
use App\Filament\Resources\UnitResource\Widgets\UnitStatusStatsWidget;
use App\Filament\Resources\UnitResource\Widgets\AvailableUnitsWidget;
use App\Filament\Resources\UnitResource\Widgets\TotalRevenueWidget;
use App\Filament\Resources\UnitResource\Widgets\AveragePriceWidget;
use App\Filament\Resources\UnitResource\Widgets\PriceRangesWidget;
use App\Filament\Resources\UnitResource\Widgets\RentedUnitsRevenueWidget;
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
            // 📊 First Row - General Unit Statistics (4 widgets)
            TotalUnitsWidget::class,              // Total units count
            UnitTypeStatsWidget::class,           // Most common unit type
            UnitStatusStatsWidget::class,         // Occupancy rate
            AvailableUnitsWidget::class,          // Available units count
            
            // 💰 Second Row - Financial Statistics (4 widgets)
            TotalRevenueWidget::class,            // Total revenue potential
            AveragePriceWidget::class,            // Average rental price
            PriceRangesWidget::class,             // Price range (high/low)
            RentedUnitsRevenueWidget::class,      // Revenue from rented units
        ];
    }
}

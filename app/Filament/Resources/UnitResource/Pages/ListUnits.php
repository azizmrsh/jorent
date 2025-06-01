<?php

namespace App\Filament\Resources\UnitResource\Pages;

use App\Filament\Resources\UnitResource;
use App\Filament\Resources\UnitResource\Widgets\TotalUnitsWidget;
use App\Filament\Resources\UnitResource\Widgets\UnitTypeStatsWidget;
use App\Filament\Resources\UnitResource\Widgets\UnitStatusStatsWidget;
use App\Filament\Resources\UnitResource\Widgets\UnitPriceStatsWidget;
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
            // 📊 Total Units (count with recent additions)
            TotalUnitsWidget::class,
            
            // 🏘️ Unit Type Stats (Apartments, Villas, Offices, Shops combined)
            UnitTypeStatsWidget::class,
            
            // 📈 Unit Status Stats (Available, Rented, Maintenance combined)  
            UnitStatusStatsWidget::class,
            
            // 💰 Unit Price Statistics (Average price with revenue potential)
            UnitPriceStatsWidget::class,
        ];
    }
}

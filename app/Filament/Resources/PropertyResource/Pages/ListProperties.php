<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use App\Filament\Resources\PropertyResource;
use App\Filament\Resources\PropertyResource\Widgets\TotalPropertiesWidget;
use App\Filament\Resources\PropertyResource\Widgets\PropertyTypeStatsWidget;
use App\Filament\Resources\PropertyResource\Widgets\UsageTypeStatsWidget;
use App\Filament\Resources\PropertyResource\Widgets\UnitsCounterWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProperties extends ListRecords
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // 📊 Total Properties (total count only)
            TotalPropertiesWidget::class,
            
            // 🏘️ Property Type Stats (Buildings, Villas, Houses, Warehouses)
            PropertyTypeStatsWidget::class,
            
            // 🏢 Usage Type Stats (Residential, Commercial, Industrial)
            UsageTypeStatsWidget::class,
            
            // 🔢 Total Units Counter
            UnitsCounterWidget::class,
        ];
    }
}

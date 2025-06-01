<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use App\Filament\Resources\PropertyResource;
use App\Filament\Resources\PropertyResource\Widgets\TotalPropertiesWidget;
use App\Filament\Resources\PropertyResource\Widgets\PropertyTypeStatsWidget;
use App\Filament\Resources\PropertyResource\Widgets\UsageTypeStatsWidget;
use App\Filament\Resources\PropertyResource\Widgets\AvailablePropertiesWidget;
use App\Filament\Resources\PropertyResource\Widgets\TotalValueWidget;
use App\Filament\Resources\PropertyResource\Widgets\PropertyRevenueWidget;
use App\Filament\Resources\PropertyResource\Widgets\AveragePropertyValueWidget;
use App\Filament\Resources\PropertyResource\Widgets\MaintenanceCostsWidget;
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
            // ===== الصف الأول: الإحصائيات العامة =====
            // 📊 Total Properties (Sort: 1)
            TotalPropertiesWidget::class,
            
            // 🏘️ Most Common Property Type (Sort: 2)
            PropertyTypeStatsWidget::class,
            
            // 🏢 Primary Usage Type (Sort: 3)
            UsageTypeStatsWidget::class,
            
            // ✅ Available Properties (Sort: 4)
            AvailablePropertiesWidget::class,
            
            // ===== الصف الثاني: الإحصائيات المالية =====
            // 💰 Total Portfolio Value (Sort: 5)
            TotalValueWidget::class,
            
            // 💵 Monthly Revenue (Sort: 6)
            PropertyRevenueWidget::class,
            
            // 📈 Average Property Value (Sort: 7)
            AveragePropertyValueWidget::class,
            
            // 🔧 Maintenance Costs (Sort: 8)
            MaintenanceCostsWidget::class,
        ];
    }
}

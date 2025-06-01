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
            // 📊 إجمالي العقارات (العدد الكلي فقط)
            TotalPropertiesWidget::class,
            
            // 🏘️ أنواع العقارات مجمعة (مباني، فيلات، منازل، مستودعات)
            PropertyTypeStatsWidget::class,
            
            // 🏢 أنواع الاستخدام مجمعة (سكني، تجاري، صناعي)
            UsageTypeStatsWidget::class,
            
            // 🔢 عداد الوحدات وإحصائياتها
            UnitsCounterWidget::class,
        ];
    }
}

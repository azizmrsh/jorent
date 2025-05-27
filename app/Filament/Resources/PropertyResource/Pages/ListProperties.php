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
            // 📊 إجمالي العقارات ومعدل النمو
            TotalPropertiesWidget::class,
            
            // 🏘️ إحصائيات أنواع العقارات مع النسب المئوية
            PropertyTypeStatsWidget::class,
            
            // 🏢 إحصائيات نوع الاستخدام مع النسب المئوية
            UsageTypeStatsWidget::class,
            
            // 🔢 عداد الوحدات ومتوسط الوحدات لكل عقار
            UnitsCounterWidget::class,
        ];
    }
}

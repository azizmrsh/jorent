<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use App\Filament\Resources\PropertyResource;
use App\Filament\Resources\PropertyResource\Widgets\QuickStatsSection;
use App\Filament\Resources\PropertyResource\Widgets\PropertyTypesSection;
use App\Filament\Resources\PropertyResource\Widgets\ChartsAnalyticsSection;
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
            // 📊 القسم الأول: الإحصائيات السريعة (مفتوح افتراضياً)
            QuickStatsSection::class,
            
            // 🏘️ القسم الثاني: تفصيل أنواع العقارات (مطوي افتراضياً)
            PropertyTypesSection::class,
            
            // 📈 القسم الثالث: الرسوم البيانية والتحليلات (مطوي افتراضياً)
            ChartsAnalyticsSection::class,
        ];
    }
}

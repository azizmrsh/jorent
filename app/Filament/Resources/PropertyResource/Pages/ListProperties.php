<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use App\Filament\Resources\PropertyResource;
use App\Filament\Resources\PropertyResource\Widgets\PropertyStatsOverview;
use App\Filament\Resources\PropertyResource\Widgets\PropertyOverviewStats;
use App\Filament\Resources\PropertyResource\Widgets\PropertyQuickStats;
use App\Filament\Resources\PropertyResource\Widgets\PropertyTypesStats;
use App\Filament\Resources\PropertyResource\Widgets\PropertyTypeDistributionChart;
use App\Filament\Resources\PropertyResource\Widgets\PropertyMonthlyTrendsChart;
use App\Filament\Resources\PropertyResource\Widgets\PropertyAdvancedStats;
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
            // الصف الأول: إحصائيات سريعة (نوع الاستخدام)
            PropertyOverviewStats::class,
            
            // الصف الثاني: معلومات إضافية سريعة  
            PropertyQuickStats::class,
            
            // الصف الثالث: تفصيل أنواع العقارات
            PropertyTypesStats::class,
            
            // الصف الرابع: الرسوم البيانية (جنب بعض)
            PropertyTypeDistributionChart::class,
            PropertyMonthlyTrendsChart::class,
            
            // الصف الخامس: التحليلات المتقدمة
            PropertyAdvancedStats::class,
        ];
    }
}

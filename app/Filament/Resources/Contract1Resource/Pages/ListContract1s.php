<?php

namespace App\Filament\Resources\Contract1Resource\Pages;

use App\Filament\Resources\Contract1Resource;
use App\Filament\Resources\Contract1Resource\Widgets\TotalContractsWidget;
use App\Filament\Resources\Contract1Resource\Widgets\ContractStatusWidget;
use App\Filament\Resources\Contract1Resource\Widgets\RevenueStatsWidget;
use App\Filament\Resources\Contract1Resource\Widgets\ExpiringContractsWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContract1s extends ListRecords
{
    protected static string $resource = Contract1Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // 📊 Simple 4 Widgets Row
            TotalContractsWidget::class,         // إجمالي العقود
            ContractStatusWidget::class,         // حالة العقود
            RevenueStatsWidget::class,          // إحصائيات الإيرادات
            ExpiringContractsWidget::class,     // العقود المنتهية الصلاحية
        ];
    }
}

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
            // 📊 Total contracts and growth rate
            TotalContractsWidget::class,
            
            // 📈 Contract status distribution
            ContractStatusWidget::class,
            
            // 💰 Revenue analytics
            RevenueStatsWidget::class,
            
            // ⏰ Expiring contracts tracker
            ExpiringContractsWidget::class,
        ];
    }
}

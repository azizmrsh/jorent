<?php

namespace App\Filament\Resources\Contract1Resource\Pages;

use App\Filament\Resources\Contract1Resource;
use App\Filament\Resources\Contract1Resource\Widgets\TotalContractsWidget;
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
            TotalContractsWidget::class,
        ];
    }
}

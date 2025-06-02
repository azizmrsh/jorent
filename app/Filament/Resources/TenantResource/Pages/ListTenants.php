<?php

namespace App\Filament\Resources\TenantResource\Pages;

use App\Filament\Resources\TenantResource;
use App\Filament\Resources\TenantResource\Widgets\TotalTenantsWidget;
use App\Filament\Resources\TenantResource\Widgets\TenantStatusWidget;
use App\Filament\Resources\TenantResource\Widgets\DocumentVerificationWidget;
use App\Filament\Resources\TenantResource\Widgets\RecentTenantsWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTenants extends ListRecords
{
    protected static string $resource = TenantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TotalTenantsWidget::class,
            TenantStatusWidget::class,
            DocumentVerificationWidget::class,
            RecentTenantsWidget::class,
        ];
    }
}

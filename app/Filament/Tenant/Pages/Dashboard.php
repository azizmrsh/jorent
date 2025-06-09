<?php

namespace App\Filament\Tenant\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    
    protected static ?string $title = 'لوحة التحكم';
    
    protected static ?string $navigationLabel = 'الرئيسية';

    public function getWidgets(): array
    {
        return [
            \App\Filament\Tenant\Widgets\TenantStatsWidget::class,
            \App\Filament\Tenant\Widgets\MyContractsWidget::class,
            \App\Filament\Tenant\Widgets\MyPaymentsWidget::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return 12;
    }
}

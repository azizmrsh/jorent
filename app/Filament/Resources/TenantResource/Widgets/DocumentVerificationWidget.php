<?php

namespace App\Filament\Resources\TenantResource\Widgets;

use App\Models\Tenant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DocumentVerificationWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 3;

    protected function getStats(): array
    {
        $totalTenants = Tenant::count();
        $verifiedDocuments = Tenant::whereNotNull('document_type')
                                  ->whereNotNull('document_number')
                                  ->count();
        $verifiedPercentage = $totalTenants > 0 ? round(($verifiedDocuments / $totalTenants) * 100, 1) : 0;

        return [
            Stat::make('Verified Documents', number_format($verifiedDocuments))
                ->description("$verifiedPercentage% of all tenants")
                ->descriptionIcon('heroicon-m-document-check')
                ->color('success')
                ->chart([7, 8, 6, 9, 8, 7, 9, 8])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20',
                ]),
        ];
    }
}

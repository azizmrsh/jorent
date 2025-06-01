<?php

namespace App\Filament\Resources\TenantResource\Widgets;

use App\Models\Tenant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DocumentVerificationWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $totalTenants = Tenant::count();
        $verifiedDocuments = Tenant::whereNotNull('document_type')
                                  ->whereNotNull('document_number')
                                  ->count();
        $unverifiedDocuments = $totalTenants - $verifiedDocuments;
        
        $verifiedPercentage = $totalTenants > 0 ? round(($verifiedDocuments / $totalTenants) * 100, 1) : 0;
        
        // Document type distribution
        $passportCount = Tenant::where('document_type', 'passport')->count();
        $idCardCount = Tenant::where('document_type', 'id_card')->count();
        $driverLicenseCount = Tenant::where('document_type', 'driver_license')->count();
        $residencyPermitCount = Tenant::where('document_type', 'residency_permit')->count();

        return [
            Stat::make('Verified Documents', number_format($verifiedDocuments))
                ->description("$verifiedPercentage% of all tenants")
                ->descriptionIcon('heroicon-m-document-check')
                ->color('success')
                ->chart([7, 8, 6, 9, 8, 7, 9, 8])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20',
                ]),

            Stat::make('Unverified Documents', number_format($unverifiedDocuments))
                ->description("Documents pending verification")
                ->descriptionIcon('heroicon-m-document-x-mark')
                ->color('warning')
                ->chart([3, 2, 4, 1, 2, 3, 1, 2])
                ->extraAttributes([
                    'class' => 'bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20',
                ]),
        ];
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\Tenant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class TenantEmailVerificationStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';
    protected static bool $isLazy = false;
    
    protected function getStats(): array
    {
        // إجمالي المستأجرين
        $totalTenants = Tenant::count();
        
        // المستأجرين الذين لديهم بريد إلكتروني
        $tenantsWithEmail = Tenant::whereNotNull('email')
            ->where('email', '!=', '')
            ->count();
        
        // المستأجرين المؤكدين للبريد الإلكتروني
        $verifiedTenants = Tenant::whereNotNull('email_verified_at')->count();
        
        // المستأجرين غير المؤكدين الذين لديهم بريد إلكتروني
        $unverifiedTenants = Tenant::whereNotNull('email')
            ->where('email', '!=', '')
            ->whereNull('email_verified_at')
            ->count();
        
        // النسبة المئوية للتحقق
        $verificationRate = $tenantsWithEmail > 0 
            ? round(($verifiedTenants / $tenantsWithEmail) * 100, 1) 
            : 0;
        
        return [
            Stat::make('📊 إجمالي المستأجرين', $totalTenants)
                ->description('العدد الكلي للمستأجرين')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),
                
            Stat::make('📧 لديهم بريد إلكتروني', $tenantsWithEmail)
                ->description('المستأجرين الذين لديهم بريد إلكتروني')
                ->descriptionIcon('heroicon-m-envelope')
                ->color('info')
                ->chart([4, 3, 4, 5, 6, 3, 5, 4]),
                
            Stat::make('✅ البريد مؤكد', $verifiedTenants)
                ->description("معدل التحقق: {$verificationRate}%")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([2, 3, 3, 4, 5, 3, 4, 3]),
                
            Stat::make('❌ البريد غير مؤكد', $unverifiedTenants)
                ->description('يحتاجون لتأكيد البريد الإلكتروني')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger')
                ->chart([5, 4, 3, 2, 1, 2, 3, 4]),
        ];
    }
    
    protected function getColumns(): int
    {
        return 4;
    }
    
    public static function canView(): bool
    {
        return true;
    }
}

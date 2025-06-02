<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Tenant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EmailVerificationOverviewWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';
    protected static bool $isLazy = false;
    protected static ?int $sort = 1;
    
    protected function getStats(): array
    {
        // إحصائيات المستخدمين
        $totalUsers = User::count();
        $verifiedUsers = User::whereNotNull('email_verified_at')->count();
        $unverifiedUsers = $totalUsers - $verifiedUsers;
        $userVerificationRate = $totalUsers > 0 ? round(($verifiedUsers / $totalUsers) * 100, 1) : 0;
        
        // إحصائيات المستأجرين
        $totalTenants = Tenant::count();
        $tenantsWithEmail = Tenant::whereNotNull('email')->where('email', '!=', '')->count();
        $verifiedTenants = Tenant::whereNotNull('email_verified_at')->count();
        $tenantVerificationRate = $tenantsWithEmail > 0 ? round(($verifiedTenants / $tenantsWithEmail) * 100, 1) : 0;
        
        // إحصائيات عامة
        $totalEmailAddresses = $totalUsers + $tenantsWithEmail;
        $totalVerifiedEmails = $verifiedUsers + $verifiedTenants;
        $overallRate = $totalEmailAddresses > 0 ? round(($totalVerifiedEmails / $totalEmailAddresses) * 100, 1) : 0;
        
        return [
            Stat::make('👥 المستخدمين المؤكدين', $verifiedUsers . '/' . $totalUsers)
                ->description("معدل التحقق: {$userVerificationRate}%")
                ->descriptionIcon('heroicon-m-user-circle')
                ->color($userVerificationRate >= 80 ? 'success' : ($userVerificationRate >= 50 ? 'warning' : 'danger'))
                ->chart([2, 3, 3, 4, 5, 3, 4, 3])
                ->extraAttributes([
                    'title' => 'نسبة تحقق المستخدمين من البريد الإلكتروني'
                ]),
                
            Stat::make('🏠 المستأجرين المؤكدين', $verifiedTenants . '/' . $tenantsWithEmail)
                ->description("معدل التحقق: {$tenantVerificationRate}%")
                ->descriptionIcon('heroicon-m-home-modern')
                ->color($tenantVerificationRate >= 80 ? 'success' : ($tenantVerificationRate >= 50 ? 'warning' : 'danger'))
                ->chart([1, 2, 4, 3, 6, 4, 5, 3])
                ->extraAttributes([
                    'title' => 'نسبة تحقق المستأجرين من البريد الإلكتروني'
                ]),
                
            Stat::make('📊 المعدل العام', $totalVerifiedEmails . '/' . $totalEmailAddresses)
                ->description("نسبة التحقق الإجمالية: {$overallRate}%")
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($overallRate >= 80 ? 'success' : ($overallRate >= 50 ? 'warning' : 'danger'))
                ->chart([3, 4, 3, 5, 6, 4, 5, 4])
                ->extraAttributes([
                    'title' => 'نسبة التحقق الإجمالية لجميع المستخدمين والمستأجرين'
                ]),
                
            Stat::make('⚠️ يحتاجون تحقق', ($unverifiedUsers + ($tenantsWithEmail - $verifiedTenants)))
                ->description('إجمالي الحسابات غير المؤكدة')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger')
                ->chart([8, 6, 5, 4, 3, 4, 3, 2])
                ->extraAttributes([
                    'title' => 'عدد الحسابات التي تحتاج لتحقق البريد الإلكتروني'
                ]),
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

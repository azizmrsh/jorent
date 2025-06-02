<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EmailVerificationStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalUsers = User::count();
        $verifiedUsers = User::whereNotNull('email_verified_at')->count();
        $unverifiedUsers = User::whereNull('email_verified_at')->count();
        $verificationRate = $totalUsers > 0 ? round(($verifiedUsers / $totalUsers) * 100, 1) : 0;
        
        return [
            Stat::make('Total Users', $totalUsers)
                ->description('All registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Verified Emails', $verifiedUsers)
                ->description("Verification rate: {$verificationRate}%")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Unverified Emails', $unverifiedUsers)
                ->description('Require email verification')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color($unverifiedUsers > 0 ? 'warning' : 'success'),
        ];
    }
    
    protected static ?string $pollingInterval = '30s';
    
    protected static bool $isLazy = false;
}

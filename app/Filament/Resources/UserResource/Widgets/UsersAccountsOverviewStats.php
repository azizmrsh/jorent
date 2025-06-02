<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use App\Models\Acc;
use App\Models\Property;
use App\Models\Contract1;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class UsersAccountsOverviewStats extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';
    
    protected function getStats(): array
    {
        // إجمالي المستخدمين
        $totalUsers = User::count();
        $activeUsers = User::where('email_verified_at', '!=', null)->count();
        $newUsersThisMonth = User::where('created_at', '>=', Carbon::now()->startOfMonth())->count();
        
        // نمو المستخدمين
        $lastMonthUsers = User::where('created_at', '>=', Carbon::now()->subMonth()->startOfMonth())
                              ->where('created_at', '<', Carbon::now()->startOfMonth())->count();
        $usersGrowth = $lastMonthUsers > 0 ? 
            round((($newUsersThisMonth - $lastMonthUsers) / $lastMonthUsers) * 100, 1) : 0;

        // مديري الحسابات
        $totalAccountManagers = Acc::count();
        $activeAccountManagers = Acc::whereHas('properties')->count();
        
        // نشاط المستخدمين (آخر تسجيل دخول خلال 30 يوم)
        $recentlyActiveUsers = User::where('updated_at', '>=', Carbon::now()->subDays(30))->count();
        $activityRate = $totalUsers > 0 ? round(($recentlyActiveUsers / $totalUsers) * 100, 1) : 0;

        // توزيع الأدوار (افتراضي)
        $adminUsers = User::where('email', 'LIKE', '%admin%')->count();
        $regularUsers = $totalUsers - $adminUsers;

        // المستخدمين بدون تفعيل
        $unverifiedUsers = User::whereNull('email_verified_at')->count();

        return [
            // الصف الأول - إحصائيات المستخدمين
            Stat::make('👥 Total Users', number_format($totalUsers))
                ->description($newUsersThisMonth . ' added this month')
                ->descriptionIcon($usersGrowth > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($usersGrowth > 0 ? 'success' : ($usersGrowth < 0 ? 'danger' : 'gray'))
                ->chart([4, 3, 6, 5, 7, 8, 6, 9, 7, 8, 10, $newUsersThisMonth])
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;'
                ]),

            Stat::make('✅ Verified Users', number_format($activeUsers))
                ->description(round(($activeUsers / max($totalUsers, 1)) * 100, 1) . '% verified')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('success')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white;'
                ]),

            Stat::make('📊 Activity Rate', $activityRate . '%')
                ->description($recentlyActiveUsers . ' active in last 30 days')
                ->descriptionIcon($activityRate > 70 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($activityRate > 70 ? 'success' : ($activityRate < 30 ? 'danger' : 'warning'))
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;'
                ]),

            Stat::make('⚠️ Unverified Users', number_format($unverifiedUsers))
                ->description('Pending email verification')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($unverifiedUsers > 0 ? 'warning' : 'success')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #ffeaa7 0%, #fab1a0 100%); color: #2d3436;'
                ]),

            // الصف الثاني - مديري الحسابات
            Stat::make('🏢 Account Managers', number_format($totalAccountManagers))
                ->description('Total account managers')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;'
                ]),

            Stat::make('✨ Active Managers', number_format($activeAccountManagers))
                ->description(round(($activeAccountManagers / max($totalAccountManagers, 1)) * 100, 1) . '% with properties')
                ->descriptionIcon('heroicon-m-star')
                ->color('success')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #374151;'
                ]),

            Stat::make('👤 Admin Users', number_format($adminUsers))
                ->description('System administrators')
                ->descriptionIcon('heroicon-m-cog-6-tooth')
                ->color('warning')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); color: #374151;'
                ]),

            Stat::make('👨‍💼 Regular Users', number_format($regularUsers))
                ->description('Standard user accounts')
                ->descriptionIcon('heroicon-m-user')
                ->color('primary')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #d299c2 0%, #fef9d7 100%); color: #374151;'
                ]),
        ];
    }
}

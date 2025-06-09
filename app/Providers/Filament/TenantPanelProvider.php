<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\MenuItem;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class TenantPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('tenant')
            ->path('/tenant')
            ->login()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->brandName('JoRent - بوابة المستأجرين')
            ->brandLogo(asset('images/logo.png'))
            ->favicon(public_path('favicon.ico'))
            ->discoverResources(in: app_path('Filament/Tenant/Resources'), for: 'App\\Filament\\Tenant\\Resources')            ->discoverPages(in: app_path('Filament/Tenant/Pages'), for: 'App\\Filament\\Tenant\\Pages')
            ->pages([
                \App\Filament\Tenant\Pages\Dashboard::class,
                \App\Filament\Tenant\Pages\TenantProfile::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Tenant/Widgets'), for: 'App\\Filament\\Tenant\\Widgets')
            ->authGuard('tenant')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])            ->authMiddleware([
                Authenticate::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('عقودي')
                    ->icon('heroicon-o-document-text')
                    ->collapsed(false),
                NavigationGroup::make()
                    ->label('المدفوعات')
                    ->icon('heroicon-o-credit-card')
                    ->collapsed(false),
                NavigationGroup::make()
                    ->label('الملف الشخصي')
                    ->icon('heroicon-o-user')
                    ->collapsed(false),
            ])            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label('الملف الشخصي')
                    ->url(fn (): string => url('/tenant/tenant-profile'))
                    ->icon('heroicon-o-user'),
                'logout' => MenuItem::make()
                    ->label('تسجيل الخروج')
                    ->url(fn (): string => route('filament.tenant.auth.logout'))
                    ->icon('heroicon-o-arrow-right-on-rectangle'),
            ]);
    }
}

<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber, // You can change this color
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                SubstituteBindings::class,
                VerifyCsrfToken::class,
                DispatchServingFilamentEvent::class,
                DisableBladeIconComponents::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            // {{change 1}}
            // Remove the incorrect fields parameter from globalSearch()
            ->globalSearch();
            // {{end change 1}}

            // If you want to make the Pengguna resource globally searchable by nama_lengkap and username,
            // you need to configure this in the PenggunaResource.php file, not here.
            // In PenggunaResource.php, add:
            // public static function isGlobalSearchable(): bool { return true; }
            // protected static array $globalSearchResultAttributes = ['nama_lengkap', 'username'];

            // You can add other configurations here, like:
            // ->profile(); // To enable the user profile page
            // ->passwordReset(); // To enable password reset functionality
            // ->emailVerification(); // To enable email verification
            // ->userMenuItems([
            //     'logout' => \Filament\Navigation\MenuItem::make()
            //         ->label(__('filament-panels::pages.auth.login.form.actions.authenticate.label')) // Example using translation key
            //         ->url(fn(): string => route('filament.admin.auth.logout'))
            //         ->icon('heroicon-o-arrow-left-on-rectangle'),
            // ]);
    }
}
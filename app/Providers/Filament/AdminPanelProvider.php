<?php

namespace App\Providers\Filament;

use App\Support\SiteSettings;
use Filament\FontProviders\LocalFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            /* Mirrors the public site, which is light-only. */
            ->darkMode(false)
            /*
             * Names only: the @font-face rules and preloads come from the Vite
             * bundle via Vite::fonts() below, so no external font CDN is used.
             */
            ->font('Instrument Sans', provider: LocalFontProvider::class)
            ->serifFont('Source Serif 4', provider: LocalFontProvider::class)
            /* Branding mirrors the public site, resolved from Settings. */
            ->brandName(fn (): string => SiteSettings::name())
            ->favicon(fn (): ?string => SiteSettings::faviconUrl())
            ->login()
            ->colors([
                'primary' => Color::hex('#d97757'),
                /*
                 * Warm neutral ramp replacing Filament's cool zinc grays, so the
                 * panel shares the public site's parchment surfaces. Declared here
                 * rather than in CSS because Filament emits these inline, after the
                 * theme stylesheet, where a :root override would lose the cascade.
                 */
                'gray' => [
                    50 => '#faf9f5',
                    100 => '#f5f3ec',
                    200 => '#eae5da',
                    300 => '#d6cec0',
                    400 => '#b0aea5',
                    500 => '#8a8880',
                    600 => '#6b6862',
                    700 => '#514f4a',
                    800 => '#3d3d3a',
                    900 => '#24241f',
                    950 => '#141413',
                ],
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): HtmlString => Vite::fonts(),
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}

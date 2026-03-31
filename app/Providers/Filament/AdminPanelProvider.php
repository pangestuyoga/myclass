<?php

namespace App\Providers\Filament;

use AchyutN\FilamentLogViewer\FilamentLogViewer;
use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Profile;
use App\Http\Middleware\DynamicFilamentTheme;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use DiogoGPinto\AuthUIEnhancer\AuthUIEnhancerPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Hammadzafar05\MobileBottomNav\MobileBottomNav;
use Hammadzafar05\MobileBottomNav\MobileBottomNavItem;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Saade\Facehash\Enums\Variant;
use Saade\FilamentFacehash\FacehashPlugin;
use Saade\FilamentFacehash\FacehashProvider;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login(Login::class)
            ->profile(Profile::class)
            ->colors([
                'primary' => Color::Gray,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([])
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
                DynamicFilamentTheme::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make()
                    ->navigationSort(5),

                AuthUIEnhancerPlugin::make()
                    ->formPanelPosition('left')
                    ->formPanelWidth('40%')
                    ->emptyPanelBackgroundImageUrl('https://images.pexels.com/photos/267885/pexels-photo-267885.jpeg'),

                FacehashPlugin::make()
                    ->size(40)
                    ->variant(Variant::Gradient)
                    ->initial(true)
                    ->colors([
                        '#ec4899',
                        '#f59e0b',
                        '#3b82f6',
                        '#f97316',
                        '#10b981',
                    ]),

                FilamentLogViewer::make()
                    ->navigationGroup('Sistem')
                    ->navigationIcon('heroicon-o-server-stack')
                    ->navigationLabel('Log')
                    ->navigationUrl('/system/logs')
                    ->pollingTime(null)
                    ->authorize(fn () => auth()->user()->can('View:LogTable'))
                    ->navigationSort(10),

                MobileBottomNav::make()
                    ->items([
                        MobileBottomNavItem::make('Dasbor')
                            ->icon('heroicon-o-home')
                            ->activeIcon('heroicon-s-home')
                            ->url('/admin')
                            ->isActive(fn () => request()->is('admin')),

                        MobileBottomNavItem::make('Presensi')
                            ->icon('heroicon-o-check-circle')
                            ->activeIcon('heroicon-s-check-circle')
                            ->url('/admin/learning/attendances')
                            ->isActive(fn () => request()->is('admin/learning/attendances')),

                        MobileBottomNavItem::make('Materi')
                            ->icon('heroicon-o-book-open')
                            ->activeIcon('heroicon-s-book-open')
                            ->url('/admin/learning/materials')
                            ->isActive(fn () => request()->is('admin/learning/materials')),

                        MobileBottomNavItem::make('Tugas')
                            ->icon('heroicon-o-clipboard-document-list')
                            ->activeIcon('heroicon-s-clipboard-document-list')
                            ->url('/admin/learning/assignments')
                            ->isActive(fn () => request()->is('admin/learning/assignments')),
                    ])
                    ->moreButton(false),
            ])
            ->maxContentWidth('full')
            ->defaultAvatarProvider(FacehashProvider::class)
            ->breadcrumbs(false)
            ->navigationGroups([
                'Master',
                'Kelola',
                'Pembelajaran',
                'Informasi',
                'Sistem',
                'Pelindung',
            ])
            ->globalSearch(false)
            ->brandLogo(fn () => asset('images/logo.png'))
            ->favicon(fn () => asset('images/logo.png'))
            ->brandLogoHeight(function () {
                return request()->is('admin/login') ? '120px' : '40px';
            });
    }
}

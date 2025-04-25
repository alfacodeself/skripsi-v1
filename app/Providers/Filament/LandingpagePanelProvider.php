<?php

namespace App\Providers\Filament;

use App\Models\Landingpage;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;


class LandingpagePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $landingpagesNavigationItems = Landingpage::query()->orderBy('order')->get()->map(function ($pages) {
            $url = url('/#' . $pages->kode_navigasi);
            return NavigationItem::make($pages->navigasi)
                ->url('#' . $pages->kode_navigasi)
                ->icon($pages->icon_navigasi);
        })->toArray();

        return $panel
            ->id('landingpage')
            ->brandName(function () {
                return new HtmlString('<div class="flex items-center gap-3">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRAi_et9kiRD3Ai5hzm5Tv8y_wYe4b1kq35sA&s" alt="Logo" class="h-10 w-10 rounded-full">
                    <span class="text-lg font-semibold tracking-tight text-gray-800">
                        ISP PT Tahta Pratama Solusindo
                    </span>
                </div>');
            })
            // ->darkMode(true)
            ->font('Poppins')
            ->path('/')
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->discoverResources(in: app_path('Filament/Landingpage/Resources'), for: 'App\\Filament\\Landingpage\\Resources')
            ->discoverPages(in: app_path('Filament/Landingpage/Pages'), for: 'App\\Filament\\Landingpage\\Pages')
            ->pages([
                // HomePage::class
            ])
            ->discoverWidgets(in: app_path('Filament/Landingpage/Widgets'), for: 'App\\Filament\\Landingpage\\Widgets')
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
            ])
            ->breadcrumbs(false)
            ->topNavigation()
            ->navigationItems($landingpagesNavigationItems);
    }
}

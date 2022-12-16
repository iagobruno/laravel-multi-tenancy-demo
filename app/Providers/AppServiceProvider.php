<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Carbon::setLocale(config('app.locale'));

        Filament::registerNavigationGroups([
            NavigationGroup::make('Loja'),
            NavigationGroup::make('Blog'),
            NavigationGroup::make('Configurações')->collapsible(false),
        ]);

        Filament::registerRenderHook('global-search.start', function () {
            return view('filament.partials.header-store-link', [
                'route' => tenant_route(tenant()->domains()->first()->domain, 'home')
            ]);
        });

        // Filament::registerRenderHook('sidebar.start', function () {
        //     return 'sidebar.start';
        // });
    }
}

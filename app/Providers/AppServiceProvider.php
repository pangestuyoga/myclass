<?php

namespace App\Providers;

use App\Filament\Support\SystemNotification;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        view()->composer('*', function ($view) {
            $view->with('userTheme', SystemNotification::getThemeSettings());
            $view->with('primaryColors', SystemNotification::getPrimaryColorValues());
        });
    }
}

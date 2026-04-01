<?php

namespace App\Providers;

use App\Filament\Support\ThemeSettings;
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
            $view->with('userTheme', ThemeSettings::getThemeSettings());
            $view->with('primaryColors', ThemeSettings::getPrimaryColorValues());
        });
    }
}

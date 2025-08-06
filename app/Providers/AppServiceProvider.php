<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

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
        // Set locale from session if available
        if (Session::has('locale')) {
            $locale = Session::get('locale');
            $supportedLocales = config('app.available_locales', ['en', 'ar']);
            
            if (in_array($locale, $supportedLocales)) {
                App::setLocale($locale);
            }
        }
    }
}

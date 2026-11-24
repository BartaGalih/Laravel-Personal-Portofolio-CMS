<?php

namespace App\Providers;

use App\Support\SiteSettings;
use Illuminate\Support\Facades\View;
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
        /*
         * Share the stored site name with every view so page titles and the footer
         * agree, without each view querying settings. Resolved lazily on first view
         * render, so artisan commands and migrations never touch the table.
         */
        View::composer('*', function ($view): void {
            $view->with('siteName', SiteSettings::name());
        });
    }
}

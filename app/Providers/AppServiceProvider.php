<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Bagikan pengaturan website ke semua halaman publik sebagai $site.
        View::composer(['layouts.*', 'partials.*', 'home', 'about', 'pipeline'], function ($view) {
            $view->with('site', SiteSetting::current());
        });
    }
}
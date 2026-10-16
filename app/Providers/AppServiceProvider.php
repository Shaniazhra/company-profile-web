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
        $site = null;

        View::composer(
            ['layouts.site', 'partials.*', 'welcome', 'about', 'projects'],
            function ($view) use (&$site) {
                $site ??= SiteSetting::first() ?? new SiteSetting;

                $view->with([
                    'site'  => $site,
                    'loc'   => app()->getLocale(),
                    'brand' => $site->site_name ?: 'Dream Ratio',
                ]);
            }
        );
    }
}
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public const LOCALES = ['id', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale');

        if (! in_array($locale, self::LOCALES, true)) {
            abort(404);
        }

        App::setLocale($locale);

        // Supaya route('home') otomatis membawa bahasa aktif.
        URL::defaults(['locale' => $locale]);

        // Parameter ini tidak perlu diteruskan ke controller.
        $request->route()->forgetParameter('locale');

        return $next($request);
    }
}
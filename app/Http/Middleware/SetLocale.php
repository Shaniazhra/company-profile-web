<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        app()->setLocale('en');
        URL::defaults(['locale' => 'en']);

        return $next($request);
    }
}
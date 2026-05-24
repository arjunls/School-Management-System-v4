<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LocaleMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->session()->get('locale', config('app.locale'));
        $locale = in_array($locale, ['id', 'en']) ? $locale : config('app.locale');
        App::setLocale($locale);
        return $next($request);
    }

    public static function setLocale(string $locale): void
    {
        session(['locale' => $locale]);
        App::setLocale($locale);
    }
}

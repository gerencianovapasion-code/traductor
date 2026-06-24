<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supported = config('translator.ui_locales', ['en']);

        $locale = $request->get('lang')
            ?? session('locale')
            ?? optional($request->user())->locale
            ?? $this->fromBrowser($request, $supported)
            ?? config('app.locale');

        if (! in_array($locale, $supported, true)) {
            $locale = config('translator.fallback_locale', 'en');
        }

        App::setLocale($locale);
        session(['locale' => $locale]);

        return $next($request);
    }

    protected function fromBrowser(Request $request, array $supported): ?string
    {
        $accept = $request->getPreferredLanguage(array_map(fn ($l) => $l, $supported));

        return $accept ?: null;
    }
}

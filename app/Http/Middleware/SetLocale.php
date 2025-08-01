<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = config('app.available_locales', ['en', 'ar']);
        $defaultLocale = config('app.locale', 'en');

        $preferredLocale = $this->getPreferredLocale($request, $supportedLocales, $defaultLocale);

        App::setLocale($preferredLocale);
        Session::put('locale', $preferredLocale);

        return $next($request);
    }

    /**
     * Get the preferred locale based on session, browser, or default
     */
    private function getPreferredLocale(Request $request, array $supportedLocales, string $defaultLocale): string
    {
        // 1. Session
        $sessionLocale = Session::get('locale');
        if ($sessionLocale && in_array($sessionLocale, $supportedLocales)) {
            return $sessionLocale;
        }

        // 2. Browser
        $browserLocale = $this->getBrowserLocale($request, $supportedLocales);
        if ($browserLocale) {
            return $browserLocale;
        }

        // 3. Default
        return in_array($defaultLocale, $supportedLocales) ? $defaultLocale : $supportedLocales[0];
    }

    /**
     * Get browser preferred locale from Accept-Language header
     */
    private function getBrowserLocale(Request $request, array $supportedLocales): ?string
    {
        $acceptLanguage = $request->header('Accept-Language');
        if (!$acceptLanguage) {
            return null;
        }

        preg_match_all('/([a-z]{1,8})(-[a-z]{1,8})?/i', $acceptLanguage, $matches);
        foreach ($matches[1] as $lang) {
            $lang = strtolower($lang);
            if (in_array($lang, $supportedLocales)) {
                return $lang;
            }
        }

        return null;
    }
}

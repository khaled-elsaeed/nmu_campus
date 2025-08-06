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
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = config('app.available_locales', ['en', 'ar']);
        $defaultLocale = config('app.locale', 'en');
        
        $preferredLocale = $this->getPreferredLocale($request, $supportedLocales, $defaultLocale);
        
        App::setLocale($preferredLocale);

        return $next($request);
    }

    /**
     * Determine the preferred locale based on priority:
     * 1. User explicitly selected locale (session)
     * 2. Existing session locale  
     * 3. Browser preference (if no user selection)
     * 4. Default/fallback locale
     *
     * @param Request $request
     * @param array $supportedLocales
     * @param string $defaultLocale
     * @return string
     */
    private function getPreferredLocale(Request $request, array $supportedLocales, string $defaultLocale): string
    {
        $sessionLocale = Session::get('locale');
        $userSelectedLocale = Session::get('user_selected_locale', false);

        // Priority 1: User has explicitly selected a language
        if ($userSelectedLocale && $sessionLocale && $this->isValidLocale($sessionLocale, $supportedLocales)) {
            return $sessionLocale;
        }

        // Priority 2: Use existing session locale from previous auto-detection
        if ($sessionLocale && $this->isValidLocale($sessionLocale, $supportedLocales)) {
            return $sessionLocale;
        }

        // Priority 3: Browser preference (only if user hasn't made explicit choice)
        if (!$userSelectedLocale) {
            $browserLocale = $this->getBrowserPreferredLocale($request, $supportedLocales);
            if ($browserLocale) {
                $this->setSessionLocaleIfEmpty($browserLocale);
                return $browserLocale;
            }
        }

        // Priority 4: Default/fallback locale
        $fallbackLocale = $this->getFallbackLocale($defaultLocale, $supportedLocales);
        $this->setSessionLocaleIfEmpty($fallbackLocale);
        
        return $fallbackLocale;
    }

    /**
     * Extract preferred locale from browser Accept-Language header.
     *
     * @param Request $request
     * @param array $supportedLocales
     * @return string|null
     */
    private function getBrowserPreferredLocale(Request $request, array $supportedLocales): ?string
    {
        $acceptLanguage = $request->header('Accept-Language');
        
        if (!$acceptLanguage) {
            return null;
        }

        // Extract language codes from Accept-Language header
        preg_match_all('/([a-z]{1,8})(-[a-z]{1,8})?/i', $acceptLanguage, $matches);
        
        foreach ($matches[1] as $languageCode) {
            $normalizedLanguage = strtolower($languageCode);
            
            if ($this->isValidLocale($normalizedLanguage, $supportedLocales)) {
                return $normalizedLanguage;
            }
        }

        return null;
    }

    /**
     * Check if locale is supported.
     *
     * @param string $locale
     * @param array $supportedLocales
     * @return bool
     */
    private function isValidLocale(string $locale, array $supportedLocales): bool
    {
        return in_array($locale, $supportedLocales);
    }

    /**
     * Get fallback locale, ensuring it's in supported locales.
     *
     * @param string $defaultLocale
     * @param array $supportedLocales
     * @return string
     */
    private function getFallbackLocale(string $defaultLocale, array $supportedLocales): string
    {
        return $this->isValidLocale($defaultLocale, $supportedLocales) 
            ? $defaultLocale 
            : $supportedLocales[0];
    }

    /**
     * Set locale in session only if no session locale exists.
     *
     * @param string $locale
     * @return void
     */
    private function setSessionLocaleIfEmpty(string $locale): void
    {
        if (!Session::has('locale')) {
            Session::put('locale', $locale);
        }
    }
}
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = config('app.available_locales', ['en', 'ar']);
        $defaultLocale = config('app.locale', 'en');
        $locale = $request->segment(1);
        
        // Check if the first segment is a supported locale
        if ($locale && in_array($locale, $supportedLocales)) {
            // Route has locale prefix (e.g., /en/home, /ar/about)
            App::setLocale($locale);
            Session::put('locale', $locale);
            URL::defaults(['locale' => $locale]);
        } else {
            // Route doesn't have locale prefix - handle fallback
            $preferredLocale = $this->getPreferredLocale($request, $supportedLocales, $defaultLocale);
            
            // Check if this is a route that should be localized
            if ($this->shouldRedirectToLocalizedRoute($request)) {
                return $this->redirectToLocalizedRoute($request, $preferredLocale);
            }
            
            // Set locale for non-localized routes
            App::setLocale($preferredLocale);
            Session::put('locale', $preferredLocale);
            URL::defaults(['locale' => $preferredLocale]);
        }
        
        return $next($request);
    }
    
    /**
     * Get the preferred locale based on session, browser, or default
     */
    private function getPreferredLocale(Request $request, array $supportedLocales, string $defaultLocale): string
    {
        // 1. Check session
        $sessionLocale = Session::get('locale');
        if ($sessionLocale && in_array($sessionLocale, $supportedLocales)) {
            return $sessionLocale;
        }
        
        // 2. Check browser language (Accept-Language header)
        $browserLocale = $this->getBrowserLocale($request, $supportedLocales);
        if ($browserLocale) {
            return $browserLocale;
        }
        
        // 3. Use default locale
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
        
        // Parse Accept-Language header
        $languages = [];
        preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $acceptLanguage, $matches);
        
        if (count($matches[1])) {
            $languages = array_combine($matches[1], $matches[4]);
            foreach ($languages as $lang => $val) {
                if ($val === '') $languages[$lang] = 1;
            }
            arsort($languages, SORT_NUMERIC);
        }
        
        // Find first supported language
        foreach ($languages as $lang => $priority) {
            $lang = strtolower(substr($lang, 0, 2)); // Get language code only (en from en-US)
            if (in_array($lang, $supportedLocales)) {
                return $lang;
            }
        }
        
        return null;
    }
    
    /**
     * Check if the current route should be redirected to a localized version
     */
    private function shouldRedirectToLocalizedRoute(Request $request): bool
    {
        $path = $request->path();
        
        if (str_contains($path, 'all')) {
            return false;
        }
        
        // Don't redirect for these paths
        $excludedPaths = [
            'api/*',
            'language/*',
            'storage/*',
            'public/*',
            '_debugbar/*',
            'telescope/*',
            'horizon/*',
            'nova/*',
            'livewire/*',
            'email/verify*',
        ];
        
        foreach ($excludedPaths as $excludedPath) {
            if ($request->is($excludedPath)) {
                return false;
            }
        }
        
        // Don't redirect AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return false;
        }
        
        // Don't redirect if it's already a localized route
        $locale = $request->segment(1);
        if ($locale && in_array($locale, config('app.available_locales', ['en', 'ar']))) {
            return false;
        }
        
        // Redirect for main application routes
        return true;
    }
    
    /**
     * Redirect to localized version of the route
     */
    private function redirectToLocalizedRoute(Request $request, string $locale): Response
    {
        $path = $request->path();
        $queryString = $request->getQueryString();
        
        // Handle root path
        if ($path === '/') {
            $localizedPath = "/{$locale}";
        } else {
            $localizedPath = "/{$locale}/{$path}";
        }
        
        // Append query string if exists
        if ($queryString) {
            $localizedPath .= "?{$queryString}";
        }
        
        return redirect($localizedPath, 302);
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Switch the application's language.
     *
     * @param  string  $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switchLanguage($locale)
    {
        $supportedLocales = ['en', 'ar'];

        if (!in_array($locale, $supportedLocales)) {
            abort(400, 'Unsupported locale');
        }

        App::setLocale($locale);
        Session::put('locale', $locale);

        // Remove old locale from the current path if present
        $currentPath = parse_url(url()->previous(), PHP_URL_PATH);
        $segments = array_filter(explode('/', trim($currentPath, '/')));

        // Check if first segment is a supported locale and replace/prepend accordingly
        if (!empty($segments) && in_array($segments[0], $supportedLocales)) {
            $segments[0] = $locale;
        } else {
            array_unshift($segments, $locale);
        }

        $newPath = '/' . implode('/', $segments);
        $localizedPath = url($newPath);

        return redirect($localizedPath);
    }
}
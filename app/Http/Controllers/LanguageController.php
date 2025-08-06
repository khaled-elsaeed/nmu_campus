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
        $supportedLocales = config('app.available_locales', ['en', 'ar']);

        if (!in_array($locale, $supportedLocales)) {
            \Log::info("Attempted to switch to unsupported locale: {$locale}");
            abort(400, 'Unsupported locale');
        }

        App::setLocale($locale);

        // Store the user's explicit language choice
        Session::put('locale', $locale);
        Session::put('user_selected_locale', true);

        \Log::info("Language switched to: {$locale}", [
            'session_locale' => Session::get('locale'),
            'user_selected_locale' => Session::get('user_selected_locale'),
        ]);

        return redirect()->back();
    }
}
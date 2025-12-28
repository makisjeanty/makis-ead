<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user has set a language preference
        if ($request->has('lang')) {
            $locale = $request->get('lang');
            Session::put('locale', $locale);
        } elseif (Session::has('locale')) {
            $locale = Session::get('locale');
        } elseif (auth()->check() && auth()->user()->preferred_language) {
            $locale = auth()->user()->preferred_language;
        } else {
            // Detect from browser
            $locale = $this->detectLocale($request);
        }

        // Validate locale
        $supportedLocales = ['fr', 'ht', 'en', 'pt'];
        if (!in_array($locale, $supportedLocales)) {
            $locale = 'fr'; // Default to French
        }

        App::setLocale($locale);

        return $next($request);
    }

    /**
     * Detect locale from browser
     */
    protected function detectLocale(Request $request): string
    {
        $browserLang = $request->server('HTTP_ACCEPT_LANGUAGE');
        
        if (!$browserLang) {
            return 'fr';
        }

        // Parse browser language
        $lang = substr($browserLang, 0, 2);

        // Map to supported locales
        $mapping = [
            'fr' => 'fr',
            'ht' => 'ht',
            'en' => 'en',
            'pt' => 'pt',
            'es' => 'fr', // Spanish speakers might prefer French
        ];

        return $mapping[$lang] ?? 'fr';
    }
}

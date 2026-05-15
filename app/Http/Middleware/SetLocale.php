<?php

namespace App\Http\Middleware;

use App\Services\LocaleService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function __construct(private readonly LocaleService $localeService) {}

    public function handle(Request $request, Closure $next)
    {
        $supported = $this->localeService->codes();
        $defaultLocale = $this->localeService->defaultLocale();

        $sessionLocale = $request->session()->get('locale');
        $sessionLocaleLocked = (bool) $request->session()->get('locale_locked', false);
        $locale = null;

        if ($sessionLocaleLocked && is_string($sessionLocale) && in_array($sessionLocale, $supported, true)) {
            $locale = $sessionLocale;
        }

        if (! $locale && auth()->check() && in_array(auth()->user()->locale, $supported, true)) {
            $locale = auth()->user()->locale;
        }

        if (! $locale) {
            $locale = $defaultLocale;
        }

        if (! in_array($locale, $supported, true)) {
            $locale = $defaultLocale;
        }

        App::setLocale($locale);

        return $next($request);
    }
}

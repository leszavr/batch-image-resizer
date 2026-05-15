<?php

namespace App\Providers;

use App\Contracts\TranslationAutoTranslator;
use App\Services\DbTranslator;
use App\Services\LocaleService;
use App\Services\NullTranslationAutoTranslator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(LocaleService::class);
        $this->app->singleton(DbTranslator::class);
        $this->app->singleton(TranslationAutoTranslator::class, NullTranslationAutoTranslator::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

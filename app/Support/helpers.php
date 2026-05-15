<?php

use App\Services\DbTranslator;

if (! function_exists('dbt')) {
    function dbt(string $key, array $replace = [], ?string $locale = null, string $group = 'ui'): string
    {
        return app(DbTranslator::class)->get($key, $replace, $locale, $group);
    }
}


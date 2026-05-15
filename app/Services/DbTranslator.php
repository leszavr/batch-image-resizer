<?php

namespace App\Services;

use Illuminate\Support\Arr;

class DbTranslator
{
    private array $cache = [];

    public function get(string $key, array $replace = [], ?string $locale = null, string $group = 'ui'): string
    {
        $locale ??= app()->getLocale();
        $fallback = config('app.fallback_locale', 'en');

        try {
            if (class_exists(LocaleService::class)) {
                $fallback = app(LocaleService::class)->defaultLocale();
            }
        } catch (\Throwable) {
            // keep config fallback
        }

        $value = $this->find($group, $key, $locale)
            ?? $this->find($group, $key, $fallback)
            ?? __($group . '.' . $key, $replace, $locale);

        foreach ($replace as $search => $replacement) {
            $value = str_replace(':' . $search, (string) $replacement, $value);
        }

        return $value;
    }

    private function find(string $group, string $key, string $locale): ?string
    {
        $cacheKey = implode(':', [$group, $key, $locale]);

        if (array_key_exists($cacheKey, $this->cache)) {
            return $this->cache[$cacheKey];
        }

        $path = lang_path($locale . '/' . $group . '.php');

        if (! is_file($path)) {
            return $this->cache[$cacheKey] = null;
        }

        $data = require $path;

        if (! is_array($data)) {
            return $this->cache[$cacheKey] = null;
        }

        $translations = is_array($data['translations'] ?? null)
            ? $data['translations']
            : $data;

        $value = Arr::get($translations, $key);

        return $this->cache[$cacheKey] = is_string($value) ? $value : null;
    }
}

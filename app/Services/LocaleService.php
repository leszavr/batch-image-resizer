<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class LocaleService
{
    public function all(): Collection
    {
        $locales = collect();

        foreach ($this->localeCodesFromFilesystem() as $code) {
            $data = $this->loadLocaleData($code);
            $meta = is_array($data['_meta'] ?? null) ? $data['_meta'] : [];
            $guessed = $this->guessNames($code);

            $locales->push((object) [
                'code' => $code,
                'name' => (string) ($meta['name'] ?? $guessed['name']),
                'native_name' => (string) ($meta['native_name'] ?? $guessed['native_name']),
                'is_active' => (bool) ($meta['is_active'] ?? true),
                'is_default' => (bool) ($meta['is_default'] ?? ($code === config('app.locale', 'ru'))),
            ]);
        }

        if ($locales->isEmpty()) {
            return collect($this->fallbackLocales());
        }

        if (! $locales->contains(fn ($locale) => $locale->is_default)) {
            $default = (string) config('app.locale', 'ru');
            $locales = $locales->map(function ($locale) use ($default) {
                $locale->is_default = $locale->code === $default;
                return $locale;
            });
        }

        return $locales
            ->sortBy(fn ($locale) => [
                -((int) $locale->is_default),
                mb_strtolower((string) $locale->name),
            ])
            ->values();
    }

    public function active(): Collection
    {
        $locales = $this->all()->filter(fn ($locale) => (bool) $locale->is_active)->values();

        if ($locales->isEmpty()) {
            return collect($this->fallbackLocales());
        }

        return $locales;
    }

    public function codes(): array
    {
        return $this->active()->pluck('code')->all();
    }

    public function defaultLocale(): string
    {
        $default = $this->all()->first(fn ($locale) => (bool) $locale->is_default);

        if ($default && is_string($default->code) && $default->code !== '') {
            return $default->code;
        }

        return (string) config('app.locale', 'ru');
    }

    private function fallbackLocales(): array
    {
        return collect((array) config('app.available_locales', ['ru', 'en']))
            ->map(function (string $code) {
                $guessed = $this->guessNames($code);

                return (object) [
                    'code' => $code,
                    'name' => $guessed['name'],
                    'native_name' => $guessed['native_name'],
                    'is_active' => true,
                    'is_default' => $code === config('app.locale', 'ru'),
                ];
            })
            ->all();
    }

    private function guessNames(string $code): array
    {
        $upper = Str::upper($code);

        if (! function_exists('locale_get_display_language')) {
            return [
                'name' => $upper,
                'native_name' => $upper,
            ];
        }

        $name = locale_get_display_language($code, 'en') ?: $upper;
        $native = locale_get_display_language($code, $code) ?: $upper;

        return [
            'name' => Str::title($name),
            'native_name' => Str::title($native),
        ];
    }

    private function localeCodesFromFilesystem(): array
    {
        $base = lang_path();

        if (! is_dir($base)) {
            return [];
        }

        $entries = scandir($base) ?: [];
        $codes = [];

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $path = $base . DIRECTORY_SEPARATOR . $entry;

            if (! is_dir($path)) {
                continue;
            }

            if (is_file($path . DIRECTORY_SEPARATOR . 'ui.php')) {
                $codes[] = strtolower($entry);
            }
        }

        return array_values(array_unique($codes));
    }

    private function loadLocaleData(string $locale): array
    {
        $path = lang_path($locale . '/ui.php');

        if (! is_file($path)) {
            return [];
        }

        $data = require $path;

        return is_array($data) ? $data : [];
    }
}

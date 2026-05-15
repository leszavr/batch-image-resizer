<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Services\LocaleService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AdminLocalizationController extends Controller
{
    public function __construct(private readonly LocaleService $localeService) {}

    public function index(Request $request)
    {
        $locales = $this->localeService->all();
        $selectedLocale = (string) $request->query('locale', '');
        $defaultLocale = $this->localeService->defaultLocale();

        if (! $locales->contains(fn ($locale) => $locale->code === $selectedLocale)) {
            $selectedLocale = $locales->firstWhere('is_default', true)?->code
                ?? $locales->first()?->code
                ?? config('app.fallback_locale', 'en');
        }

        $filter = trim((string) $request->query('q', ''));

        $defaultData = $this->loadUiFile($defaultLocale);
        $defaultTranslations = Arr::dot($defaultData['translations']);
        $selectedData = $this->loadUiFile($selectedLocale);
        $selectedTranslations = Arr::dot($selectedData['translations']);

        $keys = collect(array_unique(array_merge(
            array_keys($defaultTranslations),
            array_keys($selectedTranslations),
        )))
            ->filter(function (string $key) use ($filter, $selectedTranslations, $defaultTranslations): bool {
                if ($filter === '') {
                    return true;
                }

                $haystacks = [
                    $key,
                    $selectedTranslations[$key] ?? '',
                    $defaultTranslations[$key] ?? '',
                ];

                foreach ($haystacks as $haystack) {
                    if ($haystack !== '' && mb_stripos((string) $haystack, $filter) !== false) {
                        return true;
                    }
                }

                return false;
            })
            ->sort()
            ->values();

        $translationRows = $keys->map(fn (string $key) => [
            'key' => $key,
            'value' => $selectedTranslations[$key] ?? $defaultTranslations[$key] ?? '',
        ]);

        return view('admin.localization.index', compact(
            'filter',
            'locales',
            'selectedLocale',
            'translationRows'
        ));
    }

    public function storeLocale(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:10',
            'name' => 'required|string|max:255',
            'native_name' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
        ]);

        $code = strtolower(trim((string) $data['code']));

        if (! preg_match('/^[a-z]{2,10}$/', $code)) {
            return redirect()->route('admin.localization.index')->with('error', dbt('admin.localization.messages.invalid_locale_code'));
        }

        if (is_file(lang_path($code . '/ui.php'))) {
            return redirect()->route('admin.localization.index')->with('error', dbt('admin.localization.messages.locale_exists'));
        }

        $guessed = $this->guessNames($code);

        $meta = [
            'code' => $code,
            'name' => (string) ($data['name'] ?: $guessed['name']),
            'native_name' => (string) ($data['native_name'] ?: $guessed['native_name']),
            'is_active' => $request->boolean('is_active', true),
            'is_default' => $request->boolean('is_default'),
        ];

        if ($meta['is_default']) {
            $this->unsetDefaultFlag();
        }

        $payload = [
            '_meta' => $meta,
            'translations' => [],
        ];

        $this->writeUiFile($code, $payload);

        return redirect()->route('admin.localization.index', ['locale' => $code])->with('success', dbt('admin.localization.messages.locale_created'));
    }

    public function updateLocale(Request $request, string $locale)
    {
        $code = strtolower($locale);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'native_name' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
        ]);

        $ui = $this->loadUiFile($code);
        $meta = $ui['_meta'];

        $isDefaultProvided = $request->has('is_default');
        $newDefault = $isDefaultProvided
            ? $request->boolean('is_default')
            : (bool) ($meta['is_default'] ?? false);

        if ($isDefaultProvided && $newDefault) {
            $this->unsetDefaultFlag();
        }

        $meta['name'] = (string) $data['name'];
        $meta['native_name'] = (string) $data['native_name'];
        $meta['is_active'] = $request->boolean('is_active');
        $meta['is_default'] = $newDefault;

        $this->writeUiFile($code, [
            '_meta' => $meta,
            'translations' => $ui['translations'],
        ]);

        if ($isDefaultProvided && $newDefault) {
            app()->setLocale($code);
            $request->session()->put('locale', $code);

            if (auth()->check()) {
                auth()->user()->update(['locale' => $code]);
            }
        }

        return redirect()->route('admin.localization.index', ['locale' => $code])->with('success', dbt('admin.localization.messages.locale_updated'));
    }

    public function destroyLocale(string $locale)
    {
        $code = strtolower($locale);
        $ui = $this->loadUiFile($code);

        if (($ui['_meta']['is_default'] ?? false) === true) {
            return redirect()->route('admin.localization.index')->with('error', dbt('admin.localization.messages.locale_delete_default_blocked'));
        }

        $path = lang_path($code . '/ui.php');

        if (is_file($path)) {
            File::delete($path);
        }

        $dir = lang_path($code);
        if (is_dir($dir)) {
            $entries = array_values(array_diff(scandir($dir) ?: [], ['.', '..']));
            if ($entries === []) {
                File::deleteDirectory($dir);
            }
        }

        return redirect()->route('admin.localization.index')->with('success', dbt('admin.localization.messages.locale_deleted'));
    }

    public function updateTranslations(Request $request)
    {
        $data = $request->validate([
            'locale' => 'required|string|max:10',
            'q' => 'nullable|string',
            'translations' => 'array',
            'translations.*' => 'nullable|string',
        ]);

        $code = strtolower((string) $data['locale']);
        $ui = $this->loadUiFile($code);
        $flat = Arr::dot($ui['translations']);

        foreach (($data['translations'] ?? []) as $key => $value) {
            $normalizedKey = trim((string) $key);

            if ($normalizedKey === '') {
                continue;
            }

            $flat[$normalizedKey] = (string) ($value ?? '');
        }

        $ui['translations'] = Arr::undot($flat);
        $this->writeUiFile($code, $ui);

        return redirect()
            ->route('admin.localization.index', ['locale' => $code, 'q' => (string) ($data['q'] ?? '')])
            ->with('success', dbt('admin.localization.messages.translations_updated'));
    }

    private function unsetDefaultFlag(): void
    {
        $locales = $this->localeService->all();

        foreach ($locales as $locale) {
            if (! $locale->is_default) {
                continue;
            }

            $ui = $this->loadUiFile($locale->code);
            $ui['_meta']['is_default'] = false;
            $this->writeUiFile($locale->code, $ui);
        }
    }

    private function loadUiFile(string $locale): array
    {
        $guessed = $this->guessNames($locale);
        $path = lang_path($locale . '/ui.php');

        if (! is_file($path)) {
            return [
                '_meta' => [
                    'code' => $locale,
                    'name' => $guessed['name'],
                    'native_name' => $guessed['native_name'],
                    'is_active' => true,
                    'is_default' => false,
                ],
                'translations' => [],
            ];
        }

        $data = require $path;

        if (! is_array($data)) {
            return [
                '_meta' => [
                    'code' => $locale,
                    'name' => $guessed['name'],
                    'native_name' => $guessed['native_name'],
                    'is_active' => true,
                    'is_default' => false,
                ],
                'translations' => [],
            ];
        }

        if (! array_key_exists('_meta', $data)) {
            return [
                '_meta' => [
                    'code' => $locale,
                    'name' => $guessed['name'],
                    'native_name' => $guessed['native_name'],
                    'is_active' => true,
                    'is_default' => $locale === config('app.locale', 'ru'),
                ],
                'translations' => $data,
            ];
        }

        return [
            '_meta' => array_merge([
                'code' => $locale,
                'name' => $guessed['name'],
                'native_name' => $guessed['native_name'],
                'is_active' => true,
                'is_default' => false,
            ], (array) ($data['_meta'] ?? [])),
            'translations' => (array) ($data['translations'] ?? []),
        ];
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

    private function writeUiFile(string $locale, array $payload): void
    {
        $dir = lang_path($locale);
        if (! is_dir($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $payload['_meta']['code'] = strtolower((string) ($payload['_meta']['code'] ?? $locale));
        $payload['_meta']['name'] = (string) ($payload['_meta']['name'] ?? Str::upper($locale));
        $payload['_meta']['native_name'] = (string) ($payload['_meta']['native_name'] ?? Str::upper($locale));
        $payload['_meta']['is_active'] = (bool) ($payload['_meta']['is_active'] ?? true);
        $payload['_meta']['is_default'] = (bool) ($payload['_meta']['is_default'] ?? false);

        $content = "<?php\n\nreturn " . var_export([
            '_meta' => $payload['_meta'],
            'translations' => $payload['translations'],
        ], true) . ";\n";

        File::put($dir . '/ui.php', $content);

        if (function_exists('opcache_invalidate')) {
            @opcache_invalidate($dir . '/ui.php', true);
        }
    }
}

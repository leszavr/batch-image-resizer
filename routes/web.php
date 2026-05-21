<?php

use App\Http\Controllers\Web\Admin\AdminDashboardController;
use App\Http\Controllers\Web\Admin\AdminJobController;
use App\Http\Controllers\Web\Admin\AdminLocalizationController;
use App\Http\Controllers\Web\Admin\AdminPlanController;
use App\Http\Controllers\Web\Admin\AdminStatisticsController;
use App\Http\Controllers\Web\Admin\AdminUserController;
use App\Http\Controllers\Web\ImageJobController;
use App\Http\Controllers\Web\ToolsController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\PlanController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\PresetController;
use App\Services\LocaleService;
use Illuminate\Support\Facades\Route;

Route::get('/locale/{locale}', function (string $locale, LocaleService $localeService) {
    $locale = strtolower($locale);
    $supported = $localeService->codes();
    abort_unless(in_array($locale, $supported, true), 404);

    session([
        'locale' => $locale,
        'locale_locked' => true,
    ]);

    if (auth()->check()) {
        auth()->user()->update(['locale' => $locale]);
    }

    return redirect()->back();
})->name('locale.switch');

// ── Main app ────────────────────────────────────────────────────────────────
Route::get('/',                                 [ImageJobController::class, 'index'])->name('home');
Route::post('/jobs',                            [ImageJobController::class, 'store'])->name('jobs.store');
Route::get('/jobs/{imageJob}',                  [ImageJobController::class, 'show'])->name('jobs.show');
Route::get('/jobs/{imageJob}/status',           [ImageJobController::class, 'status'])->name('jobs.status');
Route::get('/jobs/{imageJob}/download',         [ImageJobController::class, 'download'])->name('jobs.download');
Route::get('/jobs/{imageJob}/result-files',     [ImageJobController::class, 'resultFiles'])->name('jobs.result-files');
Route::get('/jobs/{imageJob}/files/{file}/preview', [ImageJobController::class, 'previewResultFile'])->name('jobs.files.preview');

// ── Auth ─────────────────────────────────────────────────────────────────────
Route::get('/login',                            [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login',                           [AuthController::class, 'login'])->middleware('guest');
Route::get('/register',                         [AuthController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/register',                        [AuthController::class, 'register'])->middleware('guest');
Route::post('/logout',                          [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── Authenticated zone ───────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/dashboard',                    [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/history',                      [ImageJobController::class, 'history'])->name('jobs.history');

    // Presets
    Route::get('/presets',                      [PresetController::class, 'index'])->name('presets.index');
    Route::post('/presets',                     [PresetController::class, 'store'])->name('presets.store');
    Route::delete('/presets/{preset}',          [PresetController::class, 'destroy'])->name('presets.destroy');
});

// ── Admin zone ───────────────────────────────────────────────────────────────
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])
        ->middleware('can:admin.dashboard')
        ->name('dashboard');

    Route::get('/jobs', [AdminJobController::class, 'index'])
        ->middleware('can:admin.jobs.manage')
        ->name('jobs.index');
    Route::post('/jobs/cleanup-expired', [AdminJobController::class, 'cleanupExpired'])
        ->middleware('can:admin.jobs.manage')
        ->name('jobs.cleanup-expired');
    Route::post('/jobs/fail-stale', [AdminJobController::class, 'failStale'])
        ->middleware('can:admin.jobs.manage')
        ->name('jobs.fail-stale');
    Route::delete('/jobs/{job}', [AdminJobController::class, 'destroy'])
        ->middleware('can:admin.jobs.manage')
        ->name('jobs.destroy');
    Route::post('/jobs/bulk-delete', [AdminJobController::class, 'bulkDestroy'])
        ->middleware('can:admin.jobs.manage')
        ->name('jobs.bulk-destroy');

    Route::get('/plans', [AdminPlanController::class, 'index'])
        ->middleware('can:admin.plans.manage')
        ->name('plans.index');
    Route::post('/plans', [AdminPlanController::class, 'store'])
        ->middleware('can:admin.plans.manage')
        ->name('plans.store');
    Route::put('/plans/{plan}', [AdminPlanController::class, 'update'])
        ->middleware('can:admin.plans.manage')
        ->name('plans.update');
    Route::delete('/plans/{plan}', [AdminPlanController::class, 'destroy'])
        ->middleware('can:admin.plans.manage')
        ->name('plans.destroy');

    // Users
    Route::get('/users', [AdminUserController::class, 'index'])
        ->middleware('can:admin.users.manage')
        ->name('users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])
        ->middleware('can:admin.users.manage')
        ->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])
        ->middleware('can:admin.users.manage')
        ->name('users.store');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])
        ->middleware('can:admin.users.manage')
        ->name('users.update');
    Route::post('/users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])
        ->middleware('can:admin.users.manage')
        ->name('users.reset-password');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])
        ->middleware('can:admin.users.manage')
        ->name('users.destroy');
    Route::get('/users/{user}/stats', [AdminUserController::class, 'stats'])
        ->middleware('can:admin.users.manage')
        ->name('users.stats');

    // Statistics
    Route::get('/statistics', [AdminStatisticsController::class, 'index'])
        ->middleware('can:admin.dashboard')
        ->name('statistics.index');

    Route::get('/localization', [AdminLocalizationController::class, 'index'])
        ->middleware('can:admin.dashboard')
        ->name('localization.index');
    Route::post('/localization/locales', [AdminLocalizationController::class, 'storeLocale'])
        ->middleware('can:admin.dashboard')
        ->name('localization.locales.store');
    Route::put('/localization/locales/{locale}', [AdminLocalizationController::class, 'updateLocale'])
        ->middleware('can:admin.dashboard')
        ->name('localization.locales.update');
    Route::delete('/localization/locales/{locale}', [AdminLocalizationController::class, 'destroyLocale'])
        ->middleware('can:admin.dashboard')
        ->name('localization.locales.destroy');
    Route::put('/localization/translations', [AdminLocalizationController::class, 'updateTranslations'])
        ->middleware('can:admin.dashboard')
        ->name('localization.translations.update');
});

// ── Plans & billing ──────────────────────────────────────────────────────────
Route::get('/plans',                            [PlanController::class, 'index'])->name('plans.index');

// ── Tools (Online Editors) ────────────────────────────────────────────────────
Route::prefix('tools')->name('tools.')->group(function () {
    // Index page with all tools
    Route::get('/', [ToolsController::class, 'index'])->name('index');

    // Individual tool pages
    Route::get('/crop', [ToolsController::class, 'crop'])->name('crop');
    Route::get('/rotate', [ToolsController::class, 'rotate'])->name('rotate');
    Route::get('/flip', [ToolsController::class, 'flip'])->name('flip');
    Route::get('/resize', [ToolsController::class, 'resize'])->name('resize');
        Route::get('/watermark', [ToolsController::class, 'watermark'])->name('watermark');
        Route::get('/annotate', [ToolsController::class, 'annotate'])->name('annotate');
        Route::get('/frame', [ToolsController::class, 'frame'])->name('frame');
        Route::get('/enlarge', [ToolsController::class, 'enlarge'])->name('enlarge');
    Route::get('/brightness', [ToolsController::class, 'brightness'])->name('brightness');
    Route::get('/contrast', [ToolsController::class, 'contrast'])->name('contrast');
    Route::get('/saturation', [ToolsController::class, 'saturation'])->name('saturation');
    Route::get('/exposure', [ToolsController::class, 'exposure'])->name('exposure');
    Route::get('/temperature', [ToolsController::class, 'temperature'])->name('temperature');
    Route::get('/gamma', [ToolsController::class, 'gamma'])->name('gamma');
    Route::get('/clarity', [ToolsController::class, 'clarity'])->name('clarity');
    Route::get('/blur', [ToolsController::class, 'blur'])->name('blur');

    // API endpoints
    Route::post('/process', [ToolsController::class, 'process'])->name('process');
    Route::get('/preview/{session}/{file}/{ext}', [ToolsController::class, 'preview'])->name('preview');
    Route::get('/download/{session}', [ToolsController::class, 'download'])->name('download');
});

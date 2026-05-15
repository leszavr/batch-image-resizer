<?php

use App\Http\Controllers\Api\ImageJobApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Prefix: /api
| Auth: Laravel Sanctum token
*/

Route::middleware(['auth:sanctum', 'plan.api_access'])->group(function () {
    // Image Jobs
    Route::post('/jobs',                     [ImageJobApiController::class, 'store'])->name('api.jobs.store');
    Route::get('/jobs/{imageJob}',           [ImageJobApiController::class, 'show'])->name('api.jobs.show');
    Route::get('/jobs/{imageJob}/download',  [ImageJobApiController::class, 'download'])->name('api.jobs.download');
});

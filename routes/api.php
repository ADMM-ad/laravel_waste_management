<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\WasteCategoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mobile Consumer API Routes (Prefix: /api/v1)
|--------------------------------------------------------------------------
*/

// Public Authentication
Route::post('/auth/register', [AuthController::class, 'register'])->name('api.auth.register');
Route::post('/auth/login', [AuthController::class, 'login'])->name('api.auth.login');

// Protected Routes (Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // Auth & Profile
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
    Route::get('/auth/me', [AuthController::class, 'me'])->name('api.auth.me');

    // Categories
    Route::get('/categories', [WasteCategoryController::class, 'index'])->name('api.categories.index');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('api.reports.index');
    Route::get('/reports/{id}', [ReportController::class, 'show'])->name('api.reports.show');
    Route::post('/reports', [ReportController::class, 'store'])->name('api.reports.store');
});

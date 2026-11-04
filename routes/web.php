<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AiEvaluationController;
use App\Http\Controllers\Admin\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Admin Portal Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// Guest Admin Auth
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
    Route::get('/admin/register', [AuthController::class, 'showRegisterForm'])->name('admin.register');
    Route::post('/admin/register', [AuthController::class, 'register'])->name('admin.register.submit');
});

// Authenticated Web Routes (Admin and User)
Route::middleware(['auth', 'role:admin|user'])->prefix('admin')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Reports Management
    Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/reports/create', [ReportController::class, 'create'])->name('admin.reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('admin.reports.store');
    Route::post('/reports/classify-image', [ReportController::class, 'classifyImage'])->name('admin.reports.classify');
    Route::get('/reports/{id}', [ReportController::class, 'show'])->name('admin.reports.show');
    Route::get('/reports/{id}/edit', [ReportController::class, 'edit'])->name('admin.reports.edit');
    Route::put('/reports/{id}', [ReportController::class, 'update'])->name('admin.reports.update');
    Route::delete('/reports/{id}', [ReportController::class, 'destroy'])->name('admin.reports.destroy');

    // Profile & Security
    Route::get('/profile', [ProfileController::class, 'index'])->name('admin.profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('admin.profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('admin.profile.password');

    // Admin-Only Routes
    Route::middleware(['role:admin'])->group(function () {
        Route::post('/reports/{id}/status', [ReportController::class, 'updateStatus'])->name('admin.reports.status');

        // Users Management
        Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::post('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('admin.users.toggle-status');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');

        // AI Model Evaluation & Metrics
        Route::get('/ai-evaluation', [AiEvaluationController::class, 'index'])->name('admin.ai.index');
    });
});

// Backward-compatible alias
Route::get('/laporan', function () {
    return redirect()->route('admin.reports.index');
})->name('laporan.index');

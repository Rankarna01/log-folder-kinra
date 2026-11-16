<?php

use App\Http\Controllers\AiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    return auth()->user()->activeInternship()->exists()
        ? redirect()->route('dashboard')
        : redirect()->route('onboarding.show');
});

// Authentication & 2-Step Participant Registration
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Participant Routes
Route::middleware('auth')->group(function () {
    // Step 2 Onboarding (Nama, Divisi, Bagian, Mentor)
    Route::get('/onboarding', [AuthController::class, 'showOnboarding'])->name('onboarding.show');
    Route::post('/onboarding', [AuthController::class, 'storeOnboarding'])->name('onboarding.store');

    // Participant Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Logbook Harian Routes
    Route::get('/logbook', [LogbookController::class, 'index'])->name('logbook.index');
    Route::get('/logbook/create', [LogbookController::class, 'create'])->name('logbook.create');
    Route::post('/logbook', [LogbookController::class, 'store'])->name('logbook.store');
    Route::get('/logbook/{logbook}', [LogbookController::class, 'show'])->name('logbook.show');
    Route::get('/logbook/{logbook}/edit', [LogbookController::class, 'edit'])->name('logbook.edit');
    Route::put('/logbook/{logbook}', [LogbookController::class, 'update'])->name('logbook.update');
    Route::delete('/logbook/{logbook}', [LogbookController::class, 'destroy'])->name('logbook.destroy');
    Route::post('/logbook/{logbook}/duplicate', [LogbookController::class, 'duplicate'])->name('logbook.duplicate');
    Route::post('/logbook/{logbook}/comments', [ReviewController::class, 'comment'])->name('review.comment');

    // Reports & PDF Export Routes
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/reports/{report}/download', [ReportController::class, 'download'])->name('reports.download');

    // Profile & Registration Data Update
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/templates', [ProfileController::class, 'storeTemplate'])->name('profile.templates.store');
    Route::get('/profile/export-csv', [ProfileController::class, 'exportCsv'])->name('profile.export.csv');
});

// Public QR Verification & Offline Fallback
Route::get('/verify/{token}', [ReportController::class, 'verify'])->name('reports.verify');
Route::view('/offline', 'offline')->name('offline');

// JSON API Endpoints
Route::prefix('api')->group(function () {
    Route::post('/ai/polish', [AiController::class, 'polish'])->name('api.ai.polish');
    Route::post('/ai/transcribe', [AiController::class, 'transcribe'])->name('api.ai.transcribe');
    Route::post('/ai/photo-suggest', [AiController::class, 'photoSuggest'])->name('api.ai.photo');
    Route::get('/ai/quota', [AiController::class, 'quota'])->name('api.ai.quota');
    Route::post('/logbooks/sync', [AiController::class, 'syncOffline'])->name('api.logbooks.sync');
});

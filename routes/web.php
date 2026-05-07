<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profileUI', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    

    
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::put('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');

    Route::get('/settings', [ProfileController::class, 'settings'])->name('profile.settings');
    Route::put('/settings/update', [ProfileController::class, 'updateSettings'])->name('profile.settings.update');

    Route::get('/discover', [DashboardController::class, 'discover'])->name('dashboard.discover');
    Route::get('/top_charted', [DashboardController::class, 'topCharted'])->name('dashboard.topCharted');
});

require __DIR__ . '/auth.php';
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    // Rute User Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::put('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
    
    // 👇 Rute Baru Account Settings 👇
    Route::get('/settings', [ProfileController::class, 'settings'])->name('profile.settings');
    Route::put('/settings/update', [ProfileController::class, 'updateSettings'])->name('profile.settings.update');
});

// === RUTE JALAN PINTAS UNTUK TESTING ===
Route::get('/test-login', function () {
    $user = User::firstOrCreate(
        ['email' => 'tester@nextwatch.com'],
        [
            'name' => 'Admin NextWatch',
            'password' => bcrypt('password123')
        ]
    );

    Auth::login($user);
    return redirect('/profile');
});
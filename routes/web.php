<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

// Middleware auth memastikan hanya user yang login yang bisa buka profil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::put('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
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
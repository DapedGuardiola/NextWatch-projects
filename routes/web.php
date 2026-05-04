<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Models\User; // Ditambahkan untuk memanggil database User
use Illuminate\Support\Facades\Auth; // Ditambahkan untuk memaksakan Login

Route::get('/', function () {
    return view('welcome');
});

// Middleware auth memastikan hanya user yang login yang bisa buka profil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
});

// === RUTE JALAN PINTAS UNTUK TESTING ===
Route::get('/test-login', function () {
    // 1. Buat user dummy jika belum ada di database
    $user = User::firstOrCreate(
        ['email' => 'tester@nextwatch.com'],
        [
            'name' => 'Admin NextWatch',
            'password' => bcrypt('password123') // Password diacak (hashed) agar aman
        ]
    );

    // 2. Paksa sistem untuk login menggunakan user dummy tersebut
    Auth::login($user);

    // 3. Otomatis pindah (redirect) ke halaman profil
    return redirect('/profile');
});
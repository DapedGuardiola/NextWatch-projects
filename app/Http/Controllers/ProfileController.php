<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman Profile UI.
     */
    public function index(Request $request): View
    {
        return view('profile.index', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Menampilkan halaman Account Settings.
     */
    public function settings(Request $request): View
    {
        return view('profile.settings', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Memperbarui pengaturan akun (Email, Phone, Password).
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        // Validasi format email sesuai kriteria sistem
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.Auth::id()],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $user = $request->user();
        
        $user->email = $request->email;
        $user->phone = $request->phone;

        // Reset status verifikasi jika email diubah
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Logika Ganti Password & Mock Verifikasi Email
        if ($request->filled('password')) {
            /**
             * Simulasi pemicu verifikasi email karena SMTP belum di-setup.
             * Detail aksi dicatat ke dalam storage/logs/laravel.log
             */
            Log::info("MOCK EMAIL: Permintaan ganti password untuk user: {$user->email}. Link verifikasi simulasi telah dipicu.");

            // Enkripsi password menggunakan Bcrypt (Hash::make)
            $user->password = Hash::make($request->password);
            
            // Mencatat waktu perubahan password untuk riwayat keamanan
            $user->password_changed_at = now(); 
            
            $user->save();

            return Redirect::route('profile.settings')->with('status', 'verification-link-sent');
        }

        $user->save();

        return Redirect::route('profile.settings')->with('status', 'settings-updated');
    }

    /**
     * Memperbarui data profil (Nama, Gender, DOB, Bio).
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        // Validasi nama wajib lebih dari 3 huruf (min: 4)
        $request->validate([
            'name' => ['required', 'string', 'min:4', 'max:255'],
            'gender' => ['nullable', 'string', 'in:Male,Female,Other'],
            'dob' => ['nullable', 'date'],
            'bio' => ['nullable', 'string'],
        ], [
            'name.min' => 'Nama harus terdiri dari minimal 4 karakter.',
        ]);

        $user = $request->user();
        
        $user->name = $request->name;
        $user->gender = $request->gender;
        $user->dob = $request->dob;
        $user->bio = $request->bio;
        
        $user->save();

        return Redirect::route('profile.index')->with('status', 'profile-updated');
    }

    /**
     * Memperbarui foto profil pengguna (Avatar).
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $user = $request->user();

        if ($request->hasFile('avatar')) {
            // Hapus file avatar lama dari storage jika tersedia
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Simpan file baru ke folder avatars di storage public
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
            $user->save();
        }

        return Redirect::route('profile.index')->with('status', 'avatar-updated');
    }

    /**
     * Menampilkan form edit profil bawaan.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Menghapus akun pengguna dari sistem NextWatch.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
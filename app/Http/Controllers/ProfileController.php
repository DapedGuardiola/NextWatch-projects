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

// IMPORT MODEL UNTUK PERSONA GENRE
use App\Models\Genre;
use App\Models\UserGenre;

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
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.Auth::id()],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $user = $request->user();
        
        $user->email = $request->email;
        $user->phone = $request->phone;

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->filled('password')) {
            Log::info("MOCK EMAIL: Permintaan ganti password untuk user: {$user->email}. Link verifikasi simulasi telah dipicu.");
            $user->password = Hash::make($request->password);
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
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
            $user->save();
        }

        return Redirect::route('profile.index')->with('status', 'avatar-updated');
    }

    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

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

    // ==============================================
    // LOGIKA FITUR PERSONA (TUGAS 1)
    // ==============================================

    /**
     * Menampilkan halaman Edit Persona.
     */
    public function persona(Request $request): View
    {
        // Mengambil film-film yang dipilih user sebagai persona awal
        $personaMovies = \App\Models\Favorite::with('movie')
            ->where('user_id', Auth::id())
            ->where('is_persona', true)
            ->get();

        // Mengambil semua daftar master genre untuk ditampilkan di pilihan modal
        $allGenres = Genre::all();
        
        // Mengambil daftar id genre yang saat ini sedang aktif dipilih oleh pengguna
        $myGenres = UserGenre::where('user_id', Auth::id())
            ->pluck('genre_id')
            ->toArray();

        return view('profile.persona', [
            'user' => $request->user(),
            'personaMovies' => $personaMovies,
            'allGenres' => $allGenres,
            'myGenres' => $myGenres
        ]);
    }

    /**
     * Menyimpan atau memperbarui data genre dari Modal Pilihan.
     */
    public function updateGenres(Request $request): RedirectResponse
    {
        $request->validate([
            'genres' => 'nullable|array|max:4',
        ]);

        // Hapus semua pilihan genre lama milik user ini
        UserGenre::where('user_id', Auth::id())->delete();

        // Masukkan data genre baru hasil pilihan modal jika ada
        if ($request->has('genres')) {
            foreach ($request->genres as $genreId) {
                UserGenre::create([
                    'user_id' => Auth::id(),
                    'genre_id' => $genreId,
                ]);
            }
        }

        return Redirect::route('profile.persona')->with('status', 'genres-updated');
    }

    /**
     * Menghapus salah satu genre secara langsung tanpa modal lewat tombol (X).
     */
    public function destroyGenre($genreId): RedirectResponse
    {
        UserGenre::where('user_id', Auth::id())
            ->where('genre_id', $genreId)
            ->delete();

        return Redirect::route('profile.persona')->with('status', 'genre-deleted');
    }

    /**
     * Menyimpan status final aktivasi persona pengguna ke tabel users.
     */
    public function updatePersona(Request $request): RedirectResponse
    {
        $user = $request->user();
        $user->is_personalized = true;
        $user->save();

        return Redirect::route('profile.persona')->with('status', 'persona-updated');
    }
}
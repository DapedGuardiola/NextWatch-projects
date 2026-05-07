<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form (Profile UI).
     */
    public function index(Request $request): View
    {
        return view('profile.index', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Display the user's settings form.
     */
    public function settings(Request $request): View
    {
        return view('profile.settings', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's Settings (Email, Phone, Password)
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

        // Jika user mengisi form password (ingin ganti password)
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            
            // Mengupdate tanggal ganti password sesuai request di blade-mu
            $user->password_changed_at = now(); 
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.settings')->with('status', 'settings-updated');
    }

    /**
     * Update the user's Profile Data (Name, Gender, DOB, Bio)
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'in:Male,Female,Other'],
            'dob' => ['nullable', 'date'],
            'bio' => ['nullable', 'string'],
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
     * Update the user's Avatar Image
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $user = $request->user();

        if ($request->hasFile('avatar')) {
            // Hapus foto lama jika ada
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Simpan foto baru ke storage/app/public/avatars
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
            $user->save();
        }

        return Redirect::route('profile.index')->with('status', 'avatar-updated');
    }

    // --- (Fungsi edit dan destroy bawaan tetap ada di bawah sini) ---

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
}
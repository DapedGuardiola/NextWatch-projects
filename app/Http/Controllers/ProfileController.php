<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash; // Diperlukan untuk enkripsi password

class ProfileController extends Controller
{
    // ==========================================
    // 1. AREA USER PROFILE 
    // ==========================================
    public function index()
    {
        return view('profile.index'); 
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'in:Male,Female,Other'],
            'dob' => ['nullable', 'date'],
            'bio' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = auth()->user();
        $user->update([
            'name' => $request->name,
            'gender' => $request->gender,
            'dob' => $request->dob,
            'bio' => $request->bio,
        ]);

        return back()->with('success', 'Profile updated successfully!');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate(['avatar' => ['required', 'image', 'max:2048']]);
        $user = auth()->user();

        if ($user->avatar) Storage::disk('public')->delete($user->avatar);
        $path = $request->file('avatar')->store('avatars', 'public');
        
        $user->update(['avatar' => $path]);
        return back()->with('status', 'avatar-updated');
    }

    // ==========================================
    // 2. AREA ACCOUNT SETTINGS 
    // ==========================================
    public function settings()
    {
        // Menampilkan halaman account settings
        return view('profile.settings'); 
    }

   public function updateSettings(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', \Illuminate\Validation\Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8'], 
        ]);

        $user->email = $request->email;
        $user->phone = $request->phone;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            // Simpan waktu saat ini ketika password diubah
            $user->password_changed_at = now(); 
        }

        $user->save();

        return back()->with('success', 'Account settings updated!');
    }
}
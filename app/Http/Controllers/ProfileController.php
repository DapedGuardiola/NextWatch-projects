<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        // Menampilkan halaman profil
        return view('profile.index'); 
    }

    public function updateProfile(Request $request)
    {
        // Validasi inputan user
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'in:Male,Female,Other'],
            'dob' => ['nullable', 'date'],
            'bio' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = auth()->user();
        
        // Simpan data ke database
        $user->update([
            'name' => $request->name,
            'gender' => $request->gender,
            'dob' => $request->dob,
            'bio' => $request->bio,
        ]);

        return back()->with('success', 'Profile updated successfully!');
    }

    // Fungsi upload foto
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:2048'],
        ]);

        $user = auth()->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        
        $user->update(['avatar' => $path]);

        return back()->with('status', 'avatar-updated');
    }
}
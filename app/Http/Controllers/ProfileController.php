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
use Illuminate\Support\Facades\DB; // Tambahan untuk menarik nama dari DB
use Illuminate\View\View;

// IMPORT MODEL
use App\Models\Genre;
use App\Models\UserGenre;
use App\Models\UserTaste;

class ProfileController extends Controller
{
    /**
     * Helper aman untuk menarik nama dari tabel berdasarkan ID
     */
    private function getNameSafely($table, $column, $id, $fallbackPrefix)
    {
        try {
            $name = DB::table($table)->where($column, $id)->value('name');
            if (!$name && $column !== 'id') {
                $name = DB::table($table)->where('id', $id)->value('name');
            }
            return $name ?: $fallbackPrefix . ' ' . $id;
        } catch (\Exception $e) {
            return $fallbackPrefix . ' ' . $id;
        }
    }

    /**
     * Menampilkan halaman Profile UI.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // 1. Ambil Data User Genre untuk Grafik
        $userGenres = UserGenre::with('genre')
            ->where('user_id', $user->id)
            ->orderBy('weight', 'desc')
            ->get();

        $maxGenreWeight = 1;
        $genreLabels = [];
        $genreWeights = [];
        foreach ($userGenres as $ug) {
            $genreLabels[] = $ug->genre ? $ug->genre->name : 'Unknown';
            $w = $ug->weight ?? 0;
            // Konversi ke persentase berdasarkan bobot max
            $genreWeights[] = $maxGenreWeight > 0 ? round(($w / $maxGenreWeight) * 100) : 0;
        }

        // 2. Ambil Data User Taste (Aktor, Sutradara, Era)
        $userTaste = UserTaste::where('user_id', $user->id)->first();

        $actorsData = [];
        $directorsData = [];
        $erasData = [];

        if ($userTaste) {
            // Kalkulasi Persentase Aktor
            $actors = (array) ($userTaste->preferred_actors ?? []);
            arsort($actors);
            $maxActor = 1;
            foreach ($actors as $id => $score) {
                $name = $this->getNameSafely('actors', 'tmdb_actor_id', $id, 'Actor');
                $actorsData[$name] = $maxActor > 0 ? round(($score / $maxActor) * 100) : 0;
            }

            // Kalkulasi Persentase Sutradara
            $directors = (array) ($userTaste->preferred_directors ?? []);
            arsort($directors);
            $maxDir = 1;
            foreach ($directors as $id => $score) {
                // Mencoba tabel directors atau crews
                $name = $this->getNameSafely('directors', 'tmdb_director_id', $id, 'Director');
                if (str_starts_with($name, 'Director')) {
                    $name = $this->getNameSafely('crews', 'tmdb_crew_id', $id, 'Director');
                }
                $directorsData[$name] = $maxDir > 0 ? round(($score / $maxDir) * 100) : 0;
            }

            // Kalkulasi Persentase Era
            $eras = (array) ($userTaste->preferred_era ?? []);
            arsort($eras);
            $maxEra = 1;
            foreach ($eras as $eraName => $score) {
                $erasData[$eraName] = $maxEra > 0 ? round(($score / $maxEra) * 100) : 0;
            }
        }

        return view('profile.index', [
            'user' => $user,
            'userGenres' => $userGenres,
            'userTaste' => $userTaste,
            'genreLabels' => $genreLabels,
            'genreWeights' => $genreWeights,
            'actorsData' => $actorsData,
            'directorsData' => $directorsData,
            'erasData' => $erasData,
        ]);
    }

    public function settings(Request $request): View {
        return view('profile.settings', ['user' => $request->user()]);
    }

    public function updateSettings(Request $request): RedirectResponse {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.Auth::id()],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $user = $request->user();
        $user->email = $request->email;
        $user->phone = $request->phone;

        if ($user->isDirty('email')) $user->email_verified_at = null;

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

    public function updateProfile(Request $request): RedirectResponse {
        $request->validate([
            'name' => ['required', 'string', 'min:4', 'max:255'],
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

    public function updateAvatar(Request $request): RedirectResponse {
        $request->validate(['avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048']]);
        $user = $request->user();

        if ($request->hasFile('avatar')) {
            if ($user->avatar) Storage::disk('public')->delete($user->avatar);
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
            $user->save();
        }
        return Redirect::route('profile.index')->with('status', 'avatar-updated');
    }

    public function edit(Request $request): View {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function destroy(Request $request): RedirectResponse {
        $request->validateWithBag('userDeletion', ['password' => ['required', 'current_password']]);
        $user = $request->user();
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return Redirect::to('/');
    }

    // ==============================================
    // LOGIKA FITUR PERSONA 
    // ==============================================

    public function persona(Request $request): View {
        $personaMovies = \App\Models\Favorite::with('movie')
            ->where('user_id', Auth::id())
            ->where('is_persona', true)->get();
        $allGenres = Genre::all();
        $myGenres = UserGenre::where('user_id', Auth::id())->pluck('genre_id')->toArray();

        return view('profile.persona', [
            'user' => $request->user(),
            'personaMovies' => $personaMovies,
            'allGenres' => $allGenres,
            'myGenres' => $myGenres
        ]);
    }

    public function updateGenres(Request $request): RedirectResponse {
        $request->validate(['genres' => 'nullable|array|max:4']);
        UserGenre::where('user_id', Auth::id())->delete();
        if ($request->has('genres')) {
            foreach ($request->genres as $genreId) {
                UserGenre::create(['user_id' => Auth::id(), 'genre_id' => $genreId]);
            }
        }
        return Redirect::route('profile.persona')->with('status', 'genres-updated');
    }

    public function destroyGenre($genreId): RedirectResponse {
        UserGenre::where('user_id', Auth::id())->where('genre_id', $genreId)->delete();
        return Redirect::route('profile.persona')->with('status', 'genre-deleted');
    }

    public function updatePersona(Request $request): RedirectResponse {
        $user = $request->user();
        $user->is_personalized = true;
        $user->save();
        return Redirect::route('profile.persona')->with('status', 'persona-updated');
    }
}
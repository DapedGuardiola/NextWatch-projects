<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - NextWatch</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-900 font-sans antialiased">

    @php
    $avatarUrl = auth()->user()->avatar
    ? asset('storage/' . auth()->user()->avatar)
    : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=333&color=fff';
    @endphp

    <div class="relative min-h-screen w-full overflow-hidden text-gray-200">
        <!-- DYNAMIC BACKGROUND -->
        <div class="absolute inset-0 bg-cover bg-center z-0"
            style="background-image: url('{{ $avatarUrl }}'); filter: blur(60px); transform: scale(1.2);">
        </div>
        <div class="absolute inset-0 bg-black/60 z-0"></div>

        <div class="relative z-10 flex h-full max-w-7xl mx-auto p-6">

            <!-- Sidebar Kiri -->
            <div class="w-1/4 pr-8 flex flex-col gap-6 mt-20 font-semibold">
                <a href="{{ route('profile.index') }}" class="bg-white text-black py-2 px-4 rounded-xl text-center">User Profile</a>
                <a href="{{ route('profile.settings') }}" class="text-gray-400 hover:text-white text-center transition">Account Settings</a>
                <a href="#" class="text-gray-400 hover:text-white text-center transition">Favorite Movies</a>
                <a href="#" class="text-gray-400 hover:text-white text-center transition">Watchlist</a>

                <form method="POST" action="{{ route('logout') }}" class="mt-12">
                    @csrf
                    <button type="submit" class="text-red-500 w-full text-center hover:text-red-400">Sign Out</button>
                </form>
            </div>

            <!-- Panel Kanan -->
            <div class="w-3/4">
                <div class="flex justify-center gap-4 mb-8">
                    <button class="bg-yellow-600/30 text-yellow-500 px-4 py-1 rounded-full text-sm">Home</button>
                    <button class="bg-gray-800/50 px-4 py-1 rounded-full text-sm">Discover</button>
                    <button class="bg-gray-800/50 px-4 py-1 rounded-full text-sm">Top Charted</button>
                </div>

                <div class="bg-gray-900/40 backdrop-blur-md border border-gray-700/50 rounded-3xl p-8 flex gap-8">
                    <!-- Kiri: Foto -->
                    <div class="w-1/3 flex flex-col items-center">
                        <img src="{{ $avatarUrl }}" alt="Profile" class="w-48 h-48 rounded-3xl object-cover mb-4 shadow-xl">

                        <form id="avatar-form" action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="avatar" id="avatar-input" class="hidden" accept="image/*">
                        </form>

                        <button onclick="document.getElementById('avatar-input').click()"
                            class="bg-white text-black font-semibold py-2 px-6 rounded-xl w-full hover:bg-gray-200 transition">
                            Edit Profile
                        </button>
                    </div>

                    <!-- Kanan: Form Data Diri (Sudah Bisa Diedit) -->
                    <div class="w-2/3">
                        <form action="{{ route('profile.update') }}" method="POST" class="flex flex-col gap-6">
                            @csrf
                            @method('PUT')

                            <div>
                                <label class="text-sm text-gray-400">Name</label>
                                <input type="text" name="name" value="{{ auth()->user()->name }}"
                                    class="w-full bg-gray-800/50 p-3 rounded-xl mt-1 text-white border border-transparent focus:border-yellow-500 focus:bg-gray-800 focus:ring-0 outline-none transition">
                            </div>

                            <div class="flex gap-4">
                                <div class="w-1/2">
                                    <label class="text-sm text-gray-400">Gender</label>
                                    <select name="gender" class="w-full bg-gray-800/50 p-3 rounded-xl mt-1 text-white border border-transparent focus:border-yellow-500 focus:bg-gray-800 focus:ring-0 outline-none transition appearance-none">
                                        <option value="" disabled {{ !auth()->user()->gender ? 'selected' : '' }}>Select Gender</option>
                                        <option value="Male" {{ auth()->user()->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ auth()->user()->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                        <option value="Other" {{ auth()->user()->gender == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                                <div class="w-1/2">
                                    <label class="text-sm text-gray-400">Date of Birth</label>
                                    <input type="date" name="dob" value="{{ auth()->user()->dob }}"
                                        class="w-full bg-gray-800/50 p-3 rounded-xl mt-1 text-white border border-transparent focus:border-yellow-500 focus:bg-gray-800 focus:ring-0 outline-none transition color-scheme-dark"
                                        style="color-scheme: dark;">
                                </div>
                            </div>

                            <div>
                                <label class="text-sm text-gray-400">Bio</label>
                                <textarea name="bio" rows="3"
                                    class="w-full bg-gray-800/50 p-3 rounded-xl mt-1 text-gray-300 border border-transparent focus:border-yellow-500 focus:bg-gray-800 focus:ring-0 outline-none transition">{{ auth()->user()->bio }}</textarea>
                            </div>

                            <!-- Tombol Save yang wajib ada -->
                            <div class="flex justify-end mt-2">
                                <button type="submit" class="bg-yellow-600/80 hover:bg-yellow-500 text-white font-semibold py-2 px-6 rounded-xl transition">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('avatar-input').addEventListener('change', function() {
            if (this.files && this.files[0]) document.getElementById('avatar-form').submit();
        });
    </script>
</body>

</html>
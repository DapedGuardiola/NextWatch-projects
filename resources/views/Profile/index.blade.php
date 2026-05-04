<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - NextWatch</title>
    <!-- Memanggil Tailwind CSS via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 font-sans antialiased">

    @php
        $avatarUrl = auth()->user()->avatar 
            ? asset('storage/' . auth()->user()->avatar) 
            : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=333&color=fff';
    @endphp

    <!-- Container Utama -->
    <div class="relative min-h-screen w-full overflow-hidden text-gray-200">
        
        <!-- DYNAMIC BACKGROUND -->
        <div class="absolute inset-0 bg-cover bg-center z-0" 
             style="background-image: url('{{ $avatarUrl }}'); filter: blur(60px); transform: scale(1.2);">
        </div>
        <div class="absolute inset-0 bg-black/60 z-0"></div>

        <!-- Konten Halaman -->
        <div class="relative z-10 flex h-full max-w-7xl mx-auto p-6">
            
            <!-- Sidebar Kiri -->
            <div class="w-1/4 pr-8 flex flex-col gap-6 mt-20 font-semibold">
                <a href="#" class="bg-white text-black py-2 px-4 rounded-xl text-center">User Profile</a>
                <a href="#" class="text-gray-400 hover:text-white text-center">Account Settings</a>
                <a href="#" class="text-gray-400 hover:text-white text-center">Favorite Movies</a>
                <a href="#" class="text-gray-400 hover:text-white text-center">Watchlist</a>
                
                <form method="POST" action="/logout" class="mt-12">
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

                    <!-- Kanan: Data Dummy -->
                    <div class="w-2/3 flex flex-col gap-6">
                        <div>
                            <label class="text-sm text-gray-400">Name</label>
                            <div class="bg-gray-800/50 p-3 rounded-xl mt-1">{{ auth()->user()->name }}</div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-1/2">
                                <label class="text-sm text-gray-400">Gender</label>
                                <div class="bg-gray-800/50 p-3 rounded-xl mt-1 text-gray-300">Male</div>
                            </div>
                            <div class="w-1/2">
                                <label class="text-sm text-gray-400">Date of Birth</label>
                                <div class="bg-gray-800/50 p-3 rounded-xl mt-1 text-gray-300">17 January 2004</div>
                            </div>
                        </div>
                        <div>
                            <label class="text-sm text-gray-400">Bio</label>
                            <p class="text-sm text-gray-300 mt-2">menurut saya film penting untuk memahami kehidupan dari berbagai sudut pandang...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('avatar-input').addEventListener('change', function() {
            if(this.files && this.files[0]) document.getElementById('avatar-form').submit();
        });
    </script>
</body>
</html>
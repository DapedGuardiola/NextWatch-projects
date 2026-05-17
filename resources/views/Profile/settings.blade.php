<x-app-layout>
    <x-slot name="title">
        {{ __('Account Settings - NextWatch') }}
    </x-slot>

    <style>
        .input-transparan {
            background: transparent !important;
            background-color: transparent !important;
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            color: #d1d5db !important;
            width: 100%;
            text-align: center;
            padding: 10px 40px;
        }

        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus, 
        input:-webkit-autofill:active {
            transition: background-color 5000s ease-in-out 0s !important;
            -webkit-text-fill-color: #D1D5DB !important;
        }

        .icon-edit {
            width: 16px !important;
            height: 16px !important;
            position: absolute;
            right: 15px;
            pointer-events: none; 
            color: #9ca3af;
        }
        
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>

    @php
        $avatarUrl = auth()->user()->avatar 
            ? asset('storage/' . auth()->user()->avatar) 
            : "https://ui-avatars.com/api/?name=" . urlencode(auth()->user()->name) . "&background=333&color=fff";
    @endphp

    <div class="relative min-h-screen w-full overflow-hidden text-gray-200">
        <div class="absolute inset-0 bg-cover bg-center z-0" 
             style="background-image: url('{{ $avatarUrl }}'); filter: blur(60px); transform: scale(1.2);">
        </div>
        <div class="absolute inset-0 bg-black/60 z-0"></div>

        <div class="relative z-10 flex h-full max-w-7xl mx-auto p-6">
            
            <div class="w-1/4 pr-8 flex flex-col gap-6 mt-20 font-semibold">
                <a href="{{ route('profile.index') }}" class="text-gray-400 hover:text-white text-center transition">User Profile</a>
                <a href="{{ route('profile.settings') }}" class="bg-white text-black py-2 px-4 rounded-xl text-center shadow-lg">Account Settings</a>
                <a href="#" class="text-gray-400 hover:text-white text-center transition">Favorite Movies</a>
                <a href="#" class="text-gray-400 hover:text-white text-center transition">Watchlist</a>
                
                <form method="POST" action="{{ route('logout') }}" class="mt-12">
                    @csrf
                    <button type="submit" class="text-red-600 font-bold w-full text-center hover:text-red-500 transition">Sign Out</button>
                </form>
            </div>

            <div class="w-3/4">
                <div class="bg-black/20 backdrop-blur-md rounded-[2rem] p-12 mt-20 mx-auto w-11/12 shadow-2xl relative z-20">
                    <form action="{{ route('profile.settings.update') }}" method="POST" class="flex flex-col gap-8" autocomplete="off">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <label class="text-sm font-bold text-white mb-2 block">Email</label>
                            <div class="relative w-full flex items-center justify-center border-b border-transparent hover:border-gray-600/50 transition">
                                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" 
                                       class="input-transparan" required autocomplete="off">
                                <svg class="icon-edit" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </div>
                            @error('email')
                                <p class="text-xs text-red-500 text-center mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-bold text-white mb-2 block">Phone</label>
                            <div class="relative w-full flex items-center justify-center border-b border-transparent hover:border-gray-600/50 transition">
                                <input type="number" name="phone" value="{{ old('phone', auth()->user()->phone) }}" placeholder="+62 8..." 
                                       class="input-transparan" autocomplete="off">
                                <svg class="icon-edit" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </div>
                            @error('phone')
                                <p class="text-xs text-red-500 text-center mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-bold text-white mb-2 block">Password</label>
                            <div class="flex items-center">
                                <div class="relative flex-grow flex items-center justify-center border-b border-transparent hover:border-gray-600/50 transition">
                                    <input type="password" name="password" placeholder="••••••••" 
                                           class="input-transparan" autocomplete="new-password">
                                    <svg class="icon-edit" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </div>
                                <span class="text-xs text-gray-500 w-1/4 pl-4 text-right">
                                    Last Change : {{ auth()->user()->password_changed_at ? \Carbon\Carbon::parse(auth()->user()->password_changed_at)->format('d-m-Y') : 'Never' }}
                                </span>
                            </div>
                            @error('password')
                                <p class="text-xs text-red-500 mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        @if (session('status') === 'verification-link-sent')
                            <p class="text-sm text-yellow-500 text-center font-medium mt-4">
                                Permintaan ganti password diterima. Cek email Anda untuk verifikasi.
                            </p>
                        @elseif (session('status') === 'settings-updated')
                            <p class="text-sm text-green-400 text-center font-medium mt-4">
                                Pengaturan akun berhasil diperbarui!
                            </p>
                        @endif

                        <div class="flex flex-col items-center mt-4 gap-6">
                            <button type="submit" class="text-yellow-500 font-semibold py-2 px-8 rounded-full hover:bg-yellow-500/10 transition text-sm cursor-pointer border border-transparent hover:border-yellow-500/30">
                                Save Changes
                            </button>
                            <div class="flex gap-4 text-xs font-semibold text-gray-400">
                                <a href="#" class="hover:text-white transition">Privacy Policy</a>
                                <a href="#" class="hover:text-white transition">Terms Of Use</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
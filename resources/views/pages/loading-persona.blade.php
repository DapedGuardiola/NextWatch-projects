{{-- resources/views/loading.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Menyiapkan — NextWatch</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-[#070709] min-h-screen flex items-center justify-center overflow-hidden relative" style="font-family:'Plus Jakarta Sans',sans-serif">

    {{-- Blobs --}}
    <div class="absolute rounded-full pointer-events-none" style="width:500px;height:500px;top:-150px;left:-100px;background:rgba(88,80,236,0.07);filter:blur(100px)"></div>
    <div class="absolute rounded-full pointer-events-none" style="width:400px;height:400px;bottom:-100px;right:-80px;background:rgba(236,80,120,0.05);filter:blur(100px)"></div>

    <div class="flex flex-col items-center z-10">
        {{-- Badge --}}
        <div class="flex items-center gap-2 px-4 py-1.5 rounded-full mb-7" style="background:rgba(255,255,255,0.06);border:0.5px solid rgba(255,255,255,0.1)">
            <div class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></div>
            <span class="text-xs" style="color:rgba(255,255,255,0.5)">Menyiapkan rekomendasi</span>
        </div>

        {{-- Spinner --}}
        <div class="rounded-full mb-6" style="width:48px;height:48px;border:2px solid rgba(255,255,255,0.1);border-top:2px solid #fff;animation:spin 1s linear infinite"></div>

        {{-- Text --}}
        <p class="text-white text-3xl font-bold text-center" style="letter-spacing:-0.3px">Kami sedang mempelajari selera mu 🎬</p>
        <p class="text-center mt-2 text-sm" style="color:rgba(255,255,255,0.4);max-width:320px;line-height:1.6">
            Ini hanya terjadi sekali saat akun dibuat. Sebentar lagi kamu bisa menikmati rekomendasi yang personal.
        </p>

        {{-- Progress bar --}}
        <div class="mt-7 rounded-full overflow-hidden" style="width:260px;height:3px;background:rgba(255,255,255,0.08)">
            <div id="progress" class="h-full rounded-full bg-white" style="width:0%;transition:width 0.6s ease"></div>
        </div>
    </div>

    <style>
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>

    <script>
        // Animasi progress bar
        let p = 0;
        const bar = document.getElementById('progress');
        const grow = setInterval(() => {
            p = Math.min(p + Math.random() * 8, 90);
            bar.style.width = p + '%';
        }, 600);

        // Polling status
        const checkStatus = setInterval(async () => {
            const res  = await fetch('/persona-status');
            const data = await res.json();
            if (data.ready === true) {
                clearInterval(checkStatus);
                clearInterval(grow);
                bar.style.width = '100%';
                setTimeout(() => window.location.href = '/dashboard', 600);
            }
        }, 3000);
    </script>
</body>
</html>
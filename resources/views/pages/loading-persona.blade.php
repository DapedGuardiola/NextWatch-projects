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
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html {
            font-family: 'Plus Jakarta Sans', sans-serif;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }
        body {
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @keyframes pulseRing {
            0%   { transform: scale(1);   opacity: 0.15; }
            50%  { transform: scale(1.5); opacity: 0; }
            100% { transform: scale(1);   opacity: 0; }
        }

        .anim-fade-up { animation: fadeUp 0.6s cubic-bezier(0.22, 1, 0.36, 1) both; }
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }

        .spinner {
            border: 2px solid rgba(255,255,255,0.1);
            border-top-color: #fff;
            animation: spin 1s linear infinite;
        }

        .pulse-ring {
            position: absolute;
            inset: -8px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,0.15);
            animation: pulseRing 2.4s ease-out infinite;
        }
        .pulse-ring-2 {
            animation-delay: 0.8s;
        }
        .pulse-ring-3 {
            animation-delay: 1.6s;
        }

        #progress-bar {
            width: 0%;
            transition: width 0.6s ease;
        }

        .step-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: rgba(255,255,255,0.3);
            transition: color 0.4s ease;
        }
        .step-item.done {
            color: rgba(255,255,255,0.75);
        }
        .step-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: rgba(255,255,255,0.15);
            flex-shrink: 0;
            transition: background 0.4s ease, box-shadow 0.4s ease;
        }
        .step-item.done .step-dot {
            background: #fff;
            box-shadow: 0 0 6px rgba(255,255,255,0.5);
        }
        .step-item.active .step-dot {
            background: rgba(255,255,255,0.6);
            animation: pulse 1.2s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.4; }
        }
        .step-item.active {
            color: rgba(255,255,255,0.6);
        }
    </style>
</head>
<body class="bg-[#070709] flex items-center justify-center relative px-5" style="width:100%;height:100%">

    {{-- Blobs --}}
    <div class="absolute rounded-full pointer-events-none"
         style="width:clamp(280px,60vw,500px);height:clamp(280px,60vw,500px);
                top:-100px;left:-80px;
                background:rgba(88,80,236,0.08);filter:blur(100px)"></div>
    <div class="absolute rounded-full pointer-events-none"
         style="width:clamp(220px,50vw,400px);height:clamp(220px,50vw,400px);
                bottom:-80px;right:-60px;
                background:rgba(236,80,120,0.06);filter:blur(100px)"></div>

    {{-- Main card --}}
    <div class="flex flex-col items-center z-10 w-full max-w-[360px] sm:max-w-[420px] text-center">

        {{-- Badge --}}
        <div class="flex items-center gap-2 px-4 py-1.5 rounded-full mb-8 sm:mb-10 anim-fade-up"
             style="background:rgba(255,255,255,0.06);border:0.5px solid rgba(255,255,255,0.1)">
            <div class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></div>
            <span class="text-[11px] sm:text-xs" style="color:rgba(255,255,255,0.5)">Menyiapkan rekomendasi</span>
        </div>

        {{-- Spinner with pulse rings --}}
        <div class="relative flex items-center justify-center mb-8 sm:mb-10 anim-fade-up delay-100">
            <div class="pulse-ring"></div>
            <div class="pulse-ring pulse-ring-2"></div>
            <div class="pulse-ring pulse-ring-3"></div>
            <div class="spinner rounded-full"
                 style="width:44px;height:44px;" class="sm:w-12 sm:h-12"></div>
        </div>

        {{-- Headline --}}
        <h1 class="text-white text-2xl sm:text-3xl font-bold leading-tight mb-2 anim-fade-up delay-200"
            style="letter-spacing:-0.3px">
            Kami sedang mempelajari<br class="sm:hidden"> seleramu
            <span class="inline-block ml-1">🎬</span>
        </h1>

        <p class="text-[13px] sm:text-sm leading-relaxed mb-8 sm:mb-10 anim-fade-up delay-300"
           style="color:rgba(255,255,255,0.4);max-width:300px;margin-left:auto;margin-right:auto">
            Ini hanya terjadi sekali saat akun dibuat. Sebentar lagi kamu bisa menikmati rekomendasi yang personal.
        </p>

        {{-- Steps --}}
        <div class="flex flex-col gap-2.5 w-full mb-8 sm:mb-10 text-left anim-fade-up delay-300"
             style="max-width:240px;margin-left:auto;margin-right:auto">
            <div class="step-item active" id="step-1">
                <div class="step-dot"></div>
                <span>Menganalisis genre favorit</span>
            </div>
            <div class="step-item" id="step-2">
                <div class="step-dot"></div>
                <span>Mempelajari film pilihanmu</span>
            </div>
            <div class="step-item" id="step-3">
                <div class="step-dot"></div>
                <span>Membangun profil seleramu</span>
            </div>
        </div>

        {{-- Progress bar --}}
        <div class="rounded-full overflow-hidden w-full anim-fade-up delay-400"
             style="max-width:260px;height:3px;background:rgba(255,255,255,0.08)">
            <div id="progress-bar" class="h-full rounded-full bg-white progress-bar"></div>
        </div>

        {{-- Percentage --}}
        <p class="mt-3 text-[11px] anim-fade-up delay-400" style="color:rgba(255,255,255,0.2)">
            <span id="progress-text">0</span>%
        </p>

    </div>

    <script>
        let p = 0;
        const bar  = document.getElementById('progress-bar');
        const pTxt = document.getElementById('progress-text');

        const steps = [
            document.getElementById('step-1'),
            document.getElementById('step-2'),
            document.getElementById('step-3'),
        ];

        function setStep(index) {
            steps.forEach((s, i) => {
                s.classList.remove('done', 'active');
                if (i < index)  s.classList.add('done');
                if (i === index) s.classList.add('active');
            });
        }

        const grow = setInterval(() => {
            p = Math.min(p + Math.random() * 8, 90);
            bar.style.width = p + '%';
            pTxt.textContent = Math.round(p);

            if (p >= 30) setStep(1);
            if (p >= 65) setStep(2);
        }, 600);

        const checkStatus = setInterval(async () => {
            try {
                const res  = await fetch('/persona-status');
                const data = await res.json();
                if (data.ready === true) {
                    clearInterval(checkStatus);
                    clearInterval(grow);
                    setStep(3);
                    steps[2].classList.remove('active');
                    steps[2].classList.add('done');
                    bar.style.width = '100%';
                    pTxt.textContent = '100';
                    setTimeout(() => window.location.href = '/dashboard', 800);
                }
            } catch(e) { /* retry next tick */ }
        }, 3000);
    </script>
</body>
</html>
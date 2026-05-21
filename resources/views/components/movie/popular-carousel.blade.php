
<div class="relative rounded-2xl overflow-hidden bg-[#1c1c1e] flex items-center" style="height: 450px;">

    {{-- Info Panel Kiri --}}
    <div class="absolute left-8 z-10 flex flex-col transition-all duration-500" id="infoPanel"
         style="top: 50%; transform: translateY(-50%);">
        <h2 class="text-white text-2xl font-bold mb-3">Most Popular</h2>
        <div id="rankList">
            @foreach($movies as $i => $movie)
                <div class="flex items-center gap-2 text-white text-sm mb-1.5">
                    <span class="text-yellow-400 font-semibold">{{ $i + 1 }}.</span>
                    <span>{{ $movie['title'] }}</span>
                    <span>💜</span>
                    <span class="text-gray-400 text-xs">{{ number_format($movie['popularity']) }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Stage Carousel --}}
    <button id="prevButton" onclick="prevSlide()" class="absolute left-2 z-10 bg-white/15 hover:bg-white/25 text-white border-0 w-8 h-8 rounded-full flex items-center justify-center text-lg cursor-pointer transition">&#8249;</button>
    
    <div id="carouselContainer" class="absolute top-0 bottom-0 right-0 flex items-center justify-left" style="width: 60%;">
        <div class="flex items-center scrollbar-hide transition-transform duration-[450ms] ease-[cubic-bezier(.4,0,.2,1)]" id="carouselTrack">
            @foreach($movies as $i => $movie)
                <div onclick="goToSlide({{ $i }})"
                     data-index="{{ $i }}"
                     class="poster-item shrink-0 rounded-[14px] cursor-pointer transition-all duration-[450ms] ease-[cubic-bezier(.4,0,.2,1)]
                            {{ $i === 0 ? 'w-56 h-80 opacity-100 mx-2.5 outline outline-[2.5px]' : (abs($i - 0) === 1 ? 'w-36 h-56 opacity-60 mx-1' : 'w-24 h-40 opacity-35 -mx-2') }}">
                    <img src="https://image.tmdb.org/t/p/original/{{ $movie['poster_path'] }}" class="w-full h-full rounded-[14px] object-cover" alt="{{ $movie['title'] }}">
                    <div class="p-2 text-center">
                        <p class="text-white text-xs font-semibold truncate">{{ $movie['title'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <button id="nextButton" onclick="nextSlide()" class="absolute right-2 z-10 bg-white/15 hover:bg-white/25 text-white border-0 w-8 h-8 rounded-full flex items-center justify-center text-lg cursor-pointer transition">&#8250;</button>
</div>

<script>
    let activeSlide = 0;
    const posterItems = document.querySelectorAll('.poster-item');
    const carouselTrack = document.getElementById('carouselTrack');
    const infoPanel = document.getElementById('infoPanel');
    const rankList = document.getElementById('rankList');
    const prevButton = document.getElementById('prevButton');
    const nextButton = document.getElementById('nextButton');

    // Ukuran tiap tier
    const SIZE = {
        active:   { w: 230, h: 340, mx: 10, opacity: 1 },
        side:     { w: 180, h: 260, mx: 6,  opacity: 0.6 },
        farSide:  { w: 120,  h: 180, mx: -6, opacity: 0.35 },
    };

    function getSize(dist) {
        if (dist === 0) return SIZE.active;
        if (dist === 1) return SIZE.side;
        return SIZE.farSide;
    }

    function updateCarousel() {
        let offsetX = 0; // akan dihitung untuk geser active ke posisi paling kiri

        // Hitung lebar semua card sebelum active agar bisa di-offset
        let widthBeforeActive = 0;
        posterItems.forEach((el, i) => {
            const dist = Math.abs(i - activeSlide);
            const s = getSize(dist);
            if (i < activeSlide) {
                widthBeforeActive += s.w + s.mx * 2;
            }
        });

        // Geser track agar active berada di kiri (dengan sedikit padding 16px)
        offsetX = widthBeforeActive - 0;
        carouselTrack.style.transform = `translateX(-${offsetX}px)`;

        posterItems.forEach((el, i) => {
            const dist = Math.abs(i - activeSlide);
            const s = getSize(dist);

            el.style.width   = s.w + 'px';
            el.style.height  = s.h + 'px';
            el.style.marginLeft  = s.mx + 'px';
            el.style.marginRight = s.mx + 'px';
            el.style.opacity = s.opacity;
            el.style.boxShadow = dist === 0
                ? '0 0 30px #94e2f5'
                : 'none';
            el.style.outline = dist === 0
                ? '2.5px solid #94e2f5'
                : 'none';
        });

        // Info panel: saat active bukan index 0, kecilkan dan sembunyikan rank list
        if (activeSlide > 0) {
            infoPanel.style.top = '1.5rem';
            infoPanel.style.transform = 'scale(0.78)';
            infoPanel.style.transformOrigin = 'top left';
            rankList.style.opacity = '0';
            rankList.style.transform = 'translateY(8px)';
            rankList.style.pointerEvents = 'none';
            prevButton.style.marginLeft = '0%';
        } else {
            infoPanel.style.top = '50%';
            infoPanel.style.transform = 'translateY(-50%)';
            rankList.style.opacity = '1';
            rankList.style.transform = 'translateY(0)';
            rankList.style.pointerEvents = '';
            prevButton.style.marginLeft = '100%';
        }
    }

    // Inisialisasi carousel saat halaman dimuat
    updateCarousel();

    // Handle scroll wheel
    carouselTrack.addEventListener('wheel', (e) => {
        e.preventDefault();
        if (e.deltaY > 0) {
            // Scroll down = next slide
            if (activeSlide < posterItems.length - 1) {
                activeSlide++;
                updateCarousel();
            }
        } else {
            // Scroll up = prev slide
            if (activeSlide > 0) {
                activeSlide--;
                updateCarousel();
            }
        }
    }, { passive: false });

    function prevSlide() { if (activeSlide > 0) { activeSlide--; updateCarousel(); } }
    function nextSlide() { if (activeSlide < posterItems.length - 1) { activeSlide++; updateCarousel(); } }
    function goToSlide(i) { activeSlide = i; updateCarousel(); }
</script>
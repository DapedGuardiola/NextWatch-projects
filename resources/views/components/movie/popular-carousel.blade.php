<div class="relative rounded-2xl overflow-hidden bg-[#1c1c1e] flex items-center h-[300px] md:h-[380px] lg:h-[450px]">

    {{-- Info Panel Kiri --}}
    <div class="absolute left-4 md:left-6 lg:left-8 z-10 flex flex-col transition-all duration-500" id="infoPanel"
         style="top: 50%; transform: translateY(-50%);">
        <h2 class="text-white text-lg md:text-xl lg:text-2xl font-bold mb-2 md:mb-3">All Time Best Movies</h2>
        <div id="rankList">
            @foreach($movies as $i => $movie)
                <div class="flex items-center gap-2 text-white text-xs md:text-sm mb-1 md:mb-1.5">
                    <span class="text-yellow-400 font-semibold">{{ $i + 1 }}.</span>
                    <span>{{ $movie['title'] }}</span>
                    <span>💜</span>
                    <span class="text-gray-400 text-[10px] md:text-xs">{{ number_format($movie['popularity']) }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Stage Carousel --}}
    <button id="prevButton" onclick="prevSlide()"
            class="absolute left-2 z-10 bg-white/15 hover:bg-white/25 text-white border-0
                   w-7 h-7 md:w-8 md:h-8 rounded-full flex items-center justify-center
                   text-base md:text-lg cursor-pointer transition">&#8249;</button>

    <div id="carouselContainer"
         class="absolute top-0 bottom-0 right-0 flex items-center justify-left carousel-stage">
        <div class="flex items-center scrollbar-hide transition-transform duration-[450ms] ease-[cubic-bezier(.4,0,.2,1)]" id="carouselTrack">
            @foreach($movies as $i => $movie)
                <div onclick="goToSlide({{ $i }})"
                     data-index="{{ $i }}"
                     class="poster-item shrink-0 rounded-[14px] cursor-pointer transition-all duration-[450ms] ease-[cubic-bezier(.4,0,.2,1)]">
                    <img src="https://image.tmdb.org/t/p/original/{{ $movie['poster_path'] }}" class="w-full h-full rounded-[14px] object-cover" alt="{{ $movie['title'] }}">
                    <div class="p-2 text-center">
                        <p class="text-white text-xs font-semibold truncate">{{ $movie['title'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <button id="nextButton" onclick="nextSlide()"
            class="absolute right-2 z-10 bg-white/15 hover:bg-white/25 text-white border-0
                   w-7 h-7 md:w-8 md:h-8 rounded-full flex items-center justify-center
                   text-base md:text-lg cursor-pointer transition">&#8250;</button>
</div>

<script>
    let activeSlide = 0;
    const posterItems = document.querySelectorAll('.poster-item');
    const carouselTrack = document.getElementById('carouselTrack');
    const carouselContainer = document.getElementById('carouselContainer');
    const infoPanel = document.getElementById('infoPanel');
    const rankList = document.getElementById('rankList');
    const prevButton = document.getElementById('prevButton');
    const nextButton = document.getElementById('nextButton');

    function getBreakpoint() {
        const w = window.innerWidth;
        if (w >= 1024) return 'lg';
        if (w >= 768)  return 'md';
        return 'sm';
    }

    const SIZES = {
        sm: {
            containerWidth: '70%',
            active:  { w: 140, h: 210, mx: 6,  opacity: 1 },
            side:    { w: 100, h: 155, mx: 4,  opacity: 0.6 },
            farSide: { w:  70, h: 110, mx: -4, opacity: 0.35 },
        },
        md: {
            containerWidth: '65%',
            active:  { w: 185, h: 275, mx: 8,  opacity: 1 },
            side:    { w: 140, h: 210, mx: 5,  opacity: 0.6 },
            farSide: { w:  95, h: 145, mx: -5, opacity: 0.35 },
        },
        lg: {
            containerWidth: '60%',
            active:  { w: 230, h: 340, mx: 10, opacity: 1 },
            side:    { w: 180, h: 260, mx: 6,  opacity: 0.6 },
            farSide: { w: 120, h: 180, mx: -6, opacity: 0.35 },
        },
    };

    function getSize(dist) {
        const bp = getBreakpoint();
        const S = SIZES[bp];
        if (dist === 0) return S.active;
        if (dist === 1) return S.side;
        return S.farSide;
    }

    function updateCarousel() {
        const bp = getBreakpoint();
        carouselContainer.style.width = SIZES[bp].containerWidth;

        let widthBeforeActive = 0;
        posterItems.forEach((el, i) => {
            const dist = Math.abs(i - activeSlide);
            const s = getSize(dist);
            if (i < activeSlide) {
                widthBeforeActive += s.w + s.mx * 2;
            }
        });

        carouselTrack.style.transform = `translateX(-${widthBeforeActive}px)`;

        posterItems.forEach((el, i) => {
            const dist = Math.abs(i - activeSlide);
            const s = getSize(dist);

            el.style.width        = s.w + 'px';
            el.style.height       = s.h + 'px';
            el.style.marginLeft   = s.mx + 'px';
            el.style.marginRight  = s.mx + 'px';
            el.style.opacity      = s.opacity;
            el.style.boxShadow    = dist === 0 ? '0 0 30px #94e2f5' : 'none';
            el.style.outline      = dist === 0 ? '2.5px solid #94e2f5' : 'none';
        });

        if (activeSlide > 0) {
            infoPanel.style.top             = '1.5rem';
            infoPanel.style.transform       = 'scale(0.78)';
            infoPanel.style.transformOrigin = 'top left';
            rankList.style.opacity          = '0';
            rankList.style.transform        = 'translateY(8px)';
            rankList.style.pointerEvents    = 'none';
            prevButton.style.marginLeft     = '0%';
        } else {
            infoPanel.style.top             = '50%';
            infoPanel.style.transform       = 'translateY(-50%)';
            rankList.style.opacity          = '1';
            rankList.style.transform        = 'translateY(0)';
            rankList.style.pointerEvents    = '';
            prevButton.style.marginLeft     = '100%';
        }
    }

    updateCarousel();
    window.addEventListener('resize', updateCarousel);

    carouselTrack.addEventListener('wheel', (e) => {
        e.preventDefault();
        if (e.deltaY > 0) {
            if (activeSlide < posterItems.length - 1) { activeSlide++; updateCarousel(); }
        } else {
            if (activeSlide > 0) { activeSlide--; updateCarousel(); }
        }
    }, { passive: false });

    function prevSlide() { if (activeSlide > 0) { activeSlide--; updateCarousel(); } }
    function nextSlide() { if (activeSlide < posterItems.length - 1) { activeSlide++; updateCarousel(); } }
    function goToSlide(i) { activeSlide = i; updateCarousel(); }
</script>
    <!-- Hero Section -->
    <header id="home"
        class="relative min-h-[100svh] flex items-center justify-center pt-32 pb-20 lg:py-0 overflow-hidden bg-slate-950">
        <!-- Floating Decorative Blobs -->
        <div
            class="absolute top-20 left-4 w-48 h-48 bg-blue-600/20 rounded-full blur-[80px] animate-pulse z-10 pointer-events-none">
        </div>
        <div
            class="absolute bottom-10 right-4 w-72 h-72 bg-indigo-600/10 rounded-full blur-[100px] animate-bounce-slow z-10 pointer-events-none">
        </div>

        <!-- Slider Background (Full Screen) -->
        <div class="absolute inset-0 z-0 bg-slate-950">
            <div class="swiper heroSwiper w-full h-full">
                <div class="swiper-wrapper">
                    @forelse($heroSlides as $slide)
                        <div class="swiper-slide relative bg-slate-950">
                            <img src="{{ $slide->media_url }}"
                                alt="Hero Slide"
                                class="absolute inset-0 w-full h-full object-cover object-center transform scale-105 animate-slow-zoom">
                        </div>
                    @empty
                        @if ($hero->image)
                            <div class="swiper-slide relative bg-slate-950">
                                <img src="{{ $hero->media_url }}"
                                    alt="Hero Background"
                                    class="absolute inset-0 w-full h-full object-cover object-center transform scale-105 animate-slow-zoom">
                            </div>
                        @else
                            <div class="swiper-slide relative bg-slate-950">
                                <img src="https://images.unsplash.com/photo-1497215728101-856f4ea42174?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80"
                                    alt="Default Hero Background"
                                    class="absolute inset-0 w-full h-full object-cover object-center transform scale-105 animate-slow-zoom opacity-40">
                            </div>
                        @endif
                    @endforelse
                </div>
            </div>
            <!-- Gradient Overlay for Readability - Darker on small screens -->
            <div
                class="absolute inset-0 bg-slate-950/70 lg:bg-transparent lg:bg-gradient-to-r lg:from-slate-950 lg:via-slate-950/80 lg:to-slate-950/20 z-10 pointer-events-none">
            </div>
        </div>

        <div class="container mx-auto px-6 md:px-8 relative z-20">
            <div class="max-w-5xl" data-aos="fade-up" data-aos-duration="1200">
                <div class="flex items-center space-x-4 mb-6 md:mb-8">
                    <span class="w-10 md:w-16 h-[2px] bg-blue-500"></span>
                    <span
                        class="text-blue-400 font-bold uppercase tracking-[0.3em] md:tracking-[0.4em] text-[10px] md:text-sm shadow-sm">Digital
                        Agency</span>
                </div>

                <h1
                    class="text-4xl sm:text-6xl lg:text-[7rem] font-black mb-6 md:mb-10 leading-[1.1] md:leading-[0.95] tracking-tighter">
                    <span
                        class="text-white drop-shadow-2xl">{{ app()->getLocale() == 'id' ? 'Solusi' : 'Creative' }}</span><br>
                    <span
                        class="gradient-text drop-shadow-xl">{{ app()->getLocale() == 'id' ? 'Digital Kreatif' : 'Digital Solution' }}</span>
                </h1>

                <p
                    class="text-sm sm:text-xl lg:text-2xl text-slate-300 mb-8 md:mb-12 max-w-2xl leading-relaxed font-light">
                    {{ $hero->getTranslation('subtitle') ?? 'We transform your ideas into extraordinary digital experiences that push boundaries.' }}
                </p>

                <div class="flex flex-col sm:flex-row gap-4 sm:gap-6 items-stretch sm:items-center">
                    <a href="{{ $hero->cta_link ?? '#contact' }}"
                        class="group relative px-7 py-4 sm:px-10 sm:py-5 bg-blue-600 text-white rounded-2xl font-black text-base md:text-lg overflow-hidden transition-all hover:bg-blue-700 shadow-[0_20px_50px_rgba(37,99,235,0.4)] w-full sm:w-auto text-center">
                        <span
                            class="relative z-10 tracking-wide">{{ $hero->getTranslation('cta_text') ?? 'Start Project' }}</span>
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000">
                        </div>
                    </a>

                    <div class="hidden sm:flex items-center space-x-4">
                        <div class="flex -space-x-4">
                            <div
                                class="w-12 h-12 rounded-full border-2 border-slate-900 bg-slate-800 flex items-center justify-center font-bold text-xs text-blue-400">
                                JK</div>
                            <div
                                class="w-12 h-12 rounded-full border-2 border-slate-900 bg-slate-800 flex items-center justify-center font-bold text-xs text-indigo-400">
                                MA</div>
                            <div
                                class="w-12 h-12 rounded-full border-2 border-slate-900 bg-slate-800 flex items-center justify-center font-bold text-xs text-cyan-400">
                                +{{ $clients->count() }}</div>
                        </div>
                        <div class="text-sm text-slate-400">
                            <span
                                class="text-white font-bold">{{ app()->getLocale() == 'id' ? 'Klien' : 'Clients' }}</span><br>
                            {{ app()->getLocale() == 'id' ? 'Puas Bergabung' : 'Happy Joining' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-10 left-1/2 -translate-x-1/2 z-20 animate-bounce hidden lg:block">
            <div class="w-6 h-10 rounded-full border-2 border-white/20 flex justify-center pt-2">
                <div class="w-1 h-2 bg-blue-500 rounded-full"></div>
            </div>
        </div>
    </header>

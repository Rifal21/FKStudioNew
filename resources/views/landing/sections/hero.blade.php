    <!-- Hero Section -->
    <header id="home"
        class="relative min-h-[100svh] flex items-center overflow-hidden bg-slate-50 dark:bg-slate-950 transition-colors duration-500">
        
        <!-- Swiper Background Slider (Full-Bleed, Highly Visible on Right half) -->
        <div class="absolute inset-0 w-full h-full z-10">
            <div class="swiper heroSwiper w-full h-full">
                <div class="swiper-wrapper">
                    @forelse($heroSlides as $slide)
                        <div class="swiper-slide h-full w-full relative">
                            <img src="{{ $slide->media_url }}"
                                alt="FKStudio Premium Showcase"
                                class="w-full h-full object-cover object-center transform scale-100">
                        </div>
                    @empty
                        @if ($hero->image)
                            <div class="swiper-slide h-full w-full relative">
                                <img src="{{ $hero->media_url }}"
                                    alt="FKStudio Premium Showcase"
                                    class="w-full h-full object-cover object-center transform scale-100">
                            </div>
                        @else
                            <div class="swiper-slide h-full w-full relative">
                                <img src="https://images.unsplash.com/photo-1497215728101-856f4ea42174?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80"
                                    alt="Default Showcase"
                                    class="w-full h-full object-cover object-center">
                            </div>
                        @endif
                    @endforelse
                </div>
            </div>

            <!-- Master Horizontal Gradient Overlay: Blends background image seamlessly into the solid wording canvas -->
            <div class="absolute inset-0 bg-gradient-to-r from-slate-50 via-slate-50/95 to-slate-50/30 dark:from-slate-950 dark:via-slate-950/95 dark:to-slate-950/30 lg:from-slate-50 lg:via-slate-50/80 lg:to-transparent dark:lg:from-slate-950 dark:lg:via-slate-950/80 dark:lg:to-transparent z-20 pointer-events-none transition-colors duration-500"></div>
        </div>

        <!-- Left Side: Content Container -->
        <div class="container mx-auto px-6 md:px-8 relative z-30 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                <!-- Left Column: Elegant Copywriting & Navigation -->
                <div class="lg:col-span-6.5 xl:col-span-6 space-y-8 py-20 lg:py-32" data-aos="fade-right" data-aos-duration="1200">

                    {{-- badge text --}}
                    <div class="inline-flex items-center space-x-2 bg-white/80 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 rounded-full px-4 py-2">
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        <span class="text-xs font-medium text-slate-700 dark:text-slate-300">FKStudio IT Solution Partner</span>
                    </div>
                    <!-- Elegant H1 Headline -->
                    <h1 class="text-4xl sm:text-5xl lg:text-[4.5rem] xl:text-[5rem] font-black leading-[1.1] tracking-tight text-slate-900 dark:text-white">
                        <span class="gradient-text">{{ $hero->getTranslation('title') }}</span>
                    </h1>

                    <!-- Elegant Subtext -->
                    <p class="text-base sm:text-lg lg:text-xl text-slate-650 dark:text-slate-300 max-w-xl leading-relaxed font-medium">
                        {{ $hero->getTranslation('subtitle') ?? (app()->getLocale() == 'id' ? 'Kami menghadirkan jasa pembuatan website custom, platform e-commerce, dan aplikasi mobile berkinerja tinggi yang dirancang khusus untuk mempercepat pertumbuhan bisnis Anda.' : 'We deliver premium custom website development, robust e-commerce platforms, and high-performance mobile applications tailored to accelerate your business growth.') }}
                    </p>

                    <!-- Elegant CTA Action Buttons & Swiper Navigation -->
                    <div class="flex flex-col sm:flex-row gap-4 items-stretch sm:items-center pt-2">
                        <!-- Primary CTA: Start Consultation -->
                        <a href="{{ $hero->cta_link ?? '/products' }}"
                            class="group relative px-8 py-4 bg-blue-600 text-white rounded-xl font-bold text-sm uppercase tracking-wider overflow-hidden transition-all duration-300 hover:bg-blue-700 shadow-lg shadow-blue-600/20 hover:shadow-blue-600/30 hover:-translate-y-0.5 w-full sm:w-auto text-center flex items-center justify-center gap-3">
                            <span class="relative z-10">{{ $hero->getTranslation('cta_text') ?? (app()->getLocale() == 'id' ? 'Mulai Konsultasi' : 'Start Consultation') }}</span>
                            <i class="fa-solid fa-arrow-right text-xs relative z-10 group-hover:translate-x-1 transition-transform duration-300"></i>
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/15 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                        </a>

                        <!-- Secondary CTA: View Portfolio -->
                        <a href="#portfolio"
                            class="group px-8 py-4 border border-slate-200 dark:border-white hover:border-blue-500/40 text-slate-700 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 rounded-xl font-bold text-sm uppercase tracking-wider transition-all duration-300 hover:-translate-y-0.5 w-full sm:w-auto flex items-center justify-center gap-3">
                            <span>{{ app()->getLocale() == 'id' ? 'Lihat Portofolio' : 'View Portfolio' }}</span>
                        </a>

                        <!-- Interactive Banner Navigation Arrows -->
                        <div class="flex items-center space-x-2.5 justify-center sm:justify-start mt-2 sm:mt-0 sm:ml-2">
                            <button class="hero-prev-btn w-12 h-12 rounded-xl border border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/80 text-slate-700 dark:text-slate-300 flex items-center justify-center hover:bg-blue-600 dark:hover:bg-blue-600 hover:text-white transition-all duration-300 shadow-sm active:scale-95 cursor-pointer">
                                <i class="fa-solid fa-chevron-left text-xs"></i>
                            </button>
                            <button class="hero-next-btn w-12 h-12 rounded-xl border border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/80 text-slate-700 dark:text-slate-300 flex items-center justify-center hover:bg-blue-600 dark:hover:bg-blue-600 hover:text-white transition-all duration-300 shadow-sm active:scale-95 cursor-pointer">
                                <i class="fa-solid fa-chevron-right text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Trust Metrics / Clients Partners -->
                    <div class="flex items-center space-x-4 pt-4 border-t border-slate-200/50 dark:border-slate-800 max-w-md">
                        <div class="flex -space-x-3">
                            <div class="w-10 h-10 rounded-full border-2 border-white dark:border-slate-950 bg-slate-100 dark:bg-slate-800 flex items-center justify-center font-bold text-[10px] text-blue-600 shadow-sm">JK</div>
                            <div class="w-10 h-10 rounded-full border-2 border-white dark:border-slate-950 bg-slate-100 dark:bg-slate-800 flex items-center justify-center font-bold text-[10px] text-indigo-600 shadow-sm">MA</div>
                            <div class="w-10 h-10 rounded-full border-2 border-white dark:border-slate-950 bg-slate-100 dark:bg-slate-800 flex items-center justify-center font-bold text-[10px] text-cyan-600 shadow-sm">+{{ $clients->count() }}</div>
                        </div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 leading-snug">
                            <span class="text-slate-800 dark:text-slate-200 font-extrabold">{{ app()->getLocale() == 'id' ? 'Mitra Bisnis Terpercaya' : 'Trusted Business Partners' }}</span><br>
                            {{ app()->getLocale() == 'id' ? 'Telah berkolaborasi dengan brand terbaik.' : 'Collaborated with premium brands.' }}
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Scroll Indicator (Centered on Left Column) -->
        <div class="absolute bottom-8 left-[50%] -translate-x-1/2 z-20 animate-bounce hidden lg:block">
            <div class="w-5 h-8 rounded-full border-2 border-slate-350 dark:border-slate-700 flex justify-center pt-1.5">
                <div class="w-1.5 h-1.5 bg-blue-600 rounded-full"></div>
            </div>
        </div>

    </header>

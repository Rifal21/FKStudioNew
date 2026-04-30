    <!-- Services Section -->
    <section id="services" class="py-24 md:py-40 relative bg-slate-950 overflow-hidden">
        <!-- Background Accents -->
        <div class="absolute top-0 left-1/4 w-[500px] h-[500px] bg-blue-600/5 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-0 right-1/4 w-[500px] h-[500px] bg-indigo-600/5 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-[0.03] pointer-events-none"></div>

        <div class="container mx-auto px-6 relative z-10">
            <div class="text-center max-w-4xl mx-auto mb-20 md:mb-32">
                <div class="inline-flex items-center space-x-3 mb-6" data-aos="fade-up">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    <span class="text-blue-500 font-black uppercase tracking-[0.4em] text-[10px] md:text-xs">
                        {{ app()->getLocale() == 'id' ? 'Layanan Unggulan' : 'Premium Services' }}
                    </span>
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                </div>
                <h2 class="text-5xl md:text-7xl lg:text-8xl font-black text-white mb-8 tracking-tighter leading-none" data-aos="fade-up" data-aos-delay="100">
                    {{ app()->getLocale() == 'id' ? 'Keahlian' : 'Core' }} <span class="gradient-text">{{ app()->getLocale() == 'id' ? 'Kami.' : 'Expertise.' }}</span>
                </h2>
                <p class="text-lg md:text-xl text-slate-400 max-w-2xl mx-auto font-medium" data-aos="fade-up" data-aos-delay="200">
                    Kami menghadirkan solusi digital terintegrasi yang menggabungkan strategi kreatif dengan teknologi mutakhir.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($services as $index => $service)
                    <div class="group relative" data-aos="fade-up" data-aos-delay="{{ 100 * ($index % 3) }}">
                        <!-- Card Glow Backdrop -->
                        <div class="absolute -inset-2 bg-gradient-to-br from-blue-600/20 to-indigo-600/20 rounded-[3rem] blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
                        
                        <div class="relative h-full glass p-10 md:p-12 rounded-[3rem] border border-white/5 flex flex-col transition-all duration-500 group-hover:translate-y-[-15px] group-hover:border-blue-500/30 overflow-hidden">
                            <!-- Decorative Number -->
                            <span class="absolute top-8 right-10 text-6xl font-black text-white/[0.03] group-hover:text-blue-500/10 transition-colors duration-500 select-none italic">
                                {{ sprintf("%02d", $index + 1) }}
                            </span>

                            <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center mb-10 shadow-2xl shadow-blue-500/40 transform group-hover:rotate-[10deg] transition-all duration-500">
                                <i class="{{ $service->icon ?? 'fa-solid fa-rocket' }} text-2xl md:text-3xl text-white"></i>
                            </div>

                            <h3 class="text-2xl md:text-3xl font-black mb-5 text-white group-hover:text-blue-400 transition-colors duration-500 leading-tight">
                                {{ $service->getTranslation('title') }}
                            </h3>
                            
                            <p class="text-base md:text-lg text-slate-400 leading-relaxed mb-10 flex-grow font-medium group-hover:text-slate-300 transition-colors duration-500">
                                {{ $service->getTranslation('description') }}
                            </p>

                            <div class="flex items-center space-x-3 text-white/40 group-hover:text-blue-500 transition-all duration-500">
                                <span class="text-[10px] font-black uppercase tracking-[0.3em]">{{ app()->getLocale() == 'id' ? 'Pelajari Lebih Lanjut' : 'Learn More' }}</span>
                                <div class="h-px w-8 bg-current transform origin-left group-hover:scale-x-150 transition-transform duration-500"></div>
                                <i class="fa-solid fa-arrow-right text-[10px] transform group-hover:translate-x-2 transition-transform duration-500"></i>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

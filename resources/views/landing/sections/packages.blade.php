    <!-- Packages Section -->
    <section id="packages" class="py-24 md:py-40 relative bg-slate-50 dark:bg-slate-950 overflow-hidden transition-colors duration-500">
        <!-- Background Accents -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-blue-300/10 rounded-full blur-[150px] pointer-events-none"></div>
        
        <div class="container mx-auto px-6 relative z-10">
            <div class="text-center max-w-4xl mx-auto mb-20 md:mb-32">
                <div class="inline-flex items-center space-x-3 mb-6" data-aos="fade-up">
                    <span class="w-12 h-[1px] bg-blue-600/30"></span>
                    <span class="text-blue-600 dark:text-blue-400 font-black uppercase tracking-[0.4em] text-[10px] md:text-xs">
                        {{ app()->getLocale() == 'id' ? 'Investasi Digital' : 'Digital Investment' }}
                    </span>
                    <span class="w-12 h-[1px] bg-blue-600/30"></span>
                </div>
                <h2 class="text-5xl md:text-7xl lg:text-8xl font-black text-slate-900 dark:text-white mb-8 tracking-tighter leading-none" data-aos="fade-up" data-aos-delay="100">
                    {{ app()->getLocale() == 'id' ? 'Pilih' : 'Choose' }} <span class="gradient-text">{{ app()->getLocale() == 'id' ? 'Paket Anda.' : 'Your Plan.' }}</span>
                </h2>
                <p class="text-lg md:text-xl text-slate-600 dark:text-slate-350 max-w-2xl mx-auto font-medium" data-aos="fade-up" data-aos-delay="200">
                    Kami menawarkan paket fleksibel yang dirancang untuk mendukung pertumbuhan bisnis Anda di setiap tahap.
                </p>
            </div>

            <div class="swiper packagesSwiper !overflow-visible">
                <div class="swiper-wrapper !items-stretch">
                    @foreach ($packages as $index => $package)
                        <div class="swiper-slide h-auto py-10">
                            <div class="group relative h-full" data-aos="fade-up" data-aos-delay="{{ $loop->index * 150 }}">
                                @if ($package->is_featured)
                                    <div class="absolute -top-6 left-1/2 -translate-x-1/2 z-20">
                                        <span class="px-6 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-[10px] font-black uppercase tracking-widest rounded-full shadow-[0_10px_30px_rgba(37,99,235,0.25)] border border-white/20">
                                            {{ app()->getLocale() == 'id' ? 'Paling Populer' : 'Most Popular' }}
                                        </span>
                                    </div>
                                @endif

                                <!-- Card Glow Backdrop -->
                                <div class="absolute -inset-1 bg-gradient-to-b {{ $package->is_featured ? 'from-blue-600/15 to-indigo-600/15 shadow-xl' : 'from-slate-200/20 to-transparent dark:from-slate-800/10' }} rounded-[3.5rem] blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>

                                <div class="relative h-full flex flex-col p-10 md:p-14 rounded-[3.5rem] {{ $package->is_featured ? 'bg-white dark:bg-slate-900 border-blue-500 dark:border-blue-400 ring-1 ring-blue-500/20 dark:ring-blue-400/20 scale-105 shadow-2xl dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] z-10' : 'bg-white dark:bg-white/[0.02] border-slate-200/60 dark:border-white/5 shadow-md dark:shadow-lg hover:shadow-xl' }} border backdrop-blur-xl transition-all duration-700 group-hover:translate-y-[-12px] transition-colors duration-500">
                                    
                                    <div class="mb-10">
                                        <h3 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-slate-200 mb-4 tracking-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $package->getTranslation('name') }}</h3>
                                        <div class="flex items-baseline gap-2">
                                            <span class="text-2xl md:text-3xl font-black text-blue-600 dark:text-blue-400 tracking-tighter">{{ $package->price }}</span>
                                            @if(is_numeric(str_replace(['Rp', '.', ','], '', $package->price)))
                                                <span class="text-slate-400 text-sm font-bold uppercase tracking-widest">/ Project</span>
                                            @endif
                                        </div>
                                    </div>

                                    <p class="text-slate-500 dark:text-slate-400 text-sm md:text-base leading-relaxed mb-10 font-medium">
                                        {{ $package->getTranslation('description') }}
                                    </p>

                                    <div class="w-full h-px bg-gradient-to-r from-transparent via-slate-100 dark:via-slate-800 to-transparent mb-10"></div>

                                    <ul class="space-y-6 mb-12 flex-grow">
                                        @php
                                            $features = app()->getLocale() == 'id' ? $package->features_id : $package->features_en;
                                        @endphp
                                        @foreach ($features ?? [] as $feature)
                                            <li class="flex items-start gap-4 text-sm md:text-base text-slate-600 dark:text-slate-350 group/item">
                                                <div class="mt-1 flex-shrink-0 w-5 h-5 rounded-full bg-blue-55 bg-blue-50 dark:bg-blue-950/50 border border-blue-100 dark:border-blue-900/50 flex items-center justify-center group-hover/item:bg-blue-600 transition-colors duration-300">
                                                    <i class="fa-solid fa-check text-[8px] text-blue-500 group-hover/item:text-white transition-colors"></i>
                                                </div>
                                                <span class="group-hover/item:text-slate-900 dark:group-hover/item:text-white transition-colors font-medium">{{ $feature }}</span>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <a href="{{ route('package.show', $package->slug) }}"
                                        class="relative group/btn block w-full py-5 text-center rounded-2xl font-black text-sm md:text-base uppercase tracking-widest transition-all overflow-hidden {{ $package->is_featured ? 'bg-blue-600 text-white shadow-2xl shadow-blue-600/30' : 'bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
                                        <span class="relative z-10">{{ app()->getLocale() == 'id' ? 'Lihat Detail' : 'View Details' }}</span>
                                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover/btn:translate-x-full transition-transform duration-1000"></div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Custom Quote Note -->
            <div class="mt-24 text-center" data-aos="fade-up">
                <p class="text-slate-600 dark:text-slate-400 font-medium italic">
                    {{ app()->getLocale() == 'id' ? 'Punya kebutuhan khusus? Kami siap membuat penawaran kustom untuk Anda.' : 'Have special requirements? We are ready to create a custom offer for you.' }}
                    <a href="#contact" class="text-blue-600 dark:text-blue-400 font-black not-italic ml-2 hover:underline">Chat with us.</a>
                </p>
            </div>
        </div>
    </section>

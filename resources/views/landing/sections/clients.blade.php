@if ($clients->isNotEmpty())
    <!-- Clients Section -->
    <section class="py-20 md:py-32 overflow-hidden relative bg-slate-50 dark:bg-slate-950 border-y border-slate-200/30 dark:border-white/5 transition-colors duration-500 bg-dots">
        
        <!-- Local Custom Styles -->
        <style>
            .bg-dots {
                background-image: radial-gradient(rgba(148, 163, 184, 0.08) 1.5px, transparent 1.5px);
                background-size: 24px 24px;
            }
            .dark .bg-dots {
                background-image: radial-gradient(rgba(51, 65, 85, 0.15) 1.5px, transparent 1.5px);
            }

            @keyframes marquee-forward-custom {
                0% { transform: translateX(0); }
                100% { transform: translateX(-50%); }
            }
            @keyframes marquee-reverse-custom {
                0% { transform: translateX(-50%); }
                100% { transform: translateX(0); }
            }

            .animate-marquee-forward-custom {
                animation: marquee-forward-custom 45s linear infinite;
            }
            .animate-marquee-reverse-custom {
                animation: marquee-reverse-custom 45s linear infinite;
            }

            /* Pause on hover for easier visual inspection of brands */
            .group-marquee:hover .animate-marquee-forward-custom,
            .group-marquee:hover .animate-marquee-reverse-custom {
                animation-play-state: paused;
            }
        </style>

        <!-- Decorative Ambient Background Gradients -->
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-500/5 dark:bg-blue-600/5 rounded-full filter blur-[100px] pointer-events-none -z-10 animate-pulse"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-indigo-500/5 dark:bg-indigo-600/5 rounded-full filter blur-[100px] pointer-events-none -z-10 animate-pulse" style="animation-delay: 2.5s;"></div>

        <!-- Section Header -->
        <div class="container mx-auto px-6 relative z-10 mb-16 md:mb-24 text-center">
            <div class="inline-flex items-center space-x-4 mb-4" data-aos="fade-up">
                <span class="h-px w-8 md:w-12 bg-blue-600/30"></span>
                <p class="text-blue-600 dark:text-blue-400 text-[10px] md:text-xs font-black uppercase tracking-[0.5em] bg-blue-50/50 dark:bg-blue-950/30 px-3 py-1 rounded-full border border-blue-500/10">
                    {{ app()->getLocale() == 'id' ? 'Kemitraan Strategis' : 'Strategic Partnerships' }}
                </p>
                <span class="h-px w-8 md:w-12 bg-blue-600/30"></span>
            </div>
            
            <h2 class="text-3xl md:text-5xl font-black text-slate-800 dark:text-slate-200 tracking-tight leading-tight" data-aos="fade-up" data-aos-delay="100">
                {{ app()->getLocale() == 'id' ? 'Dipercaya Oleh' : 'Trusted By' }}
                <span class="gradient-text">{{ app()->getLocale() == 'id' ? 'Brand Terkemuka' : 'Industry Leaders' }}</span>
            </h2>
            <p class="mt-4 text-sm md:text-base text-slate-500 dark:text-slate-400 max-w-2xl mx-auto font-medium" data-aos="fade-up" data-aos-delay="150">
                {{ app()->getLocale() == 'id' ? 'Berkolaborasi dengan visi yang sama untuk menghasilkan produk digital kelas dunia dan pertumbuhan bisnis yang berdampak luas.' : 'Collaborating with visionaries to deliver world-class digital products and scalable business growth globally.' }}
            </p>
        </div>

        <!-- Logo Carousels Container -->
        <div class="relative group-marquee space-y-6 md:space-y-8">
            <!-- Sleek Edge Masking Gradients -->
            <div class="absolute inset-y-0 left-0 w-32 md:w-80 bg-gradient-to-r from-slate-50 via-slate-50/80 to-transparent dark:from-slate-950 dark:via-slate-950/80 dark:to-transparent z-20 pointer-events-none"></div>
            <div class="absolute inset-y-0 right-0 w-32 md:w-80 bg-gradient-to-l from-slate-50 via-slate-50/80 to-transparent dark:from-slate-950 dark:via-slate-950/80 dark:to-transparent z-20 pointer-events-none"></div>

            <!-- Track 1: Right to Left -->
            <div class="relative overflow-hidden py-2">
                <div class="flex gap-6 md:gap-8 items-center animate-marquee-forward-custom whitespace-nowrap w-max">
                    @php 
                        // Repeat clients enough to ensure smooth continuous loop
                        $repeatedForward = $clients->concat($clients)->concat($clients)->concat($clients)->concat($clients);
                    @endphp
                    @foreach ($repeatedForward as $client)
                        <div class="flex-shrink-0 px-2">
                            <div class="relative group/logo flex items-center justify-center bg-white/60 dark:bg-white/[0.02] backdrop-blur-md px-6 py-4 md:px-10 md:py-6 rounded-2xl shadow-sm hover:shadow-xl hover:shadow-blue-500/5 dark:hover:shadow-blue-500/2 hover:-translate-y-1 transition-all duration-500 w-36 sm:w-44 md:w-56 h-16 sm:h-20 md:h-24 overflow-hidden cursor-pointer">
                                <img src="{{ $client->media_url }}" 
                                     alt="{{ $client->name ?? 'Client' }}"
                                     class="h-7 sm:h-8 md:h-11 w-auto max-w-[85%] object-contain opacity-95 dark:opacity-90 group-hover/logo:opacity-100 group-hover/logo:scale-105 transition-all duration-500 relative z-10">
                                
                                <!-- Hover Internal Card Glow -->
                                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 via-transparent to-indigo-500/5 opacity-0 group-hover/logo:opacity-100 transition-opacity duration-500 z-0"></div>
                                <!-- Subtle hover backglow -->
                                <div class="absolute -bottom-10 -left-10 w-28 h-28 bg-blue-500/10 dark:bg-blue-500/5 blur-2xl rounded-full opacity-0 group-hover/logo:opacity-100 transition-opacity duration-500 -z-10"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Track 2: Left to Right (Opposing direction) -->
            <div class="relative overflow-hidden py-2">
                <div class="flex gap-6 md:gap-8 items-center animate-marquee-reverse-custom whitespace-nowrap w-max">
                    @php 
                        // Reverse client collection and repeat to diversify ordering and direction
                        $clientsReversed = $clients->reverse();
                        $repeatedReverse = $clientsReversed->concat($clientsReversed)->concat($clientsReversed)->concat($clientsReversed)->concat($clientsReversed);
                    @endphp
                    @foreach ($repeatedReverse as $client)
                        <div class="flex-shrink-0 px-2">
                            <div class="relative group/logo flex items-center justify-center bg-white/60 dark:bg-white/[0.02] backdrop-blur-md px-6 py-4 md:px-10 md:py-6 rounded-2xl shadow-sm hover:shadow-xl hover:shadow-blue-500/5 dark:hover:shadow-blue-500/2 hover:-translate-y-1 transition-all duration-500 w-36 sm:w-44 md:w-56 h-16 sm:h-20 md:h-24 overflow-hidden cursor-pointer">
                                <img src="{{ $client->media_url }}" 
                                     alt="{{ $client->name ?? 'Client' }}"
                                     class="h-7 sm:h-8 md:h-11 w-auto max-w-[85%] object-contain opacity-95 dark:opacity-90 group-hover/logo:opacity-100 group-hover/logo:scale-105 transition-all duration-500 relative z-10">
                                
                                <!-- Hover Internal Card Glow -->
                                <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 via-transparent to-blue-500/5 opacity-0 group-hover/logo:opacity-100 transition-opacity duration-500 z-0"></div>
                                <!-- Subtle hover backglow -->
                                <div class="absolute -bottom-10 -left-10 w-28 h-28 bg-indigo-500/10 dark:bg-indigo-500/5 blur-2xl rounded-full opacity-0 group-hover/logo:opacity-100 transition-opacity duration-500 -z-10"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Trust & Credibility Metrics Grid -->
        <div class="mt-16 md:mt-24 max-w-5xl mx-auto px-6 relative z-10" data-aos="fade-up" data-aos-delay="200">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 md:gap-8">
                <!-- Metric 1 -->
                <div class="relative group p-6 rounded-2xl bg-white/50 dark:bg-slate-900/40 border border-slate-200/40 dark:border-white/5 backdrop-blur-md hover:border-blue-500/20 dark:hover:border-blue-500/20 transition-all duration-500 shadow-sm">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-2xl"></div>
                    <div class="flex items-center space-x-4 relative z-10">
                        <div class="w-12 h-12 rounded-xl bg-blue-500/10 dark:bg-blue-500/5 flex items-center justify-center text-blue-600 dark:text-blue-400 group-hover:scale-110 transition-transform duration-500">
                            <i class="fa-solid fa-face-smile text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-2xl font-black text-slate-800 dark:text-slate-100">99%</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium tracking-wide">
                                {{ app()->getLocale() == 'id' ? 'Tingkat Kepuasan Klien' : 'Client Satisfaction Rate' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Metric 2 -->
                <div class="relative group p-6 rounded-2xl bg-white/50 dark:bg-slate-900/40 border border-slate-200/40 dark:border-white/5 backdrop-blur-md hover:border-indigo-500/20 dark:hover:border-indigo-500/20 transition-all duration-500 shadow-sm">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-2xl"></div>
                    <div class="flex items-center space-x-4 relative z-10">
                        <div class="w-12 h-12 rounded-xl bg-indigo-500/10 dark:bg-indigo-500/5 flex items-center justify-center text-indigo-600 dark:text-indigo-400 group-hover:scale-110 transition-transform duration-500">
                            <i class="fa-solid fa-code text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-2xl font-black text-slate-800 dark:text-slate-100">{{$clients->count()}}</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium tracking-wide">
                                {{ app()->getLocale() == 'id' ? 'Proyek Selesai' : 'Completed Projects' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Metric 3 -->
                <div class="relative group p-6 rounded-2xl bg-white/50 dark:bg-slate-900/40 border border-slate-200/40 dark:border-white/5 backdrop-blur-md hover:border-cyan-500/20 dark:hover:border-cyan-500/20 transition-all duration-500 shadow-sm">
                    <div class="absolute inset-0 bg-gradient-to-br from-cyan-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-2xl"></div>
                    <div class="flex items-center space-x-4 relative z-10">
                        <div class="w-12 h-12 rounded-xl bg-cyan-500/10 dark:bg-cyan-500/5 flex items-center justify-center text-cyan-600 dark:text-cyan-400 group-hover:scale-110 transition-transform duration-500">
                            <i class="fa-solid fa-clock-rotate-left text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-2xl font-black text-slate-800 dark:text-slate-100">92%</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium tracking-wide">
                                {{ app()->getLocale() == 'id' ? 'Retensi Kemitraan' : 'Client Retention Rate' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Global Partner Badge -->
            <div class="mt-10 text-center">
                <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/60 dark:bg-slate-900/60 border border-slate-200/50 dark:border-white/5 backdrop-blur-sm text-[10px] md:text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wider uppercase shadow-sm">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 mr-2.5 animate-pulse"></span>
                    {{ $clients->count() }} {{ app()->getLocale() == 'id' ? 'Mitra Global Aktif' : 'Active Global Partners' }}
                </span>
            </div>
        </div>

    </section>
@endif

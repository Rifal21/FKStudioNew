    @if ($clients->isNotEmpty())
        <!-- Clients Section -->
        <section class="py-16 md:py-28 overflow-hidden relative">
            <!-- Decorative Background Element -->
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-px bg-gradient-to-r from-transparent via-blue-500/10 to-transparent"></div>
            
            <div class="container mx-auto px-6 relative z-10 mb-12 md:mb-20 text-center">
                <div class="inline-flex items-center space-x-4 mb-4" data-aos="fade-up">
                    <span class="h-px w-8 md:w-12 bg-blue-600/30"></span>
                    <p class="text-blue-500 text-[10px] md:text-xs font-black uppercase tracking-[0.5em]">
                        {{ app()->getLocale() == 'id' ? 'Kemitraan Strategis' : 'Strategic Partnerships' }}
                    </p>
                    <span class="h-px w-8 md:w-12 bg-blue-600/30"></span>
                </div>
                <h2 class="text-2xl md:text-4xl font-black text-white/90 tracking-tight" data-aos="fade-up" data-aos-delay="100">
                    {{ app()->getLocale() == 'id' ? 'Dipercaya Oleh' : 'Trusted By' }}
                </h2>
            </div>

            <!-- Logo Carousel Container -->
            <div class="relative group">
                <!-- Edge Masking -->
                <div class="absolute inset-y-0 left-0 w-24 md:w-64 bg-gradient-to-r from-slate-950 to-transparent z-20 pointer-events-none"></div>
                <div class="absolute inset-y-0 right-0 w-24 md:w-64 bg-gradient-to-l from-slate-950 to-transparent z-20 pointer-events-none"></div>

                <!-- Glass Strip -->
                <div class="py-8 md:py-12 bg-white/[0.02] border-y border-white/5 backdrop-blur-sm">
                    <div class="flex gap-16 md:gap-32 items-center animate-marquee-simple whitespace-nowrap">
                        @php 
                            // Duplicate enough times to ensure seamless loop
                            $repeatedClients = $clients->concat($clients)->concat($clients)->concat($clients);
                        @endphp
                        @foreach ($repeatedClients as $client)
                            <div class="flex-shrink-0 px-4 md:px-8 group/logo transition-all duration-700">
                                <div class="relative">
                                    <img src="{{ $client->media_url }}" 
                                         alt="{{ $client->name ?? 'Client' }}"
                                         class="h-8 md:h-16 w-auto object-contain filter brightness-125 group-hover/logo:grayscale-0 group-hover/logo:opacity-100 group-hover/logo:scale-110 transition-all duration-500 brightness-125">
                                    
                                    <!-- Subtle Glow on Hover -->
                                    <div class="absolute inset-0 bg-blue-500/20 blur-2xl rounded-full opacity-0 group-hover/logo:opacity-100 transition-opacity duration-500 -z-10"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Bottom Note -->
            <div class="mt-10 text-center" data-aos="fade-up">
                <span class="text-[9px] md:text-[10px] font-bold text-slate-500 uppercase tracking-[0.3em]">
                    ❤️ {{ $clients->count() }} {{ app()->getLocale() == 'id' ? 'Klien Telah Bergabung' : 'Clients Have Joined' }}
                </span>
            </div>
        </section>
    @endif

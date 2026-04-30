    <!-- Unified About & Team Section -->
    <section id="about" class="relative lg:h-screen flex flex-col lg:flex-row bg-slate-950 overflow-hidden">
        <!-- Left Side: Cinematic Slider (Sticky on Desktop) -->
        <div class="w-full lg:w-1/2 relative h-[45vh] md:h-[55vh] lg:h-full overflow-hidden flex-shrink-0 lg:sticky lg:top-0 order-1 lg:order-1">
            <div class="swiper aboutSwiper h-full w-full">
                <div class="swiper-wrapper">
                    @forelse ($aboutSlides as $aslide)
                        <div class="swiper-slide h-full">
                            <img src="{{ $aslide->media_url }}" class="w-full h-full object-cover">
                        </div>
                    @empty
                        <div class="swiper-slide h-full">
                            <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=1200&q=80" class="w-full h-full object-cover">
                        </div>
                    @endforelse
                </div>
            </div>
            
            <!-- Dynamic Gradient Overlays -->
            <div class="absolute inset-0 z-10 bg-gradient-to-t from-slate-950 via-slate-950/20 to-transparent lg:hidden"></div>
            <div class="absolute inset-0 z-10 hidden lg:block bg-gradient-to-r from-transparent via-transparent to-slate-950"></div>
            <div class="absolute inset-0 z-10 hidden lg:block bg-gradient-to-b from-slate-950/30 via-transparent to-slate-950/30"></div>
            
            <!-- Slider Navigation Controls -->
            <div class="absolute bottom-10 left-10 z-20 flex items-center space-x-4">
                <div class="about-pagination flex items-center space-x-2"></div>
                <div class="h-px w-12 bg-white/20 hidden md:block"></div>
                <span class="text-[10px] font-black uppercase tracking-[0.4em] text-white/40 hidden md:block">Discovery</span>
            </div>

            <!-- Floating Badge -->
            <div class="absolute top-10 left-10 z-20 hidden lg:block">
                <div class="glass px-6 py-3 rounded-2xl flex items-center space-x-3">
                    <div class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-white">Est. 2021</span>
                </div>
            </div>
        </div>

        <!-- Right Side: Content & Team (Scrollable on Desktop) -->
        <div class="w-full lg:w-1/2 bg-slate-950 lg:overflow-y-auto lg:h-screen no-scrollbar custom-scroll py-16 md:py-24 lg:py-32 px-6 md:px-16 lg:px-24 order-2 lg:order-2">
            <div class="max-w-3xl mx-auto space-y-24 md:space-y-32">
                <!-- Brand Story -->
                <div class="space-y-8 md:space-y-12" data-aos="fade-up">
                    <div class="space-y-4">
                        <span class="inline-flex items-center px-4 py-2 bg-blue-600/10 text-blue-400 rounded-full text-[10px] md:text-xs font-black uppercase tracking-[0.5em]">
                            <span class="relative flex h-2 w-2 mr-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                            </span>
                            {{ app()->getLocale() == 'id' ? 'Filosofi' : 'Philosophy' }}
                        </span>
                        <h2 class="text-5xl md:text-7xl lg:text-8xl font-black text-white leading-[0.9] tracking-tighter">
                            {{ explode(' ', $about->getTranslation('title'))[0] }} 
                            <span class="gradient-text">{{ implode(' ', array_slice(explode(' ', $about->getTranslation('title')), 1)) }}</span>
                        </h2>
                    </div>
                    <p class="text-lg md:text-2xl text-slate-400 leading-relaxed font-medium md:font-light">
                        {{ $about->getTranslation('description') }}
                    </p>
                </div>

                <!-- Vision Quote -->
                <div class="relative py-12 md:py-16">
                    <div class="absolute -top-6 -left-6 md:-left-12 text-8xl md:text-[12rem] font-black text-blue-600/5 select-none pointer-events-none">"</div>
                    <div class="space-y-6 relative z-10">
                        <h4 class="text-blue-400 font-black uppercase tracking-[0.3em] text-[10px] flex items-center">
                            <span class="w-12 h-px bg-blue-500/30 mr-4"></span>
                            {{ app()->getLocale() == 'id' ? 'Visi Masa Depan' : 'Future Vision' }}
                        </h4>
                        <blockquote class="text-3xl md:text-5xl font-black text-white italic leading-tight md:leading-[1.1] tracking-tight">
                            {{ $about->getTranslation('vision') }}
                        </blockquote>
                    </div>
                </div>

                <!-- Mission Bullets -->
                <div class="space-y-10 md:space-y-16">
                    <div class="space-y-4">
                        <h4 class="text-indigo-400 font-black uppercase tracking-[0.3em] text-[10px] flex items-center">
                            <span class="w-12 h-px bg-indigo-500/30 mr-4"></span>
                            {{ app()->getLocale() == 'id' ? 'Misi Strategis' : 'Strategic Mission' }}
                        </h4>
                        @if(app()->getLocale() == 'id')
                        <h3 class="text-3xl md:text-5xl font-black text-white tracking-tighter">Cara Kami <span class="text-slate-500">Bekerja.</span></h3>
                        @else
                        <h3 class="text-3xl md:text-5xl font-black text-white tracking-tighter">Our Work  <span class="text-slate-500">Style.</span></h3>
                        @endif
                    </div>
                    <ul class="space-y-6 md:space-y-8">
                        @foreach(explode("\n", str_replace("\r", "", $about->getTranslation('mission'))) as $index => $point)
                            @if(trim($point))
                            <li class="flex items-start gap-6 md:gap-8 group">
                                <span class="text-2xl md:text-3xl font-black text-slate-800 group-hover:text-indigo-500/40 transition-colors duration-500 italic mt-1">
                                    {{ sprintf("%02d", $index + 1) }}
                                </span>
                                <div class="space-y-2">
                                    <p class="text-lg md:text-2xl text-slate-300 font-bold leading-tight group-hover:text-white transition-colors duration-500">
                                        {{ trim($point) }}
                                    </p>
                                    <div class="w-0 h-px bg-indigo-500/50 group-hover:w-full transition-all duration-700"></div>
                                </div>
                            </li>
                            @endif
                        @endforeach
                    </ul>
                </div>

                <!-- Team Section -->
                <div class="space-y-16 md:space-y-24 pt-20 md:pt-32 border-t border-white/5">
                    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                        <div class="space-y-4">
                            <span class="text-blue-500 font-black uppercase tracking-[0.4em] text-[10px]">Architecture</span>
                            <h3 class="text-4xl md:text-6xl font-black text-white tracking-tighter leading-none">
                                {{ app()->getLocale() == 'id' ? 'Tim Kreatif' : 'Creative Minds' }}
                            </h3>
                        </div>
                        <p class="text-slate-500 font-medium max-w-xs text-sm md:text-base italic">
                            {{ app()->getLocale() == 'id' ? 'Beri Hati Pada Setiap Karyamu' : 'Put Heart Into Every Piece of Your Work.' }}
                        </p>
                    </div>
                    
                    <div class="space-y-20 md:space-y-32">
                        @foreach ($owners as $owner)
                            <div class="group/item relative">
                                <div class="flex flex-col lg:flex-row lg:items-start gap-12 md:gap-20">
                                    <!-- Photo Container -->
                                    <div class="relative shrink-0 mx-auto lg:mx-0">
                                        <div class="w-56 h-56 md:w-80 md:h-80 rounded-[4rem] md:rounded-[6rem] overflow-hidden shadow-[0_0_100px_rgba(37,99,235,0.1)] transition-all duration-1000 group-hover/item:scale-105 group-hover/item:rotate-3 group-hover/item:shadow-blue-500/20">
                                            <img src="{{ $owner->media_url }}" class="w-full h-full object-cover">
                                        </div>
                                        
                                        <!-- Socials (Floating) -->
                                        <div class="absolute top-1/2 -right-6 -translate-y-1/2 flex flex-col space-y-3 md:space-y-4">
                                            @if ($owner->instagram_url)
                                                <a href="{{ $owner->instagram_url }}" target="_blank" class="w-12 h-12 md:w-14 md:h-14 glass rounded-2xl flex items-center justify-center text-white hover:bg-gradient-to-tr hover:from-purple-600 hover:to-pink-500 hover:scale-110 hover:-translate-x-2 transition-all duration-500 shadow-2xl">
                                                    <i class="fa-brands fa-instagram text-xl"></i>
                                                </a>
                                            @endif
                                            @if ($owner->linkedin_url)
                                                <a href="{{ $owner->linkedin_url }}" target="_blank" class="w-12 h-12 md:w-14 md:h-14 glass rounded-2xl flex items-center justify-center text-white hover:bg-blue-600 hover:scale-110 hover:-translate-x-2 transition-all duration-500 shadow-2xl">
                                                    <i class="fa-brands fa-linkedin-in text-xl"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Details -->
                                    <div class="flex-1 space-y-8 text-center lg:text-left py-4">
                                        <div class="space-y-3">
                                            <div class="flex items-center justify-center lg:justify-start space-x-3">
                                                <span class="w-8 h-[1px] bg-blue-500/50"></span>
                                                <span class="text-blue-400 text-[10px] font-black uppercase tracking-[0.4em]">{{ $owner->getTranslation('role') }}</span>
                                            </div>
                                            <h4 class="text-4xl md:text-6xl lg:text-7xl font-black text-white leading-none tracking-tighter">{{ $owner->name }}</h4>
                                        </div>
                                        
                                        <p class="text-lg md:text-2xl text-slate-400 leading-relaxed italic font-light max-w-xl">
                                            "{{ $owner->getTranslation('bio') }}"
                                        </p>
                                        
                                        <div class="pt-6">
                                            <a href="{{ $owner->instagram_url }}" class="inline-flex items-center text-[10px] font-black uppercase tracking-[0.3em] text-white/40 hover:text-blue-500 transition-colors">
                                                View Profile <i class="fa-solid fa-arrow-right ml-4"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        
        .custom-scroll::-webkit-scrollbar {
            width: 3px;
        }
        .custom-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background: rgba(59, 130, 246, 0.05);
            border-radius: 10px;
        }
        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(59, 130, 246, 0.2);
        }
    </style>

    <!-- Unified About & Team Section -->
    <section id="about" class="relative lg:h-screen flex flex-col lg:flex-row bg-slate-50 dark:bg-slate-950 overflow-hidden transition-colors duration-500">
        
        <!-- Left Side: Cinematic Slider (Sticky on Desktop, Full-Bleed half screen) -->
        <div class="w-full lg:w-1/2 relative h-[45vh] md:h-[55vh] lg:h-full overflow-hidden flex-shrink-0 lg:sticky lg:top-0 order-1 lg:order-1 transition-colors duration-500">
            <div class="swiper aboutSwiper h-full w-full">
                <div class="swiper-wrapper">
                    @forelse ($aboutSlides as $aslide)
                        <div class="swiper-slide h-full w-full relative">
                            <img src="{{ $aslide->media_url }}" alt="About Slide" class="w-full h-full object-cover">
                        </div>
                    @empty
                        <div class="swiper-slide h-full w-full relative">
                            <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=1200&q=80" alt="Default About Image" class="w-full h-full object-cover">
                        </div>
                    @endforelse
                </div>
            </div>
            
            <!-- Dynamic Gradient Overlays -->
            <div class="absolute inset-0 z-10 bg-gradient-to-t from-slate-50 dark:from-slate-950 via-slate-50/20 dark:via-slate-950/20 to-transparent lg:hidden pointer-events-none"></div>
            <div class="absolute inset-0 z-10 hidden lg:block bg-gradient-to-r from-transparent via-transparent to-slate-50 dark:to-slate-950 pointer-events-none"></div>
            <div class="absolute inset-0 z-10 hidden lg:block bg-gradient-to-b from-slate-50/10 dark:from-slate-950/10 via-transparent to-slate-50/10 dark:to-slate-950/10 pointer-events-none"></div>
            
            <!-- Slider Navigation Controls -->
            <div class="absolute bottom-10 left-10 z-20 flex items-center space-x-4 bg-white/70 dark:bg-slate-900/60 backdrop-blur-md px-4 py-2.5 rounded-2xl border border-slate-200/50 dark:border-white/5 shadow-md">
                <div class="about-pagination flex items-center space-x-2"></div>
                <div class="h-3 w-px bg-slate-300 dark:bg-slate-700 hidden md:block"></div>
                <span class="text-[9px] font-black uppercase tracking-[0.4em] text-slate-500 dark:text-slate-400 font-mono hidden md:block">Discovery</span>
            </div>

            <!-- Floating Established Badge -->
            <div class="absolute top-10 left-10 z-20 hidden lg:block">
                <div class="bg-white/80 dark:bg-slate-900/85 border border-slate-200/50 dark:border-white/5 backdrop-blur-md px-6 py-3 rounded-2xl flex items-center space-x-3 shadow-md">
                    <div class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-pulse"></div>
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-800 dark:text-slate-200">Est. 2021</span>
                </div>
            </div>
        </div>

        <!-- Right Side: Content, Stats & Team (Scrollable on Desktop) -->
        <div class="w-full lg:w-1/2 bg-slate-50 dark:bg-slate-950 lg:overflow-y-auto lg:h-screen no-scrollbar custom-scroll py-16 md:py-24 lg:py-32 px-6 md:px-16 lg:px-24 order-2 lg:order-2 transition-colors duration-500">
            <div class="max-w-3xl mx-auto space-y-24 md:space-y-32">
                
                <!-- Brand Story & Dynamic Stats -->
                <div class="space-y-12">
                    <div class="space-y-4">
                        <span class="inline-flex items-center px-4 py-2 bg-blue-50 dark:bg-blue-950/50 border border-blue-100/50 dark:border-blue-900/30 text-blue-600 dark:text-blue-400 rounded-full text-[10px] md:text-xs font-black uppercase tracking-[0.5em] shadow-sm">
                            <span class="relative flex h-2 w-2 mr-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                            </span>
                            {{ app()->getLocale() == 'id' ? 'Filosofi' : 'Philosophy' }}
                        </span>
                        
                        <h2 class="text-4xl md:text-6xl lg:text-7xl font-black text-slate-900 dark:text-white leading-[0.95] tracking-tighter">
                            {{ explode(' ', $about->getTranslation('title'))[0] }} 
                            <span class="gradient-text">{{ implode(' ', array_slice(explode(' ', $about->getTranslation('title')), 1)) }}</span>
                        </h2>
                    </div>
                    
                    <p class="text-base md:text-xl text-slate-600 dark:text-slate-350 leading-relaxed font-medium md:font-light">
                        {{ $about->getTranslation('description') }}
                    </p>

                    <!-- Dynamic Stats Grid (Utilizes seeded stats perfectly) -->
                    @if(!empty($about->stats) && is_array($about->stats))
                        <div class="grid grid-cols-3 gap-4 pt-4">
                            @foreach($about->stats as $stat)
                                <div class="group/stat p-5 rounded-2xl bg-white/60 dark:bg-white/[0.01] border border-slate-200/40 dark:border-white/5 backdrop-blur-md hover:border-blue-500/20 dark:hover:border-blue-500/20 hover:-translate-y-1 transition-all duration-500 shadow-sm text-center">
                                    <h4 class="text-2xl md:text-4xl font-black text-blue-600 dark:text-blue-400 group-hover/stat:scale-105 transition-transform duration-500 leading-none">{{ $stat['value'] }}</h4>
                                    <p class="mt-2 text-[9px] md:text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 leading-snug">
                                        {{ app()->getLocale() == 'id' ? $stat['label_id'] : $stat['label_en'] }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Vision Quote -->
                <div class="relative py-12 md:py-16">
                    <div class="absolute -top-10 -left-6 md:-left-10 text-[10rem] md:text-[14rem] font-serif font-black text-blue-600/10 dark:text-blue-450/10 select-none pointer-events-none leading-none">“</div>
                    <div class="space-y-6 relative z-10">
                        <h4 class="text-blue-600 dark:text-blue-400 font-black uppercase tracking-[0.3em] text-[10px] flex items-center">
                            <span class="w-12 h-px bg-blue-600/30 mr-4"></span>
                            {{ app()->getLocale() == 'id' ? 'Visi Masa Depan' : 'Future Vision' }}
                        </h4>
                        <blockquote class="text-2xl md:text-4xl lg:text-5xl font-black text-slate-800 dark:text-slate-200 italic leading-tight tracking-tight pl-2">
                            {{ $about->getTranslation('vision') }}
                        </blockquote>
                    </div>
                </div>

                <!-- Mission Bullets -->
                <div class="space-y-10 md:space-y-16">
                    <div class="space-y-4">
                        <h4 class="text-indigo-600 dark:text-indigo-400 font-black uppercase tracking-[0.3em] text-[10px] flex items-center">
                            <span class="w-12 h-px bg-indigo-605/30 mr-4"></span>
                            {{ app()->getLocale() == 'id' ? 'Misi Strategis' : 'Strategic Mission' }}
                        </h4>
                        <h3 class="text-3xl md:text-5xl font-black text-slate-900 dark:text-white tracking-tighter">
                            @if(app()->getLocale() == 'id')
                                Cara Kami <span class="text-slate-400 dark:text-slate-500">Bekerja.</span>
                            @else
                                Our Work <span class="text-slate-400 dark:text-slate-500">Style.</span>
                            @endif
                        </h3>
                    </div>
                    
                    <ul class="space-y-8 md:space-y-10">
                        @foreach(explode("\n", str_replace("\r", "", $about->getTranslation('mission'))) as $index => $point)
                            @if(trim($point))
                            <li class="flex items-start gap-6 md:gap-8 group/mission">
                                <!-- Asymmetric circular counters with subtle backglow -->
                                <div class="relative shrink-0 flex items-center justify-center w-11 h-11 rounded-full border border-slate-200 dark:border-white/10 bg-white/40 dark:bg-white/[0.02] backdrop-blur-sm group-hover/mission:border-blue-500/40 group-hover/mission:bg-blue-500/5 transition-all duration-500">
                                    <span class="text-sm font-black text-slate-400 dark:text-slate-650 group-hover/mission:text-blue-500 transition-colors duration-500 font-mono">
                                        {{ sprintf("%02d", $index + 1) }}
                                    </span>
                                    <div class="absolute inset-0 rounded-full bg-blue-500/10 blur-md opacity-0 group-hover/mission:opacity-100 transition-opacity duration-500"></div>
                                </div>
                                
                                <div class="space-y-3 flex-1 pt-1.5">
                                    <p class="text-base md:text-xl text-slate-700 dark:text-slate-300 font-bold leading-relaxed group-hover:text-slate-900 dark:group-hover:text-white transition-colors duration-500">
                                        {{ trim($point) }}
                                    </p>
                                    <div class="w-0 h-px bg-gradient-to-r from-blue-500/50 to-transparent group-hover:w-full transition-all duration-700"></div>
                                </div>
                            </li>
                            @endif
                        @endforeach
                    </ul>
                </div>

                <!-- Team Section -->
                <div class="space-y-16 md:space-y-24 pt-20 md:pt-32 border-t border-slate-200/50 dark:border-slate-800/80">
                    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                        <div class="space-y-4">
                            <span class="text-blue-600 dark:text-blue-400 font-black uppercase tracking-[0.4em] text-[10px]">Architecture</span>
                            <h3 class="text-4xl md:text-6xl font-black text-slate-900 dark:text-white tracking-tighter leading-none">
                                {{ app()->getLocale() == 'id' ? 'Tim Kreatif' : 'Creative Minds' }}
                            </h3>
                        </div>
                        <p class="text-slate-500 dark:text-slate-400 font-medium max-w-xs text-sm md:text-base italic leading-relaxed">
                            {{ app()->getLocale() == 'id' ? 'Beri Hati Pada Setiap Karyamu' : 'Put Heart Into Every Piece of Your Work.' }}
                        </p>
                    </div>
                    
                    <div class="space-y-20 md:space-y-32">
                        @foreach ($owners as $owner)
                            <div class="group/item relative">
                                <div class="flex flex-col lg:flex-row lg:items-center gap-12 md:gap-16">
                                    
                                    <!-- Avatar Container with organic border radius and hover effects -->
                                    <div class="relative shrink-0 mx-auto lg:mx-0">
                                        <div class="relative w-60 h-60 md:w-76 md:h-76 rounded-[3rem_1rem_3rem_1rem] overflow-hidden shadow-[0_20px_50px_rgba(37,99,235,0.06)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.35)] transition-all duration-1000 group-hover/item:scale-102 group-hover/item:rotate-2 group-hover/item:shadow-blue-500/10">
                                            <img src="{{ $owner->media_url }}" alt="{{ $owner->name }}" class="w-full h-full object-cover">
                                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/20 via-transparent to-transparent z-10"></div>
                                        </div>
                                        
                                        <!-- Premium Floating Socials Dock -->
                                        <div class="absolute top-1/2 -right-6 -translate-y-1/2 flex flex-col space-y-3.5 z-20">
                                            @if ($owner->instagram_url)
                                                <a href="{{ $owner->instagram_url }}" target="_blank" class="w-12 h-12 md:w-14 md:h-14 bg-white/90 dark:bg-slate-900/90 border border-slate-200/50 dark:border-white/5 shadow-xl rounded-2xl flex items-center justify-center text-slate-700 dark:text-slate-200 hover:bg-gradient-to-tr hover:from-purple-600 hover:to-pink-500 hover:text-white hover:scale-110 hover:-translate-x-1 transition-all duration-500">
                                                    <i class="fa-brands fa-instagram text-lg md:text-xl"></i>
                                                </a>
                                            @endif
                                            @if ($owner->linkedin_url)
                                                <a href="{{ $owner->linkedin_url }}" target="_blank" class="w-12 h-12 md:w-14 md:h-14 bg-white/90 dark:bg-slate-900/90 border border-slate-200/50 dark:border-white/5 shadow-xl rounded-2xl flex items-center justify-center text-slate-700 dark:text-slate-200 hover:bg-blue-600 hover:text-white hover:scale-110 hover:-translate-x-1 transition-all duration-500">
                                                    <i class="fa-brands fa-linkedin-in text-lg md:text-xl"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Details Block -->
                                    <div class="flex-1 space-y-6 text-center lg:text-left py-2">
                                        <div class="space-y-3">
                                            <div class="flex items-center justify-center lg:justify-start space-x-3">
                                                <span class="w-8 h-[1px] bg-blue-600/30"></span>
                                                <span class="text-blue-600 dark:text-blue-400 text-[10px] font-black uppercase tracking-[0.4em]">{{ $owner->getTranslation('role') }}</span>
                                            </div>
                                            <h4 class="text-4xl md:text-5xl lg:text-6xl font-black text-slate-900 dark:text-white leading-none tracking-tighter">{{ $owner->name }}</h4>
                                        </div>
                                        
                                        <!-- Stylized Quotation bio -->
                                        <div class="relative pl-0 lg:pl-4 py-2">
                                            <span class="absolute top-0 left-0 text-5xl font-serif text-blue-600/10 dark:text-blue-400/10 hidden lg:block">“</span>
                                            <p class="text-lg md:text-xl text-slate-650 dark:text-slate-350 leading-relaxed italic font-light max-w-xl">
                                                "{{ $owner->getTranslation('bio') }}"
                                            </p>
                                        </div>
                                        
                                        <div class="pt-4">
                                            <a href="{{ $owner->instagram_url }}" class="group/profile-btn inline-flex items-center text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 dark:text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                                View Profile 
                                                <i class="fa-solid fa-arrow-right ml-4 group-hover/profile-btn:translate-x-1.5 transition-transform duration-300"></i>
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

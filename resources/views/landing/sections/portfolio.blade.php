    <!-- Portfolio Section -->
    <section id="portfolio" class="py-24 md:py-40 bg-white dark:bg-slate-950 relative overflow-hidden transition-colors duration-500">
        
        <!-- Ambient Glowing Background Elements -->
        <div class="absolute top-1/2 left-0 w-96 h-96 bg-blue-500/5 dark:bg-blue-600/5 rounded-full blur-[120px] pointer-events-none animate-pulse"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-indigo-500/5 dark:bg-indigo-600/5 rounded-full blur-[120px] pointer-events-none animate-pulse" style="animation-delay: 2s;"></div>

        <div class="container mx-auto px-6 relative z-10">
            
            <!-- Section Header -->
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-24 md:mb-36 gap-10">
                <div class="max-w-2xl" data-aos="fade-right" data-aos-once="true">
                    <div class="flex items-center space-x-4 mb-6">
                        <span class="w-12 h-px bg-blue-600/50"></span>
                        <span class="text-blue-600 dark:text-blue-400 font-black uppercase tracking-[0.4em] text-[10px] md:text-xs">
                            {{ app()->getLocale() == 'id' ? 'Karya Terpilih' : 'Selected Works' }}
                        </span>
                    </div>
                    
                    <h2 class="text-5xl md:text-7xl lg:text-8xl font-black text-slate-900 dark:text-white tracking-tighter leading-none mb-8">
                        {{ app()->getLocale() == 'id' ? 'Proyek' : 'Featured' }} <span class="gradient-text">{{ app()->getLocale() == 'id' ? 'Terbaik.' : 'Projects.' }}</span>
                    </h2>
                    
                    <p class="text-lg md:text-2xl text-slate-650 dark:text-slate-350 leading-relaxed font-medium md:font-light">
                        {{ app()->getLocale() == 'id' ? 'Eksplorasi kreativitas kami dalam membangun ekosistem digital yang bermakna.' : 'Exploring our creativity in building meaningful digital ecosystems.' }}
                    </p>
                </div>
                
                <!-- Premium High-Tech Delivery Counter -->
                <div class="flex items-center" data-aos="fade-left" data-aos-once="true">
                    <div class="relative p-5 md:p-6 rounded-2xl bg-slate-50/80 dark:bg-white/[0.01] border border-slate-200/50 dark:border-white/5 backdrop-blur-md flex items-center space-x-5 shadow-sm hover:-translate-y-1 transition-transform duration-500">
                        <div class="w-12 h-12 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400">
                            <i class="fa-solid fa-square-poll-vertical text-xl"></i>
                        </div>
                        <div>
                            <p class="text-slate-900 dark:text-white font-black text-3xl leading-none font-mono">{{ $projects->count() }}</p>
                            <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mt-1.5 leading-none">Projects Delivered</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Staggered Project Mockups Grid (Offset adds high-end aesthetic rhythm) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 md:gap-20 lg:gap-24 pb-16 md:pb-24">
                @foreach ($projects as $index => $project)
                    <div class="group relative flex flex-col {{ $index % 2 != 0 ? 'md:translate-y-16' : '' }} transition-all duration-700" 
                         data-aos="fade-up" 
                         data-aos-once="true"
                         data-aos-delay="{{ $index % 2 == 0 ? '0' : '200' }}">
                        
                        <!-- High-Fidelity Ultra-Dark Glass Browser Mockup -->
                        <div class="relative overflow-hidden rounded-[2.5rem] mb-6 aspect-video bg-slate-100 dark:bg-slate-900 border border-slate-200/60 dark:border-white/5 shadow-xl group/card transition-all duration-1000 group-hover:scale-[1.02] group-hover:-translate-y-3 group-hover:shadow-[0_30px_80px_rgba(59,130,246,0.08)] group-hover:border-slate-350 dark:group-hover:border-white/10 flex flex-col">
                            
                            <!-- Premium Browser Mockup Bezel Header -->
                            <div class="shrink-0 h-12 bg-slate-100/80 dark:bg-slate-900/80 border-b border-slate-200/60 dark:border-white/5 px-6 flex items-center justify-between z-30 transition-colors">
                                <!-- macOS dots window controls -->
                                <div class="flex space-x-2 shrink-0">
                                    <div class="w-2.5 h-2.5 rounded-full bg-red-400/80 hover:bg-red-500 transition-colors"></div>
                                    <div class="w-2.5 h-2.5 rounded-full bg-yellow-400/80 hover:bg-yellow-500 transition-colors"></div>
                                    <div class="w-2.5 h-2.5 rounded-full bg-green-400/80 hover:bg-green-500 transition-colors"></div>
                                </div>
                                
                                <!-- Fake interactive search/URL input -->
                                <div class="w-1/2 md:w-3/5 bg-slate-200/50 dark:bg-white/[0.04] border border-slate-200/30 dark:border-white/5 py-1 px-4 rounded-lg text-[9px] font-mono text-slate-500 dark:text-slate-400 text-center truncate flex items-center justify-center space-x-1.5 hover:bg-slate-200 dark:hover:bg-white/[0.06] transition-all">
                                    <i class="fa-solid fa-lock text-[8px] text-green-500 animate-pulse"></i>
                                    <span>fkstudio.co/work/{{ Str::slug($project->getTranslation('title')) }}</span>
                                </div>
                                
                                <!-- Browser Actions spacer -->
                                <div class="flex space-x-1.5 shrink-0 opacity-40">
                                    <div class="w-1.5 h-1.5 rounded-full bg-slate-600"></div>
                                    <div class="w-1.5 h-1.5 rounded-full bg-slate-600"></div>
                                    <div class="w-1.5 h-1.5 rounded-full bg-slate-600"></div>
                                </div>
                            </div>

                            <!-- Screen Contents -->
                            <div class="flex-1 w-full relative overflow-hidden bg-slate-200 dark:bg-slate-900 flex items-center justify-center">
                                @if ($project->image)
                                    <img src="{{ $project->media_url }}"
                                        alt="{{ $project->getTranslation('title') }}"
                                        class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105">
                                @elseif($project->url)
                                    <div class="w-full h-full relative bg-slate-100 dark:bg-slate-900 overflow-hidden">
                                        <iframe src="{{ $project->url }}"
                                            title="{{ $project->getTranslation('title') }}"
                                            class="absolute top-0 left-0 w-[300%] h-[300%] origin-top-left border-none pointer-events-none opacity-60 group-hover:opacity-80 transition-opacity duration-1000"
                                            style="transform: scale(0.333333);" loading="lazy" scrolling="no"></iframe>
                                    </div>
                                @else
                                    <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=800&q=80"
                                        alt="Default project image"
                                        class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105">
                                @endif

                                <!-- Category Badge floating in top corner -->
                                <div class="absolute top-4 right-4 z-20">
                                    <span class="px-3.5 py-1.5 bg-white/80 dark:bg-slate-950/70 border border-slate-200/50 dark:border-white/5 text-slate-800 dark:text-slate-200 text-[8px] font-black uppercase tracking-[0.1em] rounded-xl backdrop-blur-md shadow-sm">
                                        {{ $project->getTranslation('category') ?? 'Digital' }}
                                    </span>
                                </div>

                                <!-- Hover Live Launch Overlay Link -->
                                <div class="absolute inset-0 bg-slate-950/45 opacity-0 group-hover:opacity-100 transition-opacity duration-500 z-20 flex items-center justify-center backdrop-blur-[4px]">
                                    <a href="{{ $project->url ?? '#' }}" target="_blank" 
                                       class="group/btn w-20 h-20 bg-white/95 dark:bg-slate-900/95 border border-slate-200/50 dark:border-white/10 rounded-full flex items-center justify-center text-slate-800 dark:text-white transform scale-50 group-hover:scale-100 transition-all duration-500 hover:bg-blue-600 hover:text-white hover:scale-105 hover:border-blue-600 shadow-2xl">
                                        <i class="fa-solid fa-arrow-up-right-from-square text-xl transition-transform duration-300 group-hover/btn:translate-x-0.5 group-hover/btn:-translate-y-0.5"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Card Details Row -->
                        <div class="flex items-start justify-between px-4 md:px-6">
                            <div class="space-y-2.5 max-w-sm md:max-w-md">
                                <span class="text-[9px] font-black uppercase tracking-[0.2em] text-blue-600 dark:text-blue-400">
                                    {{ $project->getTranslation('category') ?? 'Digital' }}
                                </span>
                                <h4 class="text-2xl md:text-4xl font-black text-slate-900 dark:text-white leading-none tracking-tighter group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-500">
                                    {{ $project->getTranslation('title') }}
                                </h4>
                                <p class="text-slate-500 dark:text-slate-400 font-medium text-xs md:text-sm leading-relaxed line-clamp-2">
                                    {{ $project->getTranslation('description') }}
                                </p>
                            </div>
                            
                            <!-- Arrow Button that tilts and rotates on Hover -->
                            <div class="pt-1.5 shrink-0">
                                <a href="{{ $project->url ?? '#' }}" target="_blank" 
                                   class="w-11 h-11 md:w-13 md:h-13 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm flex items-center justify-center text-slate-500 dark:text-slate-400 group-hover:bg-blue-600 group-hover:text-white group-hover:border-blue-600 transition-all duration-500 transform group-hover:rotate-45 group-hover:scale-105">
                                    <i class="fa-solid fa-arrow-right text-sm"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- More Projects Link -->
            <div class="mt-20 md:mt-32 text-center" data-aos="fade-up" data-aos-once="true">
                <a href="{{ route('portfolio.index') }}" class="group inline-flex flex-col items-center">
                    <span class="text-slate-500 dark:text-slate-400 font-black uppercase tracking-[0.4em] text-[10px] mb-4 transition-colors group-hover:text-blue-600">
                        {{ app()->getLocale() == 'id' ? 'Lihat Semua Proyek' : 'View All Projects' }}
                    </span>
                    <div class="w-24 h-[1px] bg-slate-200 dark:bg-slate-800 relative overflow-hidden">
                        <div class="absolute inset-0 bg-blue-600 -translate-x-full group-hover:translate-x-0 transition-transform duration-700"></div>
                    </div>
                </a>
            </div>
        </div>
    </section>

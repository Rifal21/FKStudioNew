    <!-- Portfolio Section -->
    <section id="portfolio" class="py-24 md:py-40 bg-slate-950 relative overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute top-1/2 left-0 w-96 h-96 bg-blue-600/5 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-indigo-600/5 rounded-full blur-[100px] pointer-events-none"></div>

        <div class="container mx-auto px-6 relative z-10">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-20 md:mb-32 gap-10">
                <div class="max-w-2xl" data-aos="fade-right">
                    <div class="flex items-center space-x-4 mb-6">
                        <span class="w-12 h-px bg-blue-500"></span>
                        <span class="text-blue-500 font-black uppercase tracking-[0.4em] text-[10px] md:text-xs">
                            {{ app()->getLocale() == 'id' ? 'Karya Terpilih' : 'Selected Works' }}
                        </span>
                    </div>
                    <h2 class="text-5xl md:text-7xl lg:text-8xl font-black text-white tracking-tighter leading-none mb-8">
                        {{ app()->getLocale() == 'id' ? 'Proyek' : 'Featured' }} <span class="gradient-text">{{ app()->getLocale() == 'id' ? 'Terbaik.' : 'Projects.' }}</span>
                    </h2>
                    <p class="text-lg md:text-2xl text-slate-400 font-medium leading-relaxed">
                        {{ app()->getLocale() == 'id' ? 'Eksplorasi kreativitas kami dalam membangun ekosistem digital yang bermakna.' : 'Exploring our creativity in building meaningful digital ecosystems.' }}
                    </p>
                </div>
                
                <div class="flex items-center space-x-6" data-aos="fade-left">
                    <div class="text-right hidden md:block">
                        <p class="text-white font-black text-4xl leading-none">{{ $projects->count() }}</p>
                        <p class="text-slate-500 text-[10px] font-bold uppercase tracking-widest mt-2">Projects Delivered</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 md:gap-20">
                @foreach ($projects as $index => $project)
                    <div class="group relative" data-aos="fade-up" data-aos-delay="{{ $index % 2 == 0 ? '0' : '200' }}">
                        <!-- Browser-style Frame -->
                        <div class="relative overflow-hidden rounded-[2.5rem] md:rounded-[4rem] mb-10 aspect-video bg-slate-900 border border-white/5 shadow-2xl group/card transition-all duration-700 hover:shadow-blue-500/10">
                            
                            <!-- Browser Dots -->
                            <div class="absolute top-6 left-8 flex space-x-2 z-30 opacity-40 group-hover:opacity-100 transition-opacity">
                                <div class="w-2.5 h-2.5 rounded-full bg-red-500/50"></div>
                                <div class="w-2.5 h-2.5 rounded-full bg-yellow-500/50"></div>
                                <div class="w-2.5 h-2.5 rounded-full bg-green-500/50"></div>
                            </div>

                            @if ($project->image)
                                <img src="{{ $project->media_url }}"
                                    alt="{{ $project->getTranslation('title') }}"
                                    class="w-full h-full object-cover transition duration-1000 group-hover:scale-110">
                            @elseif($project->url)
                                <div class="w-full h-full relative bg-slate-900 overflow-hidden">
                                    <iframe src="{{ $project->url }}"
                                        title="{{ $project->getTranslation('title') }}"
                                        class="absolute top-0 left-0 w-[300%] h-[300%] origin-top-left border-none pointer-events-none opacity-40 group-hover:opacity-60 transition-opacity duration-1000"
                                        style="transform: scale(0.333333);" loading="lazy" scrolling="no"></iframe>
                                </div>
                            @else
                                <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=800&q=80"
                                    alt="Default project image"
                                    class="w-full h-full object-cover transition duration-1000 group-hover:scale-110">
                            @endif

                            <!-- Hover Overlay -->
                            <div class="absolute inset-0 bg-slate-950/60 opacity-0 group-hover:opacity-100 transition-all duration-500 z-20 flex items-center justify-center backdrop-blur-sm">
                                <a href="{{ $project->url ?? '#' }}" target="_blank" 
                                   class="w-20 h-20 md:w-24 md:h-24 bg-blue-600 rounded-full flex items-center justify-center text-white text-2xl transform scale-50 group-hover:scale-100 transition-all duration-500 hover:bg-blue-700 hover:scale-110 shadow-2xl shadow-blue-500/50">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                </a>
                            </div>

                            <!-- Floating Category -->
                            <div class="absolute top-6 right-8 z-30">
                                <span class="px-4 py-2 glass rounded-2xl text-[10px] text-white font-black uppercase tracking-widest border border-white/10 group-hover:border-blue-500/30 transition-colors">
                                    {{ $project->getTranslation('category') ?? 'Digital' }}
                                </span>
                            </div>
                        </div>

                        <!-- Details Container -->
                        <div class="flex items-start justify-between px-6 md:px-10">
                            <div class="space-y-4 max-w-sm">
                                <h4 class="text-3xl md:text-5xl font-black text-white leading-none tracking-tighter group-hover:text-blue-400 transition-colors duration-500">
                                    {{ $project->getTranslation('title') }}
                                </h4>
                                <p class="text-slate-500 font-medium text-sm md:text-lg leading-relaxed line-clamp-2">
                                    {{ $project->getTranslation('description') }}
                                </p>
                            </div>
                            
                            <div class="pt-2">
                                <div class="w-12 h-12 md:w-16 md:h-16 rounded-3xl border border-white/5 flex items-center justify-center text-slate-500 group-hover:bg-blue-600 group-hover:text-white group-hover:border-blue-600 transition-all duration-500 transform group-hover:rotate-45">
                                    <i class="fa-solid fa-arrow-right text-xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- More Projects Link -->
            <div class="mt-32 text-center" data-aos="fade-up">
                <a href="#" class="group inline-flex flex-col items-center">
                    <span class="text-slate-500 font-black uppercase tracking-[0.4em] text-[10px] mb-6 transition-colors group-hover:text-blue-500">View All Architecture</span>
                    <div class="w-20 h-[1px] bg-white/10 relative overflow-hidden">
                        <div class="absolute inset-0 bg-blue-500 -translate-x-full group-hover:translate-x-0 transition-transform duration-700"></div>
                    </div>
                </a>
            </div>
        </div>
    </section>

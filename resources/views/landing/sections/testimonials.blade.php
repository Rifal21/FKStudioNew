    <!-- Testimonials Section -->
    <section id="testimonials" class="py-24 md:py-40 bg-white dark:bg-slate-950 relative overflow-hidden transition-colors duration-500">
        
        <!-- Ambient Glowing Background Elements -->
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-blue-500/5 dark:bg-blue-600/5 rounded-full blur-[120px] pointer-events-none animate-pulse"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-indigo-500/5 dark:bg-indigo-600/5 rounded-full blur-[120px] pointer-events-none animate-pulse" style="animation-delay: 2.5s;"></div>

        <div class="container mx-auto px-6 relative z-10">
            
            <!-- Section Header -->
            <div class="text-center max-w-4xl mx-auto mb-20 md:mb-32">
                <div class="inline-flex items-center space-x-3 mb-6" data-aos="fade-up" data-aos-once="true">
                    <span class="w-10 h-[1px] bg-blue-600/30"></span>
                    <span class="text-blue-600 dark:text-blue-400 font-black uppercase tracking-[0.4em] text-[10px] md:text-xs">
                        {{ app()->getLocale() == 'id' ? 'Kepuasan Klien' : 'Client Satisfaction' }}
                    </span>
                    <span class="w-10 h-[1px] bg-blue-600/30"></span>
                </div>
                
                <h2 class="text-5xl md:text-7xl font-black text-slate-900 dark:text-white mb-8 tracking-tighter leading-none" data-aos="fade-up" data-aos-once="true" data-aos-delay="100">
                    {{ app()->getLocale() == 'id' ? 'Apa Kata' : 'What' }} <span class="gradient-text">{{ app()->getLocale() == 'id' ? 'Mereka.' : 'They Say.' }}</span>
                </h2>
                
                <p class="text-lg md:text-xl text-slate-655 dark:text-slate-350 max-w-xl mx-auto font-medium" data-aos="fade-up" data-aos-once="true" data-aos-delay="200">
                    {{ app()->getLocale() == 'id' ? 'Berikut adalah pengalaman klien bekerja bersama FKStudio.' : 'Read authentic stories from business owners who partnered with FKStudio.' }}
                </p>
            </div>

            <!-- Swiper Slider Container -->
            <div class="swiper testimonialSwiper lg:max-w-6xl mx-auto overflow-visible">
                <div class="swiper-wrapper py-8">
                    @foreach ($testimonials as $index => $tm)
                        <div class="swiper-slide h-auto px-3">
                            
                            <!-- High-Fidelity Glassmorphic Card -->
                            <div class="bg-white/80 dark:bg-slate-900/40 p-8 md:p-12 rounded-[3rem] text-left relative h-full flex flex-col justify-between border border-slate-200/50 dark:border-white/5 hover:border-blue-500/20 dark:hover:border-blue-500/15 hover:-translate-y-2 hover:shadow-[0_20px_50px_rgba(59,130,246,0.06)] shadow-md transition-all duration-700 flex flex-col"
                                 data-aos="fade-up"
                                 data-aos-once="true"
                                 data-aos-delay="{{ 100 * ($index % 3) }}">
                                
                                <!-- Large Ambient Quotation Mark in Background -->
                                <div class="absolute top-6 right-8 text-[7rem] md:text-[8rem] font-serif font-black text-blue-600/5 dark:text-blue-500/5 select-none pointer-events-none leading-none">”</div>

                                <div class="relative">
                                    <!-- Amber Glowing Stars Grid -->
                                    <div class="flex text-amber-500 mb-8 space-x-1.5">
                                        @for ($i = 0; $i < ($tm->rating ?? 5); $i++)
                                            <i class="fa-solid fa-star text-xs drop-shadow-[0_0_8px_rgba(245,158,11,0.55)]"></i>
                                        @endfor
                                    </div>

                                    <!-- Testimonial content -->
                                    <p class="text-base md:text-lg italic text-slate-700 dark:text-white mb-10 leading-relaxed font-semibold md:font-medium">
                                        "{{ $tm->getTranslation('content') }}"
                                    </p>
                                </div>

                                <!-- Client Meta Block -->
                                <div class="flex items-center space-x-4 border-t border-slate-200/50 dark:border-white/5 pt-6 mt-auto">
                                    @if ($tm->avatar)
                                        <div class="relative shrink-0 group/avatar">
                                            <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl blur-md opacity-20 group-hover/avatar:opacity-60 transition duration-700"></div>
                                            <img src="{{ $tm->media_url }}"
                                                 class="relative w-12 h-12 md:w-14 md:h-14 rounded-2xl object-cover border border-white/40 dark:border-white/10 shadow-sm"
                                                 alt="{{ $tm->name }}">
                                        </div>
                                    @else
                                        <div class="w-12 h-12 md:w-14 md:h-14 rounded-2xl bg-blue-500/10 dark:bg-blue-500/5 border border-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center font-black text-lg md:text-xl shrink-0">
                                            {{ substr($tm->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div class="truncate">
                                        <h4 class="font-black text-base md:text-lg text-slate-900 dark:text-white tracking-tight leading-none mb-1.5 truncate">
                                            {{ $tm->name }}
                                        </h4>
                                        <p class="text-[9px] text-blue-600 dark:text-blue-400 uppercase tracking-[0.2em] font-black italic">
                                            {{ $tm->getTranslation('role') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Custom Luxury Navigation Controls -->
                <div class="flex justify-center items-center mt-16 md:mt-24 space-x-8" data-aos="fade-up" data-aos-once="true">
                    <!-- Prev Button -->
                    <button class="testimonial-prev w-14 h-14 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/70 dark:bg-slate-900/40 text-slate-650 dark:text-slate-400 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all duration-500 shadow-sm outline-none group transform hover:-translate-x-1 backdrop-blur-md">
                        <i class="fa-solid fa-arrow-left text-sm group-hover:-translate-x-0.5 transition-transform"></i>
                    </button>
                    
                    <!-- Progress Bullet Pagination -->
                    <div class="testimonial-pagination !static !w-auto"></div>
                    
                    <!-- Next Button -->
                    <button class="testimonial-next w-14 h-14 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/70 dark:bg-slate-900/40 text-slate-655 dark:text-slate-400 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all duration-500 shadow-sm outline-none group transform hover:translate-x-1 backdrop-blur-md">
                        <i class="fa-solid fa-arrow-right text-sm group-hover:translate-x-0.5 transition-transform"></i>
                    </button>
                </div>
            </div>
        </div>
    </section>

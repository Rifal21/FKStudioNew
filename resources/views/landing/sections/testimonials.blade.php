    <!-- Testimonials -->
    <section id="testimonials" class="py-20 md:py-32 bg-slate-950 relative overflow-hidden">
        <!-- Background Accents -->
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-blue-600/5 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-indigo-600/5 rounded-full blur-[100px] pointer-events-none"></div>

        <div class="container mx-auto px-6 relative z-10">
            <div class="text-center max-w-4xl mx-auto mb-16 md:mb-24">
                <div class="inline-flex items-center space-x-3 mb-4" data-aos="fade-up">
                    <span class="w-10 h-[1px] bg-blue-500/50"></span>
                    <span class="text-blue-500 font-black uppercase tracking-[0.4em] text-[10px]">
                        {{ app()->getLocale() == 'id' ? 'Kepuasan Klien' : 'Client Satisfaction' }}
                    </span>
                    <span class="w-10 h-[1px] bg-blue-500/50"></span>
                </div>
                <h2 class="text-4xl md:text-6xl font-black text-white mb-6 tracking-tighter leading-none" data-aos="fade-up" data-aos-delay="100">
                    {{ app()->getLocale() == 'id' ? 'Apa Kata' : 'What' }} <span class="gradient-text">{{ app()->getLocale() == 'id' ? 'Mereka.' : 'They Say.' }}</span>
                </h2>
                <p class="text-base md:text-lg text-slate-400 max-w-xl mx-auto font-medium" data-aos="fade-up" data-aos-delay="200">
                    Berikut adalah pengalaman klien bekerja bersama FKStudio.
                </p>
            </div>

            <div class="swiper testimonialSwiper lg:max-w-6xl mx-auto overflow-visible">
                <div class="swiper-wrapper py-6">
                    @foreach ($testimonials as $tm)
                        <div class="swiper-slide h-auto px-3">
                            <div class="glass p-8 md:p-10 rounded-[2.5rem] text-left relative h-full flex flex-col justify-between border border-white/5 hover:border-blue-500/30 transition-all duration-700 shadow-xl"
                                data-aos="fade-up">
                                <!-- Quote Icon -->
                                <div class="absolute -top-4 -left-4 md:-top-6 md:-left-6">
                                    <div class="w-12 h-12 md:w-16 md:h-16 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-full flex items-center justify-center text-white text-lg md:text-2xl shadow-xl shadow-blue-500/40 transform -rotate-12">
                                        <i class="fa-solid fa-quote-left"></i>
                                    </div>
                                </div>

                                <div class="relative pt-4">
                                    <!-- Stars -->
                                    <div class="flex text-yellow-500 mb-6 space-x-1">
                                        @for ($i = 0; $i < ($tm->rating ?? 5); $i++)
                                            <i class="fa-solid fa-star text-[10px]"></i>
                                        @endfor
                                    </div>

                                    <p class="text-base md:text-lg italic text-slate-300 mb-8 leading-relaxed font-medium">
                                        "{{ $tm->getTranslation('content') }}"
                                    </p>
                                </div>

                                <div class="flex items-center space-x-4 mt-6 border-t border-white/5 pt-8">
                                    @if ($tm->avatar)
                                        <div class="relative group/avatar">
                                            <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl blur opacity-25 group-hover/avatar:opacity-100 transition duration-700"></div>
                                            <img src="{{ $tm->media_url }}"
                                                class="relative w-12 h-12 md:w-14 md:h-14 rounded-xl object-cover shadow-xl">
                                        </div>
                                    @else
                                        <div class="w-12 h-12 md:w-14 md:h-14 bg-slate-800 rounded-xl flex items-center justify-center font-black text-blue-500 text-lg md:text-xl border border-white/5">
                                            {{ substr($tm->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div>
                                        <h4 class="font-black text-lg text-white tracking-tight leading-none mb-1.5">
                                            {{ $tm->name }}
                                        </h4>
                                        <p class="text-[9px] text-blue-500 uppercase tracking-[0.2em] font-black italic">
                                            {{ $tm->getTranslation('role') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Swiper Navigation -->
                <div class="flex justify-center items-center mt-16 md:mt-24 space-x-10">
                    <button class="testimonial-prev w-14 h-14 md:w-16 md:h-16 rounded-3xl border border-white/5 flex items-center justify-center text-slate-500 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all duration-500 outline-none group transform hover:-translate-x-2">
                        <i class="fa-solid fa-arrow-left text-xl"></i>
                    </button>
                    <div class="testimonial-pagination !static !w-auto"></div>
                    <button class="testimonial-next w-14 h-14 md:w-16 md:h-16 rounded-3xl border border-white/5 flex items-center justify-center text-slate-500 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all duration-500 outline-none group transform hover:translate-x-2">
                        <i class="fa-solid fa-arrow-right text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Section -->
    <footer id="contact" class="bg-slate-100 dark:bg-slate-950 pt-24 md:pt-40 pb-12 relative overflow-hidden border-t border-slate-200/85 dark:border-white/5 transition-colors duration-500">
        <!-- Floating decorative blobs -->
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-blue-300/10 rounded-full blur-[100px] pointer-events-none"></div>

        <div class="container mx-auto px-6 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 lg:gap-24 mb-20 md:mb-32">
                <!-- Brand Info -->
                <div class="lg:col-span-5 space-y-8">
                    @if ($settings->site_logo)
                        <img src="{{ $settings->logo_url }}" class="h-12 w-auto">
                    @else
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/20">
                            <span class="text-white font-black text-2xl italic leading-none">F</span>
                        </div>
                    @endif
                    <p class="text-slate-600 dark:text-slate-350 text-sm md:text-lg leading-relaxed font-medium">
                        {{ $settings->site_description ?? 'Membangun masa depan digital yang luar biasa untuk bisnis Anda.' }}
                    </p>
                    <div class="flex items-center space-x-4">
                        @if ($settings->instagram_url)
                            <a href="{{ $settings->instagram_url }}" target="_blank"
                                class="w-12 h-12 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-center text-slate-500 dark:text-slate-400 hover:bg-blue-600 hover:text-white hover:border-blue-600 hover:scale-110 hover:shadow-lg transition-all duration-500">
                                <i class="fa-brands fa-instagram text-lg"></i>
                            </a>
                        @endif
                        @if ($settings->linkedin_url)
                            <a href="{{ $settings->linkedin_url }}" target="_blank"
                                class="w-12 h-12 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-center text-slate-500 dark:text-slate-400 hover:bg-blue-600 hover:text-white hover:border-blue-600 hover:scale-110 hover:shadow-lg transition-all duration-500">
                                <i class="fa-brands fa-linkedin-in text-lg"></i>
                            </a>
                        @endif
                        @if ($settings->tiktok_url)
                            <a href="{{ $settings->tiktok_url }}" target="_blank"
                                class="w-12 h-12 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-center text-slate-500 dark:text-slate-400 hover:bg-blue-600 hover:text-white hover:border-blue-600 hover:scale-110 hover:shadow-lg transition-all duration-500">
                                <i class="fa-brands fa-tiktok text-lg"></i>
                            </a>
                        @endif
                        @if ($settings->youtube_url)
                            <a href="{{ $settings->youtube_url }}" target="_blank"
                                class="w-12 h-12 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-center text-slate-500 dark:text-slate-400 hover:bg-blue-600 hover:text-white hover:border-blue-600 hover:scale-110 hover:shadow-lg transition-all duration-500">
                                <i class="fa-brands fa-youtube text-lg"></i>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="lg:col-span-3 space-y-6">
                    <h4 class="text-slate-800 dark:text-slate-200 font-black uppercase tracking-[0.3em] text-[10px] flex items-center">
                        <span class="w-8 h-[1px] bg-blue-600/30 mr-4"></span>
                        Quick Navigation
                    </h4>
                    <ul class="space-y-4">
                        <li>
                            <a href="#home" class="text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 text-sm font-medium transition-colors">Home</a>
                        </li>
                        <li>
                            <a href="#about" class="text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 text-sm font-medium transition-colors">About Us</a>
                        </li>
                        <li>
                            <a href="#services" class="text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 text-sm font-medium transition-colors">Services</a>
                        </li>
                        <li>
                            <a href="#portfolio" class="text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 text-sm font-medium transition-colors">Portfolio</a>
                        </li>
                        <li>
                            <a href="{{ route('package.index') }}" class="text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 text-sm font-medium transition-colors">Products</a>
                        </li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="lg:col-span-4 space-y-6">
                    <h4 class="text-slate-800 dark:text-slate-200 font-black uppercase tracking-[0.3em] text-[10px] flex items-center">
                        <span class="w-8 h-[1px] bg-blue-600/30 mr-4"></span>
                        Get In Touch
                    </h4>
                    <div class="space-y-4">
                        @if ($settings->contact_email)
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex items-center justify-center text-blue-600 dark:text-blue-400 shrink-0 shadow-sm">
                                    <i class="fa-regular fa-envelope"></i>
                                </div>
                                <a href="mailto:{{ $settings->contact_email }}"
                                    class="text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 text-sm font-medium transition-colors break-all">
                                    {{ $settings->contact_email }}
                                </a>
                            </div>
                        @endif
                        @if ($settings->contact_phone)
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex items-center justify-center text-blue-600 dark:text-blue-400 shrink-0 shadow-sm">
                                    <i class="fa-solid fa-phone"></i>
                                </div>
                                <a href="tel:{{ $settings->contact_phone }}"
                                    class="text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 text-sm font-medium transition-colors">
                                    {{ $settings->contact_phone }}
                                </a>
                            </div>
                        @endif
                        @if ($settings->contact_address)
                            <div class="flex items-start space-x-4">
                                <div class="w-10 h-10 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex items-center justify-center text-blue-600 dark:text-blue-400 shrink-0 mt-1 shadow-sm">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>
                                <p class="text-slate-600 dark:text-slate-400 text-sm font-medium leading-relaxed">
                                    {{ $settings->contact_address }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="pt-12 border-t border-slate-200/80 dark:border-slate-800 flex flex-col md:flex-row justify-between items-center gap-6">
                <p class="text-slate-500 dark:text-slate-400 text-xs font-medium">
                    &copy; {{ date('Y') }} {{ $settings->site_name ?? 'FKStudio' }}. All rights reserved.
                </p>
                <div class="flex items-center space-x-8">
                    <a href="#" class="text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 text-xs font-medium transition-colors">Privacy Policy</a>
                    <a href="#" class="text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 text-xs font-medium transition-colors">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

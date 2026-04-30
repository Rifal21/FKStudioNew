    <!-- Footer / Contact -->
    <footer id="contact" class="pt-16 md:pt-28 pb-8 md:pb-12 bg-slate-950 border-t border-white/5">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 md:gap-12 mb-12 md:mb-20">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-2 mb-6 md:mb-8">
                        @if ($settings->site_logo)
                            <img src="{{ $settings->logo_url }}" class="h-8 md:h-10 w-auto">
                        @else
                            <div
                                class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg md:rounded-xl flex items-center justify-center">
                                <span class="text-white font-bold text-lg md:text-xl italic leading-none">F</span>
                            </div>
                        @endif
                        <span
                            class="text-xl md:text-2xl font-black italic tracking-tighter">{{ $settings->getTranslation('site_name') ?? 'FKStudio' }}</span>
                    </div>
                    <p class="text-slate-400 max-w-sm text-base md:text-lg leading-relaxed mb-6 md:mb-8">
                        {{ $about->getTranslation('description') }}
                    </p>
                    <div class="flex space-x-3 md:space-x-4">
                        <!-- Socials -->
                        @php $socials = $settings->social_links ?? []; @endphp

                        @if (!empty($socials['instagram']))
                            <a href="{{ $socials['instagram'] }}" target="_blank"
                                class="w-12 h-12 glass rounded-2xl flex items-center justify-center hover:bg-pink-600 hover:scale-110 transition-all"><svg
                                    class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.256-2.636-5.892-5.892-5.892zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                                </svg></a>
                        @endif

                        @if (!empty($socials['twitter']))
                            <a href="{{ $socials['twitter'] }}" target="_blank"
                                class="w-12 h-12 glass rounded-2xl flex items-center justify-center hover:bg-blue-400 hover:scale-110 transition-all"><svg
                                    class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                                </svg></a>
                        @endif

                        @if (!empty($socials['facebook']))
                            <a href="{{ $socials['facebook'] }}" target="_blank"
                                class="w-12 h-12 glass rounded-2xl flex items-center justify-center hover:bg-blue-800 hover:scale-110 transition-all"><svg
                                    class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.324v-21.35c0-.732-.593-1.325-1.323-1.325z" />
                                </svg></a>
                        @endif

                        @if (!empty($socials['linkedin']))
                            <a href="{{ $socials['linkedin'] }}" target="_blank"
                                class="w-10 h-10 md:w-12 md:h-12 glass rounded-xl md:rounded-2xl flex items-center justify-center hover:bg-blue-700 hover:scale-110 transition-all"><svg
                                    class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                                </svg></a>
                        @endif
                    </div>
                </div>

                <div>
                    <h4 class="text-white font-bold text-lg md:text-xl mb-6 md:mb-8">
                        {{ app()->getLocale() == 'id' ? 'Tautan Cepat' : 'Quick Links' }}</h4>
                    <ul class="space-y-3 md:space-y-4 text-slate-400 text-sm md:text-base">
                        <li><a href="#home" class="hover:text-blue-500 transition-colors">Home</a></li>
                        <li><a href="#about" class="hover:text-blue-500 transition-colors">About Us</a></li>
                        <li><a href="#services" class="hover:text-blue-500 transition-colors">Services</a></li>
                        <li><a href="#portfolio" class="hover:text-blue-500 transition-colors">Portfolio</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-bold text-lg md:text-xl mb-6 md:mb-8">
                        {{ app()->getLocale() == 'id' ? 'Kontak' : 'Contact Us' }}</h4>
                    <ul class="space-y-3 md:space-y-4 text-slate-400 text-sm md:text-base">
                        <li class="flex items-start space-x-3">
                            <i class="fa-solid fa-location-dot mt-1 text-blue-500"></i>
                            <span>{{ $settings->address ?? 'Jakarta, Indonesia' }}</span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <i class="fa-solid fa-phone text-blue-500"></i>
                            <span>{{ $settings->phone ?? '+62 812 3456 7890' }}</span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <i class="fa-solid fa-envelope text-blue-500"></i>
                            <span>{{ $settings->email ?? 'hello@fkstudio.com' }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="pt-8 md:pt-12 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-slate-500 text-xs md:text-sm text-center md:text-left">
                    &copy; {{ date('Y') }} {{ $settings->getTranslation('site_name') ?? 'FKStudio' }}. All rights reserved.
                </p>
                <div class="flex items-center space-x-4 md:space-x-8 text-[10px] md:text-xs text-slate-500 font-bold uppercase tracking-widest">
                    <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
                    <a href="#" class="hover:text-white transition-colors">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

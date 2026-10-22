@php
    $id = app()->getLocale() === 'id';
    $socials = array_filter([
        'instagram' => [$site->instagram_url, 'fa-brands fa-instagram'],
        'linkedin' => [$site->linkedin_url, 'fa-brands fa-linkedin-in'],
        'tiktok' => [$site->tiktok_url, 'fa-brands fa-tiktok'],
        'youtube' => [$site->youtube_url, 'fa-brands fa-youtube'],
    ], fn ($s) => ! empty($s[0]));
@endphp

<footer class="mt-16 bg-[#17123A] text-white dark:border-t dark:border-line dark:bg-surface md:mt-24">
    <div class="container-page py-14 md:py-16">
        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-12">
            <div class="lg:col-span-5">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                    @if ($site->site_logo)
                        <img src="{{ $site->logo_url }}" alt="{{ $site->site_name }}" class="h-11 w-auto rounded-md bg-white p-1.5" loading="lazy">
                    @else
                        <span class="flex h-11 w-11 items-center justify-center rounded-full bg-brand-gradient font-extrabold">FK</span>
                    @endif
                    <span class="text-xl font-extrabold text-white">{{ $site->site_name ?: 'FKStudio' }}</span>
                </a>
                @if ($site->site_description)
                    <p class="mt-4 max-w-md text-small leading-relaxed text-white/70">{{ $site->site_description }}</p>
                @endif
                @if ($socials)
                    <div class="mt-6 flex gap-3">
                        @foreach ($socials as $name => [$url, $icon])
                            <a href="{{ $url }}" target="_blank" rel="noopener" aria-label="{{ ucfirst($name) }}"
                                class="flex h-11 w-11 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-brand-600">
                                <i class="{{ $icon }}" aria-hidden="true"></i>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="lg:col-span-3">
                <h3 class="text-small font-bold text-white">{{ $id ? 'Jelajahi' : 'Explore' }}</h3>
                <ul class="mt-4 space-y-3 text-small text-white/70">
                    <li><a class="transition hover:text-white" href="{{ route('home') }}#about">{{ $id ? 'Tentang Kami' : 'About Us' }}</a></li>
                    <li><a class="transition hover:text-white" href="{{ route('home') }}#services">{{ $id ? 'Layanan' : 'Services' }}</a></li>
                    <li><a class="transition hover:text-white" href="{{ route('portfolio.index') }}">{{ $id ? 'Portofolio' : 'Portfolio' }}</a></li>
                    <li><a class="transition hover:text-white" href="{{ route('package.index') }}">{{ $id ? 'Produk & Paket' : 'Products & Packages' }}</a></li>
                    <li><a class="transition hover:text-white" href="{{ route('blog.index') }}">Blog</a></li>
                </ul>
            </div>

            <div class="lg:col-span-4">
                <h3 class="text-small font-bold text-white">{{ $id ? 'Hubungi Kami' : 'Contact' }}</h3>
                <ul class="mt-4 space-y-4 text-small text-white/70">
                    @if ($site->contact_address)
                        <li class="flex gap-3"><i class="fa-solid fa-location-dot mt-1 w-4 text-pink-300" aria-hidden="true"></i><span>{{ $site->contact_address }}</span></li>
                    @endif
                    @if ($site->contact_email)
                        <li class="flex gap-3"><i class="fa-solid fa-envelope mt-1 w-4 text-pink-300" aria-hidden="true"></i><a class="break-all transition hover:text-white" href="mailto:{{ $site->contact_email }}">{{ $site->contact_email }}</a></li>
                    @endif
                    @if ($site->contact_phone)
                        <li class="flex gap-3"><i class="fa-solid fa-phone mt-1 w-4 text-pink-300" aria-hidden="true"></i><a class="transition hover:text-white" href="tel:{{ $site->contact_phone }}">{{ $site->contact_phone }}</a></li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="mt-12 flex flex-col items-center justify-between gap-2 border-t border-white/10 pt-6 text-caption text-white/60 sm:flex-row">
            <p>&copy; {{ date('Y') }} {{ $site->site_name ?: 'FKStudio' }}. {{ $id ? 'Hak cipta dilindungi.' : 'All rights reserved.' }}</p>
            <p>{{ $id ? 'Dibuat dengan' : 'Made with' }} <i class="fa-solid fa-heart text-pink-400" aria-hidden="true"></i> Indonesia</p>
        </div>
    </div>
</footer>

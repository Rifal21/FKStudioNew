@php
    $id = app()->getLocale() === 'id';
    $slides = $heroSlides->isNotEmpty()
        ? $heroSlides->map(fn ($s) => $s->media_url)->filter()->values()
        : ($hero->image ? collect([$hero->media_url]) : collect());

    $types = [
        'profil' => [$id ? 'Company profile' : 'Company site', 'namabisnis.fkstudio.id'],
        'toko' => [$id ? 'Toko online' : 'Online store', 'tokokopi.fkstudio.id'],
        'landing' => [$id ? 'Landing page' : 'Landing page', 'promo.fkstudio.id'],
        'aplikasi' => [$id ? 'Aplikasi web' : 'Web app', 'app.fkstudio.id'],
    ];

    $assurances = $id
        ? ['Bisa bayar DP 50%', 'Pantau progres di akun Anda', 'Subdomain .fkstudio.id gratis']
        : ['Pay a 50% deposit', 'Track progress in your account', 'Free .fkstudio.id subdomain'];
@endphp

<section id="home" class="relative overflow-hidden border-b-2 border-edge bg-brand-100 pb-24 pt-28 dark:bg-surface-2 md:pb-32 md:pt-36"
    x-data="{ type: 'profil', tab: 'demo', urls: @js(collect($types)->map(fn ($t) => $t[1])) }">

    <div class="container-page relative">
        <div class="grid grid-cols-1 items-center gap-16 lg:grid-cols-12 lg:gap-6">
            {{-- Copy --}}
            <div class="min-w-0 lg:col-span-6">
                <h1 class="font-display text-[2.75rem] font-extrabold leading-[0.98] tracking-tight [font-stretch:80%] sm:text-7xl lg:text-[5.25rem]">
                    {{ $hero->getTranslation('title') }}
                </h1>

                <p class="mt-7 max-w-lg text-base text-ink/80 sm:text-lg">
                    {{ $hero->getTranslation('subtitle') ?? ($id
                        ? 'Kami menghadirkan jasa pembuatan website custom, platform e-commerce, dan aplikasi mobile berkinerja tinggi yang dirancang khusus untuk mempercepat pertumbuhan bisnis Anda.'
                        : 'We deliver custom websites, e-commerce platforms, and high-performance mobile apps built to accelerate your business growth.') }}
                </p>

                <div class="mt-9 flex flex-col gap-4 sm:flex-row">
                    <a href="{{ $hero->cta_link ?? route('package.index') }}" class="btn-primary btn-lg">
                        {{ $hero->getTranslation('cta_text') ?? ($id ? 'Mulai konsultasi' : 'Start a consultation') }}
                        <i class="fa-solid fa-arrow-right text-small" aria-hidden="true"></i>
                    </a>
                    <a href="#portfolio" class="btn-outline btn-lg">{{ $id ? 'Lihat portofolio' : 'See our work' }}</a>
                </div>

                <ul class="mt-9 space-y-2.5 text-small font-semibold text-ink/85">
                    @foreach ($assurances as $line)
                        <li class="flex items-center gap-3">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded border-2 border-edge bg-sun-400 text-[0.6rem] text-navy"><i class="fa-solid fa-check" aria-hidden="true"></i></span>
                            {{ $line }}
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Preview: a sun, a browser window and a phone --}}
            <div class="relative min-w-0 lg:col-span-6 lg:pl-6">
                <span class="pointer-events-none absolute -right-24 -top-12 h-[20rem] w-[20rem] rounded-full border-2 border-edge bg-sun-400 sm:-right-28 sm:-top-14 sm:h-[28rem] sm:w-[28rem]" aria-hidden="true"></span>

                <div class="relative">
                    <span class="sticker absolute -right-2 -top-5 z-30 rotate-6 sm:-right-4">
                        <i class="fa-solid fa-hand-holding-dollar" aria-hidden="true"></i>
                        {{ $id ? 'Bisa DP 50%' : '50% deposit' }}
                    </span>

                    <div class="pop relative overflow-hidden rounded-xl bg-surface shadow-hard-lg lg:rotate-[1.2deg]">
                        <div class="flex items-center gap-3 border-b-2 border-edge bg-sun-300 px-4 py-2.5">
                            <span class="flex gap-1.5" aria-hidden="true">
                                <span class="h-3 w-3 rounded-full border-2 border-navy bg-white"></span>
                                <span class="h-3 w-3 rounded-full border-2 border-navy bg-brand-300"></span>
                                <span class="h-3 w-3 rounded-full border-2 border-navy bg-navy"></span>
                            </span>
                            <span class="min-w-0 flex-1 truncate rounded-full border-2 border-navy bg-white px-3 py-0.5 text-caption font-semibold text-navy">
                                <i class="fa-solid fa-lock mr-1 text-[0.65rem]" aria-hidden="true"></i><span x-text="tab === 'demo' ? urls[type] : '{{ $id ? 'hasil-kerja-kami' : 'our-work' }}'">namabisnis.fkstudio.id</span>
                            </span>
                            @if ($slides->isNotEmpty())
                                <span class="flex shrink-0 gap-1" role="tablist">
                                    @foreach (['demo' => ($id ? 'Contoh' : 'Preview'), 'work' => ($id ? 'Hasil kami' : 'Our work')] as $key => $label)
                                        <button type="button" role="tab" @click="tab = '{{ $key }}'" :aria-selected="tab === '{{ $key }}'"
                                            class="rounded-full border-2 border-navy px-2.5 py-0.5 text-caption font-bold transition"
                                            :class="tab === '{{ $key }}' ? 'bg-navy text-white' : 'bg-white text-navy hover:bg-brand-100'">{{ $label }}</button>
                                    @endforeach
                                </span>
                            @endif
                        </div>

                        <div class="relative aspect-[4/3] bg-bg">
                            {{-- Layer 1: sample sites that assemble themselves --}}
                            <div class="absolute inset-0 transition-opacity duration-300" :class="tab !== 'demo' && '!pointer-events-none !opacity-0'" :aria-hidden="tab !== 'demo'">
                                {{-- Company profile --}}
                                <div x-show="type === 'profil'" class="absolute inset-0 flex flex-col gap-4 p-4 sm:p-5">
                                    <div class="bi flex items-center gap-3" style="--d:.05s">
                                        <span class="h-6 w-6 rounded-md border-2 border-edge bg-sun-400"></span><span class="h-2.5 w-20 rounded-full bg-ink/80"></span>
                                        <span class="ml-auto flex gap-2"><span class="h-2 w-8 rounded-full bg-ink/20"></span><span class="h-2 w-8 rounded-full bg-ink/20"></span><span class="h-2 w-8 rounded-full bg-ink/20"></span></span>
                                    </div>
                                    <div class="grid flex-1 grid-cols-5 gap-4">
                                        <div class="col-span-3 flex flex-col justify-center gap-2.5">
                                            <span class="bi h-4 w-11/12 rounded-full bg-ink" style="--d:.2s"></span>
                                            <span class="bi h-4 w-7/12 rounded-full bg-ink" style="--d:.3s"></span>
                                            <span class="bi mt-1 h-2 w-full rounded-full bg-ink/25" style="--d:.4s"></span>
                                            <span class="bi h-2 w-10/12 rounded-full bg-ink/25" style="--d:.45s"></span>
                                            <span class="bi mt-2 h-8 w-28 rounded-md border-2 border-edge bg-sun-400 shadow-hard-sm" style="--d:.6s"></span>
                                        </div>
                                        <div class="bi col-span-2 flex items-center justify-center rounded-lg border-2 border-edge bg-brand-200 text-4xl text-brand-700" style="--d:.35s"><i class="fa-solid fa-building"></i></div>
                                    </div>
                                    <div class="grid grid-cols-3 gap-3">
                                        @foreach ([.7, .8, .9] as $d)
                                            <div class="bi rounded-lg border-2 border-edge/60 bg-surface p-2.5" style="--d:{{ $d }}s"><span class="block h-5 w-5 rounded bg-sun-300"></span><span class="mt-2 block h-2 w-3/4 rounded-full bg-ink/70"></span><span class="mt-1.5 block h-2 w-1/2 rounded-full bg-ink/20"></span></div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Online store --}}
                                <div x-show="type === 'toko'" x-cloak class="absolute inset-0 flex flex-col gap-3 p-4 sm:p-5">
                                    <div class="bi flex items-center gap-3" style="--d:.05s">
                                        <span class="h-6 w-6 rounded-md border-2 border-edge bg-sun-400"></span><span class="h-2.5 w-16 rounded-full bg-ink/80"></span>
                                        <span class="ml-auto flex h-7 w-7 items-center justify-center rounded-full border-2 border-edge bg-surface text-caption"><i class="fa-solid fa-bag-shopping"></i></span>
                                    </div>
                                    <div class="bi flex items-center justify-between rounded-lg border-2 border-edge bg-sun-300 px-4 py-3" style="--d:.2s">
                                        <span class="space-y-2"><span class="block h-3.5 w-28 rounded-full bg-navy"></span><span class="block h-2 w-20 rounded-full bg-navy/40"></span></span>
                                        <span class="h-7 w-16 rounded-md border-2 border-navy bg-white"></span>
                                    </div>
                                    <div class="grid flex-1 grid-cols-4 gap-3">
                                        @foreach (['fa-mug-hot' => 'bg-brand-200', 'fa-shirt' => 'bg-sun-200', 'fa-bag-shopping' => 'bg-brand-100', 'fa-gift' => 'bg-sun-100'] as $icon => $tone)
                                            <div class="bi flex flex-col rounded-lg border-2 border-edge/60 bg-surface p-2" style="--d:{{ .35 + $loop->index * .1 }}s">
                                                <span class="flex flex-1 items-center justify-center rounded-md text-xl text-ink/70 {{ $tone }}"><i class="fa-solid {{ $icon }}"></i></span>
                                                <span class="mt-2 block h-2 w-3/4 rounded-full bg-ink/70"></span>
                                                <span class="mt-1.5 flex items-center justify-between"><span class="block h-2 w-1/3 rounded-full bg-sun-500"></span><span class="h-4 w-4 rounded border-2 border-edge bg-sun-400"></span></span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Landing page --}}
                                <div x-show="type === 'landing'" x-cloak class="absolute inset-0 flex flex-col items-center gap-3 p-4 text-center sm:p-5">
                                    <div class="bi flex w-full items-center gap-3" style="--d:.05s">
                                        <span class="h-6 w-6 rounded-md border-2 border-edge bg-sun-400"></span>
                                        <span class="ml-auto h-6 w-20 rounded-md border-2 border-edge bg-surface"></span>
                                    </div>
                                    <div class="flex flex-1 flex-col items-center justify-center gap-2.5">
                                        <span class="bi h-4 w-64 max-w-[80%] rounded-full bg-ink" style="--d:.2s"></span>
                                        <span class="bi h-4 w-40 max-w-[55%] rounded-full bg-ink" style="--d:.3s"></span>
                                        <span class="bi mt-1 h-2 w-56 max-w-[70%] rounded-full bg-ink/25" style="--d:.4s"></span>
                                        <span class="bi mt-3 flex w-72 max-w-[85%] gap-2" style="--d:.55s"><span class="h-9 flex-1 rounded-md border-2 border-edge bg-surface"></span><span class="h-9 w-24 rounded-md border-2 border-edge bg-sun-400 shadow-hard-sm"></span></span>
                                    </div>
                                    <div class="grid w-full grid-cols-3 gap-3">
                                        @foreach ([.7, .8, .9] as $d)
                                            <div class="bi flex items-center gap-2 rounded-lg border-2 border-edge/60 bg-surface p-2" style="--d:{{ $d }}s"><span class="flex h-5 w-5 shrink-0 items-center justify-center rounded border-2 border-edge bg-sun-400 text-[0.55rem] text-navy"><i class="fa-solid fa-check"></i></span><span class="h-2 w-full rounded-full bg-ink/40"></span></div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Web app --}}
                                <div x-show="type === 'aplikasi'" x-cloak class="absolute inset-0 flex gap-3 p-4 sm:p-5">
                                    <div class="bi flex w-10 shrink-0 flex-col items-center gap-3 rounded-lg border-2 border-edge/60 bg-surface py-3" style="--d:.05s">
                                        <span class="h-5 w-5 rounded border-2 border-edge bg-sun-400"></span><span class="h-4 w-4 rounded bg-ink/25"></span><span class="h-4 w-4 rounded bg-ink/25"></span><span class="h-4 w-4 rounded bg-ink/25"></span>
                                    </div>
                                    <div class="flex min-w-0 flex-1 flex-col gap-3">
                                        <div class="grid grid-cols-3 gap-3">
                                            @foreach ([.15, .25, .35] as $d)
                                                <div class="bi rounded-lg border-2 border-edge/60 bg-surface p-2.5" style="--d:{{ $d }}s"><span class="block h-2 w-1/2 rounded-full bg-ink/30"></span><span class="mt-2 block h-4 w-3/4 rounded-full {{ $loop->index === 0 ? 'bg-sun-400' : 'bg-brand-300' }}"></span></div>
                                            @endforeach
                                        </div>
                                        <div class="bi flex flex-1 items-end gap-2 rounded-lg border-2 border-edge/60 bg-surface p-3" style="--d:.5s">
                                            @foreach ([40, 62, 48, 80, 58, 92, 70] as $h)
                                                <span class="flex-1 rounded-t {{ $loop->last ? 'bg-sun-400' : 'bg-brand-300' }}" style="height: {{ $h }}%"></span>
                                            @endforeach
                                        </div>
                                        <div class="bi space-y-2" style="--d:.7s"><span class="block h-2.5 w-full rounded-full bg-ink/20"></span><span class="block h-2.5 w-4/5 rounded-full bg-ink/20"></span></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Layer 2: real work from the CMS --}}
                            @if ($slides->isNotEmpty())
                                <div class="pointer-events-none absolute inset-0 opacity-0 transition-opacity duration-300" :class="tab === 'work' && '!pointer-events-auto !opacity-100'" :aria-hidden="tab !== 'work'">
                                    <div class="swiper js-hero h-full w-full">
                                        <div class="swiper-wrapper">
                                            @foreach ($slides as $i => $src)
                                                <div class="swiper-slide bg-brand-gradient-soft">
                                                    <img src="{{ $src }}" alt="{{ $id ? 'Contoh hasil kerja FKStudio' : 'FKStudio showcase' }}" class="h-full w-full object-cover" width="800" height="600" loading="lazy">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @if ($slides->count() > 1)
                                        <div class="absolute inset-x-0 bottom-3 z-10 flex items-center justify-center gap-3">
                                            <button type="button" class="js-hero-prev flex h-8 w-8 items-center justify-center rounded-full border-2 border-navy bg-white text-navy" aria-label="{{ $id ? 'Slide sebelumnya' : 'Previous slide' }}"><i class="fa-solid fa-chevron-left text-caption" aria-hidden="true"></i></button>
                                            <div class="js-hero-pagination swiper-pagination !static !w-auto rounded-full bg-white px-2 py-1"></div>
                                            <button type="button" class="js-hero-next flex h-8 w-8 items-center justify-center rounded-full border-2 border-navy bg-white text-navy" aria-label="{{ $id ? 'Slide berikutnya' : 'Next slide' }}"><i class="fa-solid fa-chevron-right text-caption" aria-hidden="true"></i></button>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Phone: same site, small screen --}}
                    <div class="pop absolute -bottom-10 -left-3 z-20 hidden w-[7.25rem] -rotate-3 rounded-[1.4rem] bg-surface p-1.5 shadow-hard sm:block lg:-left-12 lg:w-32" aria-hidden="true">
                        <div class="relative aspect-[9/17] overflow-hidden rounded-[1rem] border-2 border-edge/50 bg-bg p-2">
                            <span class="mx-auto block h-1 w-8 rounded-full bg-ink/30"></span>
                            <div class="mt-2 flex items-center gap-1.5"><span class="h-3 w-3 rounded border border-edge bg-sun-400"></span><span class="ml-auto h-2 w-3 rounded-sm bg-ink/40"></span></div>
                            <span class="mt-2.5 block aspect-[4/3] rounded-md border-2 border-edge bg-brand-200"></span>
                            <span class="mt-2.5 block h-2 w-5/6 rounded-full bg-ink"></span>
                            <span class="mt-1.5 block h-2 w-3/5 rounded-full bg-ink"></span>
                            <span class="mt-2 block h-1.5 w-full rounded-full bg-ink/25"></span>
                            <span class="mt-3 block h-5 w-full rounded border-2 border-edge bg-sun-400"></span>
                            <span class="mt-2.5 grid grid-cols-2 gap-1.5"><span class="h-8 rounded bg-brand-100"></span><span class="h-8 rounded bg-sun-200"></span></span>
                        </div>
                    </div>
                </div>

                <div class="relative mt-16 sm:pl-24 lg:pl-20" role="group" aria-labelledby="hero-type-label">
                    <p id="hero-type-label" class="text-small font-bold">{{ $id ? 'Mau bikin apa? Lihat contoh tampilannya.' : 'What do you want to build? See a sample.' }}</p>
                    <div class="mt-3 flex flex-wrap gap-2.5">
                        @foreach ($types as $key => [$label])
                            <button type="button" @click="type = '{{ $key }}'; tab = 'demo'" :aria-pressed="type === '{{ $key }}'"
                                class="rounded-full border-2 border-edge px-4 py-2 text-small font-bold transition"
                                :class="type === '{{ $key }}' ? 'bg-ink text-bg shadow-hard-sm' : 'bg-surface text-ink hover:bg-brand-200 dark:hover:bg-brand-soft'">{{ $label }}</button>
                        @endforeach
                    </div>
                    <p class="mt-3 text-caption text-ink/70">{{ $id ? 'Contoh ilustrasi. Hasil sebenarnya menyesuaikan brand Anda.' : 'Illustrative sample. The real result is tailored to your brand.' }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

@php
    $id = app()->getLocale() === 'id';
    $tiles = ['bg-sun-300', 'bg-brand-200', 'bg-white', 'bg-brand-100', 'bg-sun-200'];
    $digits = preg_replace('/[^0-9]/', '', $settings->contact_phone ?? '');
    if ($digits && str_starts_with($digits, '0')) $digits = '62' . substr($digits, 1);
@endphp

@if ($services->isNotEmpty())
    <section id="services" class="scroll-mt-20 border-y-2 border-edge bg-surface py-16 md:py-24">
        <div class="container-page">
            <div class="grid gap-12 lg:grid-cols-12 lg:gap-16">
                <div class="lg:col-span-4">
                    <div class="lg:sticky lg:top-28">
                        <h2 class="section-title">{{ $id ? 'Yang bisa kami bangun untuk bisnis Anda' : 'What we can build for your business' }}</h2>
                        <p class="section-lead">{{ $id ? 'Dari company profile sampai toko online dan aplikasi mobile.' : 'From company profiles to online stores and mobile apps.' }}</p>
                        @if ($digits)
                            <a href="https://wa.me/{{ $digits }}" target="_blank" rel="noopener" class="btn-outline mt-8">
                                <i class="fa-brands fa-whatsapp" aria-hidden="true"></i> {{ $id ? 'Tanya kebutuhan Anda' : 'Ask about your project' }}
                            </a>
                        @endif
                    </div>
                </div>

                <ul class="min-w-0 lg:col-span-8">
                    @foreach ($services as $i => $service)
                        <li class="group relative flex items-start gap-5 border-t-2 border-edge/25 py-7 transition-all duration-200 last:border-b-2 hover:bg-sun-100/70 sm:gap-7 md:px-4 md:hover:pl-7 dark:hover:bg-sun-400/10">
                            <span class="flex h-16 w-16 shrink-0 items-center justify-center rounded-lg border-2 border-edge text-2xl text-navy shadow-hard-sm transition duration-200 group-hover:-rotate-6 {{ $tiles[$i % count($tiles)] }}">
                                <i class="{{ $service->icon ?? 'fa-solid fa-rocket' }}" aria-hidden="true"></i>
                            </span>
                            <div class="min-w-0 flex-1">
                                <h3 class="text-2xl leading-tight">{{ $service->getTranslation('title') }}</h3>
                                <p class="mt-2 max-w-xl text-muted">{{ $service->getTranslation('description') }}</p>
                            </div>
                            <span class="mt-1 hidden h-10 w-10 shrink-0 items-center justify-center rounded-full border-2 border-edge transition group-hover:bg-sun-400 group-hover:text-navy sm:flex" aria-hidden="true"><i class="fa-solid fa-plus transition group-hover:rotate-90"></i></span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>
@endif

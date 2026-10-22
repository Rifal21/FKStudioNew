@php
    $id = app()->getLocale() === 'id';
    $tones = [
        'bg-brand-50 text-brand-700 dark:bg-brand-500/20 dark:text-brand-300',
        'bg-pink-50 text-pink-700 dark:bg-pink-500/15 dark:text-pink-300',
        'bg-sun-50 text-sun-700 dark:bg-sun-400/15 dark:text-sun-300',
        'bg-mint-50 text-mint-800 dark:bg-mint-400/15 dark:text-mint-300',
        'bg-sky-50 text-sky-800 dark:bg-sky-400/15 dark:text-sky-300',
    ];
@endphp

@if ($services->isNotEmpty())
    <section id="services" class="section scroll-mt-20 bg-surface-2/60">
        <div class="container-page">
            <x-ui.section-heading
                :eyebrow="$id ? 'Layanan' : 'Services'"
                :title="$id ? 'Solusi digital untuk setiap kebutuhan bisnis' : 'Digital solutions for every business need'"
                :lead="$id ? 'Dari website company profile sampai platform e-commerce, kami bantu wujudkan dengan kualitas terbaik.' : 'From company websites to full e-commerce platforms, we build it with care.'" />

            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($services as $i => $service)
                    <article class="card card-pad card-hover" data-aos="fade-up" data-aos-delay="{{ ($i % 3) * 80 }}">
                        <span class="flex h-14 w-14 items-center justify-center rounded-lg text-2xl {{ $tones[$i % count($tones)] }}">
                            <i class="{{ $service->icon ?? 'fa-solid fa-rocket' }}" aria-hidden="true"></i>
                        </span>
                        <h3 class="mt-5 text-xl">{{ $service->getTranslation('title') }}</h3>
                        <p class="mt-2 text-muted">{{ $service->getTranslation('description') }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif

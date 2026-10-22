@php
    $id = app()->getLocale() === 'id';
    $paper = ['bg-sun-200', 'bg-white', 'bg-brand-200', 'bg-sun-100'];
    $tilt = ['md:-rotate-2', 'md:rotate-1', 'md:rotate-2', 'md:-rotate-1'];
@endphp

@if ($testimonials->isNotEmpty())
    <section id="testimonials" class="scroll-mt-20 pb-24 pt-8 md:pb-32 md:pt-12">
        <div class="container-page grid gap-12 lg:grid-cols-12 lg:gap-10">
            <div class="lg:col-span-4">
                <h2 class="section-title lg:sticky lg:top-28">{{ $id ? 'Kata mereka yang sudah memakai jasa kami' : 'What clients say after working with us' }}</h2>
            </div>

            <div class="grid items-start gap-x-8 gap-y-10 pt-4 md:grid-cols-2 lg:col-span-8">
                @foreach ($testimonials as $i => $tm)
                    <figure class="relative rounded-md border-2 border-edge p-6 text-navy shadow-hard {{ $paper[$i % 4] }} {{ $tilt[$i % 4] }} {{ $i % 2 ? 'md:mt-12' : '' }}">
                        <span class="tape -top-3 left-1/2 -translate-x-1/2 {{ $i % 2 ? 'rotate-3' : '-rotate-2' }}" aria-hidden="true"></span>
                        <div class="flex gap-0.5 text-navy" role="img" aria-label="{{ $tm->rating ?? 5 }} / 5">
                            @for ($s = 0; $s < ($tm->rating ?? 5); $s++)<i class="fa-solid fa-star text-small" aria-hidden="true"></i>@endfor
                        </div>
                        <blockquote class="mt-4 font-display text-xl font-semibold leading-snug">&ldquo;{{ $tm->getTranslation('content') }}&rdquo;</blockquote>
                        <figcaption class="mt-5 flex items-center gap-3 border-t-2 border-navy/20 pt-4">
                            @if ($tm->avatar)
                                <img src="{{ $tm->media_url }}" alt="{{ $tm->name }}" loading="lazy" class="h-11 w-11 rounded-full border-2 border-navy object-cover">
                            @else
                                <span class="flex h-11 w-11 items-center justify-center rounded-full border-2 border-navy bg-white font-bold">{{ mb_strtoupper(mb_substr($tm->name, 0, 1)) }}</span>
                            @endif
                            <div class="min-w-0">
                                <p class="truncate font-bold">{{ $tm->name }}</p>
                                <p class="truncate text-small text-navy/70">{{ $tm->getTranslation('role') }}</p>
                            </div>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        </div>
    </section>
@endif

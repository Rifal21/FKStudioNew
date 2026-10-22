@php
    $id = app()->getLocale() === 'id';
    $paper = ['bg-sun-200', 'bg-white', 'bg-brand-200', 'bg-sun-100'];
    $tilt = ['md:-rotate-2', 'md:rotate-1', 'md:rotate-2', 'md:-rotate-1'];
@endphp

@if ($testimonials->isNotEmpty())
    <section id="testimonials" class="section scroll-mt-20">
        <div class="container-page">
            <h2 class="section-title max-w-2xl">{{ $id ? 'Apa kata klien kami' : 'What our clients say' }}</h2>

            <div class="mt-16 columns-1 gap-8 md:columns-2 lg:columns-3">
                @foreach ($testimonials as $i => $tm)
                    <figure class="relative mb-10 break-inside-avoid rounded-md border-2 border-edge p-6 text-navy shadow-hard {{ $paper[$i % 4] }} {{ $tilt[$i % 4] }}">
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

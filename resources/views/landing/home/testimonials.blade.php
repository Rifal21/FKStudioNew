@php($id = app()->getLocale() === 'id')

@if ($testimonials->isNotEmpty())
    <section id="testimonials" class="section scroll-mt-20 bg-surface-2/60">
        <div class="container-page">
            <x-ui.section-heading
                :eyebrow="$id ? 'Testimoni' : 'Testimonials'"
                :title="$id ? 'Apa kata klien kami' : 'What our clients say'" />

            <div class="swiper js-testimonials mt-12 !pb-2">
                <div class="swiper-wrapper">
                    @foreach ($testimonials as $tm)
                        <div class="swiper-slide !h-auto">
                            <figure class="card card-pad flex h-full flex-col">
                                <div class="flex gap-0.5 text-sun-500" role="img" aria-label="{{ $tm->rating ?? 5 }} / 5">
                                    @for ($i = 0; $i < ($tm->rating ?? 5); $i++)<i class="fa-solid fa-star" aria-hidden="true"></i>@endfor
                                </div>
                                <blockquote class="mt-4 flex-1 text-body">&ldquo;{{ $tm->getTranslation('content') }}&rdquo;</blockquote>
                                <figcaption class="mt-6 flex items-center gap-3 border-t border-line pt-5">
                                    @if ($tm->avatar)
                                        <img src="{{ $tm->media_url }}" alt="{{ $tm->name }}" loading="lazy" class="h-12 w-12 rounded-full object-cover">
                                    @else
                                        <span class="flex h-12 w-12 items-center justify-center rounded-full bg-brand-gradient font-bold text-white">{{ mb_strtoupper(mb_substr($tm->name, 0, 1)) }}</span>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="truncate font-bold">{{ $tm->name }}</p>
                                        <p class="truncate text-small text-muted">{{ $tm->getTranslation('role') }}</p>
                                    </div>
                                </figcaption>
                            </figure>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-8 flex items-center justify-center gap-4">
                <button type="button" class="js-testimonials-prev btn-icon border border-line bg-surface" aria-label="{{ $id ? 'Sebelumnya' : 'Previous' }}"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i></button>
                <div class="js-testimonials-pagination swiper-pagination !static !w-auto"></div>
                <button type="button" class="js-testimonials-next btn-icon border border-line bg-surface" aria-label="{{ $id ? 'Berikutnya' : 'Next' }}"><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
            </div>
        </div>
    </section>
@endif

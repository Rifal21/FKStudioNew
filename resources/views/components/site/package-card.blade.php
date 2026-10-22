@props(['package'])

@php
    $id = app()->getLocale() === 'id';
    $features = $id ? $package->features_id : $package->features_en;
    $isNumericPrice = is_numeric(str_replace(['Rp', '.', ',', ' '], '', $package->price));
    $featured = (bool) $package->is_featured;
@endphp

<article @class([
    'pop relative flex flex-col rounded-lg',
    'bg-sun-300 text-navy shadow-hard-lg lg:-translate-y-4' => $featured,
    'bg-surface' => ! $featured,
])>
    @if ($featured)
        <span class="sticker absolute -right-3 -top-4 rotate-3 !bg-navy !text-white !border-navy">
            <i class="fa-solid fa-star text-sun-400" aria-hidden="true"></i> {{ $id ? 'Paling dipilih' : 'Most popular' }}
        </span>
    @endif

    <div class="p-6 md:p-7">
        <h3 @class(['text-2xl', '!text-navy' => $featured])>{{ $package->getTranslation('name') }}</h3>
        <p @class(['mt-2 text-small', 'text-navy/75' => $featured, 'text-muted' => ! $featured])>{{ Str::limit($package->getTranslation('description'), 110) }}</p>

        <p class="mt-6 flex flex-wrap items-baseline gap-x-2">
            <span class="font-display text-4xl font-extrabold leading-none">{{ $package->price }}</span>
            @if ($isNumericPrice)<span @class(['text-small', 'text-navy/70' => $featured, 'text-muted' => ! $featured])>/ {{ $id ? 'proyek' : 'project' }}</span>@endif
        </p>
    </div>

    {{-- Tear-off line --}}
    <div class="relative" aria-hidden="true">
        <i @class(['fa-solid fa-scissors absolute -top-2.5 left-5 bg-inherit px-1 text-small', 'text-navy bg-sun-300' => $featured, 'text-ink bg-surface' => ! $featured])></i>
        <div @class(['border-t-[3px] border-dashed', 'border-navy/40' => $featured, 'border-edge/40' => ! $featured])></div>
    </div>

    <div class="flex flex-1 flex-col p-6 md:p-7">
        <ul class="flex-1 space-y-3">
            @foreach ($features ?? [] as $feature)
                <li class="flex items-start gap-3 text-small">
                    <span @class(['mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded border-2 text-[0.6rem]', 'border-navy bg-white text-navy' => $featured, 'border-edge bg-sun-400 text-navy' => ! $featured])><i class="fa-solid fa-check" aria-hidden="true"></i></span>
                    <span>{{ $feature }}</span>
                </li>
            @endforeach
        </ul>

        <a href="{{ route('package.show', $package->slug) }}" @class(['mt-8 w-full', 'btn-navy' => $featured, 'btn-primary' => ! $featured])>
            {{ $id ? 'Lihat detail' : 'View details' }}
            <i class="fa-solid fa-arrow-right text-caption" aria-hidden="true"></i>
        </a>
    </div>
</article>

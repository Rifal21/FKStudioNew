@props(['package'])

@php
    $id = app()->getLocale() === 'id';
    $features = $id ? $package->features_id : $package->features_en;
    $isNumericPrice = is_numeric(str_replace(['Rp', '.', ',', ' '], '', $package->price));
    $featured = (bool) $package->is_featured;
    $paper = $featured ? 'bg-sun-300' : 'bg-surface';
    $cut = $featured ? 'border-navy/40' : 'border-edge/40';
@endphp

<article @class([
    'pop relative grid rounded-lg lg:grid-cols-[minmax(0,1fr)_minmax(0,1.3fr)_15rem]',
    'bg-sun-300 text-navy shadow-hard-lg' => $featured,
    'bg-surface' => ! $featured,
])>
    <div class="p-6 md:p-8">
        @if ($featured)
            <p class="text-small font-bold"><i class="fa-solid fa-star" aria-hidden="true"></i> {{ $id ? 'Paket unggulan' : 'Featured package' }}</p>
        @endif
        <h3 @class(['text-3xl leading-tight', 'mt-2', '!text-navy' => $featured])>{{ $package->getTranslation('name') }}</h3>
        <p @class(['mt-2 max-w-sm text-small', 'text-navy/75' => $featured, 'text-muted' => ! $featured])>{{ Str::limit($package->getTranslation('description'), 120) }}</p>
        <p class="mt-6 flex flex-wrap items-baseline gap-x-2">
            <span class="font-display text-4xl font-extrabold leading-none">{{ $package->price }}</span>
            @if ($isNumericPrice)<span @class(['text-small', 'text-navy/70' => $featured, 'text-muted' => ! $featured])>/ {{ $id ? 'proyek' : 'project' }}</span>@endif
        </p>
    </div>

    <div @class(['relative border-t-[3px] border-dashed p-6 md:p-8 lg:border-l-[3px] lg:border-t-0', $cut])>
        <i @class(['fa-solid fa-scissors absolute -top-3 left-6 px-1 text-small lg:-left-3 lg:top-8 lg:rotate-90', $paper, 'text-navy' => $featured, 'text-ink' => ! $featured]) aria-hidden="true"></i>
        <ul class="grid gap-x-6 gap-y-3 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
            @foreach ($features ?? [] as $feature)
                <li class="flex items-start gap-3 text-small">
                    <span @class(['mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded border-2 text-[0.6rem]', 'border-navy bg-white text-navy' => $featured, 'border-edge bg-sun-400 text-navy' => ! $featured])><i class="fa-solid fa-check" aria-hidden="true"></i></span>
                    <span>{{ $feature }}</span>
                </li>
            @endforeach
        </ul>
    </div>

    <div @class(['flex items-center border-t-[3px] border-dashed p-6 md:p-8 lg:border-l-[3px] lg:border-t-0', $cut])>
        <a href="{{ route('package.show', $package->slug) }}" @class(['w-full', 'btn-navy' => $featured, 'btn-primary' => ! $featured])>
            {{ $id ? 'Lihat detail' : 'View details' }}<span class="sr-only"> {{ $package->getTranslation('name') }}</span>
        </a>
    </div>
</article>

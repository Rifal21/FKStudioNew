@php
    $id = app()->getLocale() === 'id';
    $stats = collect(! empty($about->stats) && is_array($about->stats) ? $about->stats : [])
        ->map(fn ($s) => ['value' => $s['value'] ?? '', 'label' => $id ? ($s['label_id'] ?? '') : ($s['label_en'] ?? '')])
        ->filter(fn ($s) => $s['value'] !== '');

    if ($stats->isEmpty()) {
        $stats = collect([
            ['value' => $projects->count() . '+', 'label' => $id ? 'Proyek selesai' : 'Projects completed'],
            ['value' => $clients->count() . '+', 'label' => $id ? 'Klien & mitra' : 'Clients & partners'],
            ['value' => $testimonials->count() . '+', 'label' => $id ? 'Ulasan pelanggan' : 'Customer reviews'],
        ])->filter(fn ($s) => (int) $s['value'] > 0);
    }
@endphp

@if ($stats->isNotEmpty())
    <section class="container-page py-14 md:py-20">
        <div class="relative overflow-hidden rounded-xl bg-brand-gradient p-8 text-white shadow-pop md:p-12">
            <div class="pointer-events-none absolute -right-16 -top-16 h-56 w-56 rounded-full bg-white/10" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -bottom-20 left-1/3 h-56 w-56 rounded-full bg-sun-400/20" aria-hidden="true"></div>
            <dl class="relative grid grid-cols-2 gap-8 text-center md:grid-flow-col md:auto-cols-fr">
                @foreach ($stats as $stat)
                    <div @class(['col-span-2 md:col-span-1' => $loop->last && $stats->count() % 2 === 1])>
                        <dd class="text-4xl font-extrabold md:text-5xl">{{ $stat['value'] }}</dd>
                        <dt class="mt-2 text-small font-medium text-white/85">{{ $stat['label'] }}</dt>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>
@endif

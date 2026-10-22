@php
    $id = app()->getLocale() === 'id';
    $stats = collect(! empty($about->stats) && is_array($about->stats) ? $about->stats : [])
        ->map(fn ($s) => ['value' => $s['value'] ?? '', 'label' => $id ? ($s['label_id'] ?? '') : ($s['label_en'] ?? '')])
        ->filter(fn ($s) => $s['value'] !== '');

    if ($stats->isEmpty()) {
        $stats = collect([
            ['value' => $projects->count() . '+', 'label' => $id ? 'proyek selesai' : 'projects delivered'],
            ['value' => $clients->count() . '+', 'label' => $id ? 'klien dan mitra' : 'clients and partners'],
            ['value' => $testimonials->count() . '+', 'label' => $id ? 'ulasan pelanggan' : 'customer reviews'],
        ])->filter(fn ($s) => (int) $s['value'] > 0);
    }
@endphp

@if ($stats->isNotEmpty())
    <dl class="mt-10 flex flex-wrap gap-x-8 gap-y-5">
        @foreach ($stats as $stat)
            <div class="border-l-4 border-sun-400 pl-4">
                <dd class="font-display text-3xl font-extrabold leading-none md:text-4xl">{{ $stat['value'] }}</dd>
                <dt class="mt-1 text-small text-muted">{{ $stat['label'] }}</dt>
            </div>
        @endforeach
    </dl>
@endif

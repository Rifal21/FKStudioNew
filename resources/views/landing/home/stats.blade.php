@php
    $id = app()->getLocale() === 'id';
    $stats = collect(! empty($about->stats) && is_array($about->stats) ? $about->stats : [])
        ->map(fn ($s) => ['value' => $s['value'] ?? '', 'label' => $id ? ($s['label_id'] ?? '') : ($s['label_en'] ?? '')])
        ->filter(fn ($s) => $s['value'] !== '');

    if ($stats->isEmpty()) {
        $stats = collect([
            ['value' => $projects->count() . '+', 'label' => $id ? 'proyek selesai' : 'projects delivered'],
            ['value' => $clients->count() . '+', 'label' => $id ? 'klien dan mitra' : 'clients and partners'],
        ])->filter(fn ($s) => (int) $s['value'] > 0);
    }
@endphp

@if ($stats->isNotEmpty())
    <p class="mt-5 max-w-xl text-base">
        @foreach ($stats as $stat)
            <strong class="font-display text-xl font-extrabold">{{ $stat['value'] }}</strong> <span class="text-muted">{{ mb_strtolower($stat['label']) }}{{ $loop->last ? '.' : ',' }}</span>
        @endforeach
    </p>
@endif

<x-app-layout>
    <x-slot name="header">Blog</x-slot>

    @php
        $fmt = fn ($d, $f = 'd F Y') => $d?->locale('id')->translatedFormat($f) ?? '-';
        $peak = max(1, $series->max('total'));
        $author = $blog->author_name ?: $blog->author?->name ?: '-';
    @endphp

    <div class="mx-auto max-w-7xl space-y-6">
        <x-ui.page-header :title="$blog->title_id" :description="$blog->title_en">
            <x-slot:actions>
                <x-ui.button :href="route('dashboard.blogs.index')" variant="secondary" icon="fa-arrow-left">Kembali</x-ui.button>
                <x-ui.button :href="route('dashboard.blogs.index', ['edit' => $blog->id])" variant="secondary" icon="fa-pen">Ubah</x-ui.button>
                @if ($blog->is_published)
                    <x-ui.button :href="route('blog.show', $blog->slug)" target="_blank" rel="noopener" icon="fa-arrow-up-right-from-square">Buka di situs</x-ui.button>
                @endif
            </x-slot:actions>
        </x-ui.page-header>

        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <x-ui.stat-card label="Total pembaca" :value="number_format($stats['total'], 0, ',', '.')" icon="fa-eye" tone="brand" class="h-full" />
            <x-ui.stat-card label="Hari ini" :value="number_format($stats['today'], 0, ',', '.')" icon="fa-calendar-day" tone="sun" class="h-full" />
            <x-ui.stat-card label="7 hari terakhir" :value="number_format($stats['week'], 0, ',', '.')" icon="fa-calendar-week" tone="sky" class="h-full" />
            <x-ui.stat-card label="30 hari terakhir" :value="number_format($stats['month'], 0, ',', '.')" icon="fa-calendar" tone="mint" class="h-full" />
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-5 lg:items-start">
            <div class="space-y-6 lg:col-span-3">
                <x-ui.card :padded="false" class="overflow-hidden">
                    @if ($blog->image)
                        <img src="{{ $blog->media_url }}" alt="{{ $blog->title_id }}" class="aspect-[16/9] w-full object-cover">
                    @endif
                    <div class="p-5 md:p-6">
                        <div class="rich-text">{!! $blog->content !!}</div>
                    </div>
                </x-ui.card>
            </div>

            <div class="space-y-6 lg:col-span-2">
                <x-ui.card>
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h2 class="text-lg font-bold">Info artikel</h2>
                        @if ($blog->is_published)
                            <x-ui.badge variant="success" dot>Terbit</x-ui.badge>
                        @else
                            <x-ui.badge variant="warning" dot>Draf</x-ui.badge>
                        @endif
                    </div>
                    <dl class="mt-4 grid grid-cols-2 gap-x-4 gap-y-4 text-small">
                        <div><dt class="text-caption text-muted">Penulis</dt><dd class="font-bold">{{ $author }}</dd></div>
                        <div><dt class="text-caption text-muted">Kategori</dt><dd class="font-bold">{{ $blog->category_id ?: '-' }}</dd></div>
                        <div><dt class="text-caption text-muted">Tanggal terbit</dt><dd class="font-bold">{{ $fmt($blog->published_at) }}</dd></div>
                        <div><dt class="text-caption text-muted">Terakhir diubah</dt><dd class="font-bold">{{ $fmt($blog->updated_at) }}</dd></div>
                        <div class="col-span-2"><dt class="text-caption text-muted">Alamat</dt><dd class="break-all font-mono text-caption font-semibold">/blog/{{ $blog->slug }}</dd></div>
                    </dl>
                </x-ui.card>

                <x-ui.card>
                    <h2 class="text-lg font-bold">Pembaca per hari</h2>
                    <p class="mt-1 text-small text-muted">30 hari terakhir. Satu orang dihitung sekali per hari.</p>

                    <ol class="mt-5 flex h-32 items-end gap-[3px]" aria-label="Jumlah pembaca per hari, 30 hari terakhir">
                        @foreach ($series as $day)
                            <li class="group relative flex h-full min-w-0 flex-1 items-end" title="{{ $fmt($day['date'], 'd M') }}: {{ $day['total'] }} pembaca">
                                <span class="block w-full rounded-t-sm {{ $day['total'] ? 'bg-brand-500' : 'bg-line' }}"
                                    style="height: {{ $day['total'] ? max(6, round($day['total'] / $peak * 100)) : 3 }}%"></span>
                                <span class="sr-only">{{ $fmt($day['date'], 'd M') }}: {{ $day['total'] }} pembaca</span>
                            </li>
                        @endforeach
                    </ol>
                    <div class="mt-2 flex justify-between text-caption text-muted">
                        <span>{{ $fmt($series->first()['date'], 'd M') }}</span>
                        <span>{{ $fmt($series->last()['date'], 'd M') }}</span>
                    </div>

                    <p class="mt-5 text-caption leading-relaxed text-muted">
                        Mesin pencari, pratinjau tautan, dan akun admin tidak dihitung.
                        @if ($stats['trackedSince'])
                            Rincian per hari tersedia sejak {{ $fmt(\Illuminate\Support\Carbon::parse($stats['trackedSince'])) }}; total di atas juga mencakup pembaca sebelum itu.
                        @endif
                    </p>
                </x-ui.card>
            </div>
        </div>
    </div>
</x-app-layout>

@if ($clients->isNotEmpty())
    @php
        $id = app()->getLocale() === 'id';
        $repeat = max(1, (int) ceil(8 / $clients->count()));
        $half = collect();
        for ($i = 0; $i < $repeat; $i++) { $half = $half->concat($clients); }
    @endphp

    <section class="relative z-10 -mt-6 overflow-x-clip py-10 md:py-14" aria-label="{{ $id ? 'Klien kami' : 'Our clients' }}">
        <div class="-mx-4 -rotate-1 overflow-hidden border-y-2 border-edge bg-sun-400 py-5 shadow-hard sm:-mx-6">
            <div class="flex w-max animate-marquee items-center gap-6 hover:[animation-play-state:paused]">
                @foreach ([false, true] as $isCopy)
                    <div class="flex shrink-0 items-center gap-6" @if ($isCopy) aria-hidden="true" @endif>
                        <span class="shrink-0 font-display text-xl font-extrabold text-navy">{{ $id ? 'Dipercaya oleh' : 'Trusted by' }}</span>
                        @foreach ($half as $client)
                            <div class="flex h-16 w-36 shrink-0 items-center justify-center rounded-md border-2 border-navy bg-white px-4 md:h-[4.5rem] md:w-44">
                                <img src="{{ $client->media_url }}" alt="{{ $isCopy ? '' : ($client->name ?? 'Client') }}" loading="lazy" class="max-h-9 w-auto max-w-full object-contain md:max-h-10">
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif

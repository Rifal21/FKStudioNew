@if ($clients->isNotEmpty())
    @php
        $id = app()->getLocale() === 'id';
        $repeat = max(1, (int) ceil(8 / $clients->count()));
        $half = collect();
        for ($i = 0; $i < $repeat; $i++) { $half = $half->concat($clients); }
    @endphp

    <section class="border-y border-line bg-surface py-12" aria-label="{{ $id ? 'Klien kami' : 'Our clients' }}">
        <p class="container-page text-center text-small font-semibold text-muted">
            {{ $id ? 'Dipercaya oleh brand dan bisnis di berbagai bidang' : 'Trusted by brands and businesses across industries' }}
        </p>

        <div class="relative mt-8 overflow-hidden [mask-image:linear-gradient(to_right,transparent,#000_12%,#000_88%,transparent)]">
            <div class="flex w-max animate-marquee gap-4 hover:[animation-play-state:paused]">
                @foreach ([false, true] as $isCopy)
                    <div class="flex shrink-0 gap-4" @if ($isCopy) aria-hidden="true" @endif>
                        @foreach ($half as $client)
                            <div class="flex h-20 w-40 shrink-0 items-center justify-center rounded-lg border border-line bg-bg px-6 transition hover:border-brand-200 hover:shadow-soft md:h-24 md:w-52">
                                <img src="{{ $client->media_url }}" alt="{{ $isCopy ? '' : ($client->name ?? 'Client') }}" loading="lazy"
                                    class="max-h-10 w-auto max-w-full object-contain opacity-70 grayscale transition hover:opacity-100 hover:grayscale-0 md:max-h-12">
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif

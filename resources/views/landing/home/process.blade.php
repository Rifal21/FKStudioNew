@php
    $id = app()->getLocale() === 'id';
    $digits = preg_replace('/[^0-9]/', '', $settings->contact_phone ?? '');
    if ($digits && str_starts_with($digits, '0')) $digits = '62' . substr($digits, 1);
    $steps = [
        [$id ? 'Konsultasi' : 'Consult', $id ? 'Ceritakan kebutuhan bisnis Anda, kami bantu pilih solusi dan paket yang tepat.' : 'Tell us about your business and we will help pick the right solution and package.'],
        [$id ? 'Desain' : 'Design', $id ? 'Kami merancang tampilan yang sesuai identitas brand dan nyaman di semua perangkat.' : 'We craft a design that fits your brand and works on every device.'],
        [$id ? 'Pengembangan' : 'Build', $id ? 'Website dibangun dan Anda bisa memantau progres langsung dari akun Anda.' : 'We build your website and you can follow progress from your account.'],
        [$id ? 'Peluncuran' : 'Launch', $id ? 'Website siap tayang. Kami tetap mendampingi bila ada kendala atau pertanyaan.' : 'Your site goes live and we stay around for questions and support.'],
    ];
@endphp

<section id="process" class="scroll-mt-20 overflow-hidden border-b-2 border-edge bg-bg py-16 md:py-24">
    <div class="container-page">
        <h2 class="section-title max-w-2xl">{{ $id ? 'Dari obrolan pertama sampai website tayang' : 'From the first chat to a live website' }}</h2>

        {{-- Each step sits one stair higher than the last --}}
        <ol class="mt-12 md:mt-16">
            @foreach ($steps as $i => [$title, $text])
                <li class="flex items-start gap-5 border-t-2 border-edge/30 py-7 last:border-b-2 sm:gap-8 lg:[padding-left:calc(var(--i)*7rem)]" style="--i: {{ $i }}">
                    <span class="w-14 shrink-0 font-display text-7xl font-extrabold leading-[0.8] text-sun-400 [-webkit-text-stroke:2px_rgb(var(--c-edge))] sm:w-20 sm:text-8xl" aria-hidden="true">{{ $i + 1 }}</span>
                    <div class="min-w-0 max-w-md">
                        <h3 class="text-2xl leading-tight">{{ $title }}</h3>
                        <p class="mt-2 text-muted">{{ $text }}</p>
                        @if ($i === 0 && $digits)
                            <a href="https://wa.me/{{ $digits }}" target="_blank" rel="noopener" class="mt-4 inline-block text-small font-bold underline decoration-sun-400 decoration-[3px] underline-offset-4 hover:decoration-brand-500">{{ $id ? 'Mulai dari obrolan di WhatsApp' : 'Start with a WhatsApp chat' }}</a>
                        @endif
                    </div>
                </li>
            @endforeach
        </ol>
    </div>
</section>

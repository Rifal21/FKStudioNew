@php
    $id = app()->getLocale() === 'id';
    $steps = [
        ['fa-comments', $id ? 'Konsultasi' : 'Consult', $id ? 'Ceritakan kebutuhan bisnis Anda, kami bantu pilih solusi dan paket yang tepat.' : 'Tell us about your business and we will help pick the right solution and package.'],
        ['fa-pen-ruler', $id ? 'Desain' : 'Design', $id ? 'Kami merancang tampilan yang sesuai identitas brand dan nyaman di semua perangkat.' : 'We craft a design that fits your brand and works on every device.'],
        ['fa-code', $id ? 'Pengembangan' : 'Build', $id ? 'Website dibangun dan Anda bisa memantau progres langsung dari akun Anda.' : 'We build your website and you can follow progress from your account.'],
        ['fa-rocket', $id ? 'Peluncuran' : 'Launch', $id ? 'Website siap tayang. Kami tetap mendampingi bila ada kendala atau pertanyaan.' : 'Your site goes live and we stay around for questions and support.'],
    ];
@endphp

<section id="process" class="section scroll-mt-20 bg-brand-100/60 dark:bg-surface-2/50">
    <div class="container-page">
        <h2 class="section-title max-w-2xl">{{ $id ? 'Empat langkah menuju website impian' : 'Four steps to your dream website' }}</h2>

        <ol class="relative mt-16 grid gap-x-6 gap-y-14 md:grid-cols-2 lg:grid-cols-4">
            <span class="absolute left-7 right-7 top-7 hidden border-t-[3px] border-dashed border-edge/50 lg:block" aria-hidden="true"></span>
            @foreach ($steps as $i => [$icon, $title, $text])
                <li class="relative">
                    <span class="relative z-10 flex h-14 w-14 items-center justify-center rounded-full border-2 border-edge bg-sun-400 font-display text-2xl font-extrabold text-navy shadow-hard-sm">{{ $i + 1 }}</span>
                    <div class="pop mt-5 rounded-lg bg-surface p-5 {{ $i % 2 ? 'lg:translate-y-6' : '' }}">
                        <i class="fa-solid {{ $icon }} text-xl text-brand-600" aria-hidden="true"></i>
                        <h3 class="mt-3 text-xl">{{ $title }}</h3>
                        <p class="mt-2 text-small text-muted">{{ $text }}</p>
                    </div>
                </li>
            @endforeach
        </ol>
    </div>
</section>

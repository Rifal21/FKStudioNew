@php
    $id = app()->getLocale() === 'id';
    $steps = [
        ['fa-comments', $id ? 'Konsultasi' : 'Consult', $id ? 'Ceritakan kebutuhan bisnis Anda, kami bantu pilih solusi dan paket yang tepat.' : 'Tell us about your business and we will help pick the right solution.', 'bg-brand-600 text-white'],
        ['fa-pen-ruler', $id ? 'Desain' : 'Design', $id ? 'Kami merancang tampilan yang sesuai identitas brand dan nyaman di semua perangkat.' : 'We craft a design that fits your brand and works on every device.', 'bg-pink-500 text-white'],
        ['fa-code', $id ? 'Pengembangan' : 'Build', $id ? 'Website dibangun dan Anda bisa memantau progres langsung dari akun Anda.' : 'We build your website and you can follow progress from your account.', 'bg-sun-400 text-[#17123A]'],
        ['fa-rocket', $id ? 'Peluncuran' : 'Launch', $id ? 'Website siap tayang. Kami tetap mendampingi bila ada kendala atau pertanyaan.' : 'Your site goes live and we stay around for questions and support.', 'bg-mint-400 text-[#17123A]'],
    ];
@endphp

<section id="process" class="section scroll-mt-20">
    <div class="container-page">
        <x-ui.section-heading
            :eyebrow="$id ? 'Cara kerja' : 'How it works'"
            :title="$id ? 'Empat langkah menuju website impian' : 'Four steps to your dream website'" />

        <ol class="relative mt-14 grid gap-8 md:grid-cols-2 lg:grid-cols-4">
            <span class="absolute left-0 right-0 top-7 hidden h-0.5 bg-gradient-to-r from-brand-300 via-pink-300 to-mint-300 lg:block" aria-hidden="true"></span>
            @foreach ($steps as $i => [$icon, $title, $text, $tone])
                <li class="relative text-center md:text-left lg:text-center" data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
                    <span class="relative z-10 mx-auto flex h-14 w-14 items-center justify-center rounded-full text-xl shadow-card ring-4 ring-bg md:mx-0 lg:mx-auto {{ $tone }}">
                        <i class="fa-solid {{ $icon }}" aria-hidden="true"></i>
                    </span>
                    <p class="mt-5 text-caption font-bold uppercase tracking-wider text-brand-text">{{ $id ? 'Langkah' : 'Step' }} {{ $i + 1 }}</p>
                    <h3 class="mt-1 text-xl">{{ $title }}</h3>
                    <p class="mt-2 text-muted">{{ $text }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>

@php
    $id = app()->getLocale() === 'id';
    $digits = preg_replace('/[^0-9]/', '', $settings->contact_phone ?? '');
    if ($digits && str_starts_with($digits, '0')) $digits = '62' . substr($digits, 1);
@endphp

<section class="container-page pb-20 pt-4 md:pb-28">
    <div class="pop dots-paper relative overflow-hidden rounded-xl bg-sun-400 px-6 py-16 shadow-hard-lg md:px-16 md:py-24">
        <span class="pointer-events-none absolute -bottom-24 -right-16 h-72 w-72 rounded-full border-2 border-navy bg-brand-300" aria-hidden="true"></span>
        <span class="sticker !bg-white absolute right-6 top-6 hidden -rotate-6 sm:inline-flex !border-navy !text-navy">{{ $id ? 'Konsultasi gratis' : 'Free consultation' }}</span>

        <div class="relative max-w-3xl text-navy">
            <h2 class="font-display text-4xl font-extrabold leading-[1.02] !text-navy sm:text-5xl md:text-6xl">{{ $id ? 'Punya ide website atau aplikasi? Kita bangun bareng.' : 'Got an idea for a website or app? Let us build it together.' }}</h2>
            <p class="mt-5 max-w-xl text-base text-navy/80 md:text-lg">{{ $id ? 'Pilih paket yang sesuai atau ceritakan kebutuhan Anda kepada tim kami.' : 'Pick a package or tell our team what you need.' }}</p>
            <div class="mt-9 flex flex-col gap-4 sm:flex-row">
                <a href="{{ route('package.index') }}" class="btn-navy btn-lg">{{ $id ? 'Lihat paket' : 'View packages' }}</a>
                @if ($digits)
                    <a href="https://wa.me/{{ $digits }}" target="_blank" rel="noopener" class="btn btn-lg border-2 border-navy bg-white text-navy shadow-hard-sm hover:-translate-y-0.5 hover:shadow-hard"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> {{ $id ? 'Chat WhatsApp' : 'Chat on WhatsApp' }}</a>
                @endif
            </div>
        </div>
    </div>
</section>

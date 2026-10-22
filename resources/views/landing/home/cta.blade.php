@php
    $id = app()->getLocale() === 'id';
    $digits = preg_replace('/[^0-9]/', '', $settings->contact_phone ?? '');
    if ($digits && str_starts_with($digits, '0')) $digits = '62' . substr($digits, 1);
@endphp

<section class="container-page pb-4">
    <div class="relative overflow-hidden rounded-xl bg-brand-gradient px-6 py-14 text-center text-white shadow-pop md:px-16 md:py-20">
        <div class="pointer-events-none absolute -left-10 -top-10 h-48 w-48 rounded-full bg-white/10" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -bottom-16 -right-10 h-64 w-64 rounded-full bg-sun-400/25" aria-hidden="true"></div>
        <div class="relative mx-auto max-w-2xl">
            <h2 class="text-3xl font-extrabold text-white md:text-4xl">{{ $id ? 'Siap memulai proyek Anda?' : 'Ready to start your project?' }}</h2>
            <p class="mt-4 text-base text-white/90 md:text-lg">{{ $id ? 'Pilih paket yang sesuai atau konsultasikan kebutuhan Anda bersama tim kami — gratis.' : 'Pick a package or talk to our team about your needs — it is free.' }}</p>
            <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                <a href="{{ route('package.index') }}" class="btn-lg btn bg-white text-brand-700 hover:bg-brand-50">{{ $id ? 'Lihat Paket' : 'View Packages' }}</a>
                @if ($digits)
                    <a href="https://wa.me/{{ $digits }}" target="_blank" rel="noopener" class="btn-sun btn-lg"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> {{ $id ? 'Chat WhatsApp' : 'Chat on WhatsApp' }}</a>
                @endif
            </div>
        </div>
    </div>
</section>

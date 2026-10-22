@php
    $digits = preg_replace('/[^0-9]/', '', $site->contact_phone ?? '');
    // 08xxxx -> 628xxxx
    if ($digits && str_starts_with($digits, '0')) $digits = '62' . substr($digits, 1);
@endphp

@if ($digits)
    <a href="https://wa.me/{{ $digits }}" target="_blank" rel="noopener"
        class="group fixed bottom-5 right-4 z-40 flex h-14 items-center gap-2 rounded-full bg-[#25D366] px-4 text-[#0B3D1F] shadow-pop transition hover:scale-105 sm:right-6"
        style="margin-bottom: env(safe-area-inset-bottom)"
        aria-label="{{ app()->getLocale() === 'id' ? 'Chat WhatsApp' : 'Chat on WhatsApp' }}">
        <i class="fa-brands fa-whatsapp text-2xl" aria-hidden="true"></i>
        <span class="hidden text-small font-bold sm:inline">{{ app()->getLocale() === 'id' ? 'Konsultasi Gratis' : 'Free Consultation' }}</span>
    </a>
@endif

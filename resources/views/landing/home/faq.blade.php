@php
    $id = app()->getLocale() === 'id';
    $faqs = $id ? [
        ['Apakah bisa membayar dengan DP?', 'Bisa. Saat checkout Anda dapat memilih pembayaran penuh atau DP, lalu melunasi sisanya saat kami menagihnya melalui halaman Pesanan Saya.'],
        ['Apakah saya mendapat alamat website?', 'Ya. Saat checkout Anda dapat menentukan subdomain gratis berakhiran .fkstudio.id. Untuk kebutuhan domain sendiri, silakan hubungi kami lewat WhatsApp.'],
        ['Bagaimana cara memantau progres pengerjaan?', 'Setelah memesan, status pengerjaan dan pembayaran dapat dilihat kapan saja di menu Pesanan Saya pada akun Anda.'],
        ['Berapa lama waktu pengerjaannya?', 'Tergantung paket dan kompleksitas fitur yang Anda butuhkan. Hubungi kami lewat WhatsApp untuk estimasi waktu yang sesuai.'],
        ['Apakah ada invoice?', 'Ada. Invoice diterbitkan untuk pesanan Anda dan dapat dibuka dari halaman Pesanan Saya.'],
        ['Bagaimana jika saya butuh bantuan setelah website tayang?', 'Tim kami siap membantu. Anda bisa menghubungi kami lewat WhatsApp atau email yang tertera di bagian kontak.'],
    ] : [
        ['Can I pay with a deposit (DP)?', 'Yes. At checkout you can choose full payment or a deposit, then settle the remaining balance from My Orders when we bill it.'],
        ['Do I get a website address?', 'Yes. At checkout you can pick a free subdomain ending in .fkstudio.id. For your own custom domain, please contact us on WhatsApp.'],
        ['How do I follow the progress?', 'After ordering, the work and payment status are always visible under My Orders in your account.'],
        ['How long does a project take?', 'It depends on the package and the features you need. Contact us on WhatsApp for a suitable time estimate.'],
        ['Do I get an invoice?', 'Yes. An invoice is issued for your order and can be opened from the My Orders page.'],
        ['What if I need help after launch?', 'Our team is ready to help. Reach us via WhatsApp or the email listed in the contact section.'],
    ];
@endphp

@php
    $digits = preg_replace('/[^0-9]/', '', $settings->contact_phone ?? '');
    if ($digits && str_starts_with($digits, '0')) $digits = '62' . substr($digits, 1);
@endphp

<x-seo.schema :data="['@type' => 'FAQPage', 'mainEntity' => array_map(fn ($f) => ['@type' => 'Question', 'name' => $f[0], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f[1]]], $faqs)]" />

<section id="faq" class="scroll-mt-20 py-16 md:py-24">
    <div class="container-page">
        <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
            <h2 class="section-title max-w-xl">{{ $id ? 'Pertanyaan yang sering muncul sebelum order' : 'Questions people ask before ordering' }}</h2>
            @if ($digits)
                <a href="https://wa.me/{{ $digits }}" target="_blank" rel="noopener" class="btn-outline shrink-0 self-start md:self-auto"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> {{ $id ? 'Tanya langsung di WhatsApp' : 'Ask us on WhatsApp' }}</a>
            @endif
        </div>

        <dl class="mt-12 grid gap-x-16 gap-y-10 md:mt-14 lg:grid-cols-2">
            @foreach ($faqs as [$q, $a])
                <div class="border-t-2 border-edge pt-5">
                    <dt class="font-display text-xl font-bold leading-snug">{{ $q }}</dt>
                    <dd class="mt-2 max-w-lg text-muted">{{ $a }}</dd>
                </div>
            @endforeach
        </dl>
    </div>
</section>

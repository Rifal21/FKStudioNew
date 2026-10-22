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

<section id="faq" class="section scroll-mt-20 border-t-2 border-edge bg-surface">
    <div class="container-page">
        <div class="grid gap-12 lg:grid-cols-12 lg:gap-16">
            <div class="lg:col-span-4">
                <div class="lg:sticky lg:top-28">
                    <h2 class="section-title">{{ $id ? 'Pertanyaan yang sering diajukan' : 'Frequently asked questions' }}</h2>
                    <p class="section-lead">{{ $id ? 'Tidak menemukan jawabannya? Tanyakan langsung ke kami.' : 'Cannot find your answer? Ask us directly.' }}</p>
                    @if ($digits)
                        <a href="https://wa.me/{{ $digits }}" target="_blank" rel="noopener" class="btn-primary mt-8"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> {{ $id ? 'Chat WhatsApp' : 'Chat on WhatsApp' }}</a>
                    @endif
                </div>
            </div>

            <div class="min-w-0 lg:col-span-8" x-data="{ open: 0 }">
                @foreach ($faqs as $i => [$q, $a])
                    <div class="border-t-2 border-edge/25 last:border-b-2" :class="open === {{ $i }} && 'bg-sun-100/70 dark:bg-sun-400/10'">
                        <h3>
                            <button type="button" class="flex w-full items-center justify-between gap-5 px-2 py-5 text-left font-display text-lg font-bold md:px-4 md:text-xl"
                                @click="open = open === {{ $i }} ? null : {{ $i }}"
                                :aria-expanded="open === {{ $i }}" aria-controls="faq-{{ $i }}">
                                <span>{{ $q }}</span>
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-md border-2 border-edge transition" :class="open === {{ $i }} ? 'rotate-45 bg-sun-400 text-navy' : 'bg-surface'">
                                    <i class="fa-solid fa-plus text-caption" aria-hidden="true"></i>
                                </span>
                            </button>
                        </h3>
                        <div id="faq-{{ $i }}" x-show="open === {{ $i }}" x-transition x-cloak>
                            <p class="max-w-2xl px-2 pb-6 text-muted md:px-4">{{ $a }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

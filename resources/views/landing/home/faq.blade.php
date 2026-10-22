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

<section id="faq" class="section scroll-mt-20">
    <div class="container-page max-w-4xl">
        <x-ui.section-heading
            :eyebrow="$id ? 'Pertanyaan umum' : 'FAQ'"
            :title="$id ? 'Pertanyaan yang sering diajukan' : 'Frequently asked questions'" />

        <div class="mt-10 space-y-3" x-data="{ open: 0 }">
            @foreach ($faqs as $i => [$q, $a])
                <div class="card overflow-hidden">
                    <h3>
                        <button type="button" class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left text-base font-semibold md:px-6"
                            @click="open = open === {{ $i }} ? null : {{ $i }}"
                            :aria-expanded="open === {{ $i }}" aria-controls="faq-{{ $i }}">
                            <span>{{ $q }}</span>
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-soft text-brand-text transition" :class="open === {{ $i }} && 'rotate-45'">
                                <i class="fa-solid fa-plus text-caption" aria-hidden="true"></i>
                            </span>
                        </button>
                    </h3>
                    <div id="faq-{{ $i }}" x-show="open === {{ $i }}" x-transition x-cloak>
                        <p class="px-5 pb-5 text-muted md:px-6">{{ $a }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

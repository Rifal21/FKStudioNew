@php
    $id = app()->getLocale() === 'id';
    $digits = preg_replace('/[^0-9]/', '', $settings->contact_phone ?? '');
    if ($digits && str_starts_with($digits, '0')) $digits = '62' . substr($digits, 1);
    $flow = $id
        ? ['Pilih paket yang cocok dengan kebutuhan Anda.', 'Isi nama website dan jenis bisnis saat checkout.', 'Bayar DP 50% dulu, atau langsung lunas.', 'Pantau progres dan invoice dari akun Anda.']
        : ['Pick the package that fits your needs.', 'Enter your website name and business type at checkout.', 'Pay a 50% deposit first, or pay in full.', 'Follow progress and invoices from your account.'];
@endphp
<x-layouts.public
    :title="$id ? 'Paket Layanan' : 'Service Packages'"
    :description="$id ? 'Pilih paket pembuatan website dan layanan digital FKStudio dengan harga transparan dan pembayaran fleksibel.' : 'Choose a FKStudio website and digital service package with transparent pricing and flexible payment.'">
    <x-slot:navbar><x-site.navbar /></x-slot:navbar>

    <x-seo.schema :data="['@graph' => [
        \App\Support\Seo::breadcrumbs([[$id ? 'Beranda' : 'Home', route('home')], [$id ? 'Paket Layanan' : 'Service Packages', route('package.index')]]),
        ['@type' => 'ItemList', 'name' => $id ? 'Paket layanan FKStudio' : 'FKStudio service packages', 'itemListElement' => $packages->values()->map(fn ($p, $i) => ['@type' => 'ListItem', 'position' => $i + 1, 'item' => \App\Support\Seo::product($p)])->all()],
    ]]" />

    <x-site.page-head
        :title="$id ? 'Paket layanan' : 'Service packages'"
        :lead="$id ? 'Harga tertera di tiap paket. Bayar DP 50% dulu atau langsung lunas.' : 'Prices are listed on every package. Pay a 50% deposit first or pay in full.'" />

    <section class="dots-paper container-page max-w-none py-14 md:py-20">
        <div class="mx-auto grid max-w-page gap-12 lg:grid-cols-12 lg:gap-14">
            <aside class="lg:col-span-4">
                <div class="lg:sticky lg:top-28">
                    <h2 class="text-2xl">{{ $id ? 'Cara memesan' : 'How ordering works' }}</h2>
                    <ol class="mt-6 space-y-5">
                        @foreach ($flow as $i => $step)
                            <li class="flex gap-4">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border-2 border-edge bg-sun-400 font-display text-lg font-extrabold text-navy shadow-hard-sm">{{ $i + 1 }}</span>
                                <span class="pt-1">{{ $step }}</span>
                            </li>
                        @endforeach
                    </ol>
                    @if ($digits)
                        <p class="mt-9 text-small text-muted">{{ $id ? 'Butuh sesuatu di luar paket ini?' : 'Need something beyond these packages?' }}</p>
                        <a href="https://wa.me/{{ $digits }}" target="_blank" rel="noopener" class="btn-outline mt-3"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> {{ $id ? 'Tanya di WhatsApp' : 'Ask on WhatsApp' }}</a>
                    @endif
                </div>
            </aside>

            <div class="min-w-0 lg:col-span-8">
                @if ($packages->isEmpty())
                    <x-ui.card><x-ui.empty-state icon="fa-tags" :title="$id ? 'Belum ada paket tersedia' : 'No packages available yet'" :text="$id ? 'Silakan kembali lagi nanti.' : 'Please check back soon.'" /></x-ui.card>
                @else
                    <div class="space-y-10">
                        @foreach ($packages as $package)
                            <x-site.package-card :package="$package" :stack="true" />
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>

    <x-slot:footer><x-site.footer /><x-site.whatsapp /></x-slot:footer>
</x-layouts.public>

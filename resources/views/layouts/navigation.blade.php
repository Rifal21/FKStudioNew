<aside
    id="admin-sidebar"
    class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col border-r border-line bg-surface shadow-pop transition-transform duration-300 lg:sticky lg:top-0 lg:h-screen lg:w-64 lg:translate-x-0 lg:shadow-none"
    :class="{ 'translate-x-0': sidebarOpen }"
    aria-label="Menu admin">

    <div class="flex h-16 shrink-0 items-center justify-between border-b border-line px-5">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-gradient text-small font-extrabold text-white shadow-glow">FK</span>
            <span class="leading-tight">
                <span class="block text-base font-extrabold">FKStudio</span>
                <span class="block text-caption text-muted">Panel admin</span>
            </span>
        </a>
        <button type="button" class="btn-icon lg:hidden" @click="sidebarOpen = false" aria-label="Tutup menu"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
    </div>

    <nav class="custom-scrollbar flex-1 space-y-6 overflow-y-auto px-3 py-5">
        <div class="space-y-1">
            <x-admin.nav-link route="dashboard" icon="fa-gauge-high" label="Dashboard" />
        </div>

        @if (Auth::user()->isSuperAdmin())
            <div class="space-y-1">
                <p class="px-3 pb-1 text-caption font-bold uppercase tracking-wider text-muted">Transaksi</p>
                <x-admin.nav-link route="dashboard.orders.index" match="dashboard.orders.*" icon="fa-cart-shopping" label="Pesanan" :badge="$navCounts['orders'] ?: null" />
                <x-admin.nav-link route="dashboard.invoices.index" match="dashboard.invoices.*" icon="fa-file-invoice-dollar" label="Invoice" />
                <x-admin.nav-link route="dashboard.vouchers.index" match="dashboard.vouchers.*" icon="fa-ticket" label="Voucher" />
                <x-admin.nav-link route="dashboard.tickets.index" match="dashboard.tickets.*" icon="fa-headset" label="Tiket bantuan" :badge="$navCounts['tickets'] ?: null" />
            </div>

            <div class="space-y-1">
                <p class="px-3 pb-1 text-caption font-bold uppercase tracking-wider text-muted">Konten landing</p>
                <x-admin.nav-link route="dashboard.hero.edit" match="dashboard.hero.*" icon="fa-wand-magic-sparkles" label="Hero" />
                <x-admin.nav-link route="dashboard.about.edit" match="dashboard.about.*" icon="fa-address-card" label="Tentang" />
                <x-admin.nav-link route="dashboard.services.index" match="dashboard.services.*" icon="fa-briefcase" label="Layanan" />
                <x-admin.nav-link route="dashboard.projects.index" match="dashboard.projects.*" icon="fa-layer-group" label="Portofolio" />
                <x-admin.nav-link route="dashboard.packages.index" match="dashboard.packages.*" icon="fa-tags" label="Paket" />
                <x-admin.nav-link route="dashboard.blogs.index" match="dashboard.blogs.*" icon="fa-newspaper" label="Blog" />
                <x-admin.nav-link route="dashboard.testimonials.index" match="dashboard.testimonials.*" icon="fa-comment-dots" label="Testimoni" />
                <x-admin.nav-link route="dashboard.clients.index" match="dashboard.clients.*" icon="fa-handshake" label="Klien" />
                <x-admin.nav-link route="dashboard.owners.index" match="dashboard.owners.*" icon="fa-user-tie" label="Tim" />
            </div>

            <div class="space-y-1">
                <p class="px-3 pb-1 text-caption font-bold uppercase tracking-wider text-muted">Sistem</p>
                <x-admin.nav-link route="dashboard.settings.edit" match="dashboard.settings.*" icon="fa-gears" label="Pengaturan umum" />
                <x-admin.nav-link route="dashboard.payment_methods.edit" match="dashboard.payment_methods.*" icon="fa-credit-card" label="Metode pembayaran" />
                <x-admin.nav-link route="dashboard.users.index" match="dashboard.users.*" icon="fa-users-gear" label="Pengguna" />
            </div>
        @endif
    </nav>

    <div class="shrink-0 border-t border-line p-3">
        <div class="flex items-center gap-3 rounded-lg bg-surface-2 p-2.5">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-gradient font-bold uppercase text-white">{{ mb_substr(Auth::user()->name, 0, 1) }}</span>
            <div class="min-w-0 flex-1">
                <p class="truncate text-small font-bold">{{ Auth::user()->name }}</p>
                <p class="truncate text-caption text-muted">{{ Auth::user()->email }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-icon text-danger-700 hover:bg-danger-50 hover:text-danger-700 dark:text-red-300 dark:hover:bg-danger-500/15" aria-label="Keluar" title="Keluar"><i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i></button>
            </form>
        </div>
    </div>
</aside>

{{-- Mobile overlay --}}
<div x-show="sidebarOpen" x-cloak x-transition.opacity @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-ink/50 backdrop-blur-sm lg:hidden" aria-hidden="true"></div>

@extends('layouts.tenant-app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-10">
    <header class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-4xl font-black text-white tracking-tighter mb-1">Overview Bisnis</h2>
            <p class="text-slate-400">Statistik performa toko {{ tenant('branding_name') ?? 'FKStudio' }} Anda.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('tenant.home') }}" target="_blank" class="px-5 py-2.5 bg-white/5 border border-white/10 rounded-xl text-xs font-bold text-white hover:bg-white/10 transition-all">
                <i class="fa-solid fa-external-link mr-2 text-blue-400"></i> Kunjungi Web
            </a>
            <a href="{{ route('tenant.builder') }}" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-600/20">
                <i class="fa-solid fa-palette mr-2"></i> Site Builder
            </a>
        </div>
    </header>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @if($siteType === 'sales')
            <!-- Revenue Card -->
            <div class="glass p-6 rounded-[2rem] border-blue-500/20 bg-gradient-to-br from-blue-500/5 to-transparent">
                <div class="w-10 h-10 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center mb-4">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <h3 class="text-slate-500 text-[10px] font-bold uppercase tracking-widest mb-1">Total Pendapatan</h3>
                <p class="text-2xl font-black text-white italic">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                <div class="mt-4 flex items-center text-[10px] text-emerald-400 font-bold">
                    <i class="fa-solid fa-arrow-up mr-1"></i> +12% <span class="text-slate-500 ml-1 font-medium italic">bulan ini</span>
                </div>
            </div>

            <!-- Orders Card -->
            <div class="glass p-6 rounded-[2rem] border-indigo-500/20 bg-gradient-to-br from-indigo-500/5 to-transparent">
                <div class="w-10 h-10 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center mb-4">
                    <i class="fa-solid fa-shopping-bag"></i>
                </div>
                <h3 class="text-slate-500 text-[10px] font-bold uppercase tracking-widest mb-1">Total Pesanan</h3>
                <p class="text-2xl font-black text-white italic">{{ $totalOrders }}</p>
                <p class="mt-4 text-[10px] text-slate-500 font-bold uppercase tracking-widest">
                    <span class="text-amber-400">{{ $pendingOrders }} Pending</span>
                </p>
            </div>

            <!-- Products Card -->
            <div class="glass p-6 rounded-[2rem] border-pink-500/20 bg-gradient-to-br from-pink-500/5 to-transparent">
                <div class="w-10 h-10 rounded-xl bg-pink-500/20 text-pink-400 flex items-center justify-center mb-4">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <h3 class="text-slate-500 text-[10px] font-bold uppercase tracking-widest mb-1">Total Produk</h3>
                <p class="text-2xl font-black text-white italic">{{ $totalProducts }}</p>
                <a href="{{ route('tenant.products.index') }}" class="mt-4 block text-[10px] text-pink-400 font-bold uppercase tracking-widest hover:underline">Kelola Produk &rarr;</a>
            </div>

            <!-- Profit Card -->
            <div class="glass p-6 rounded-[2rem] border-emerald-500/20 bg-gradient-to-br from-emerald-500/5 to-transparent">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center mb-4">
                    <i class="fa-solid fa-money-bill-trend-up"></i>
                </div>
                <h3 class="text-slate-500 text-[10px] font-bold uppercase tracking-widest mb-1">Total Keuntungan (Laba)</h3>
                <p class="text-2xl font-black text-white italic">Rp {{ number_format($totalProfit, 0, ',', '.') }}</p>
                <div class="mt-4 flex items-center text-[10px] text-emerald-400 font-bold">
                    Margin: {{ number_format($profitMargin, 1) }}% <span class="text-slate-500 ml-1 font-medium italic">dari omzet</span>
                </div>
            </div>
        @else
            <!-- Branding/Personal Stats -->
            <div class="glass p-6 rounded-[2rem] border-blue-500/20 bg-gradient-to-br from-blue-500/5 to-transparent">
                <div class="w-10 h-10 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center mb-4">
                    <i class="fa-solid fa-fingerprint"></i>
                </div>
                <h3 class="text-slate-500 text-[10px] font-bold uppercase tracking-widest mb-1">Brand Identity</h3>
                <p class="text-2xl font-black text-white italic">Aktif</p>
                <div class="mt-4 text-[10px] text-slate-500 font-bold italic">Profil perusahaan online</div>
            </div>

            <div class="glass p-6 rounded-[2rem] border-indigo-500/20 bg-gradient-to-br from-indigo-500/5 to-transparent">
                <div class="w-10 h-10 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center mb-4">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <h3 class="text-slate-500 text-[10px] font-bold uppercase tracking-widest mb-1">Layanan / Fitur</h3>
                <p class="text-2xl font-black text-white italic">{{ count($siteType === 'branding' ? (\App\Models\TenantSetting::first()->sections_data['features'] ?? []) : []) }}</p>
                <a href="{{ route('tenant.builder') }}" class="mt-4 block text-[10px] text-indigo-400 font-bold uppercase tracking-widest hover:underline">Edit Services &rarr;</a>
            </div>

            <div class="glass p-6 rounded-[2rem] border-pink-500/20 bg-gradient-to-br from-pink-500/5 to-transparent">
                <div class="w-10 h-10 rounded-xl bg-pink-500/20 text-pink-400 flex items-center justify-center mb-4">
                    <i class="fa-solid fa-images"></i>
                </div>
                <h3 class="text-slate-500 text-[10px] font-bold uppercase tracking-widest mb-1">Katalog Portfolio</h3>
                <p class="text-2xl font-black text-white italic">{{ count($siteType === 'branding' ? (\App\Models\TenantSetting::first()->sections_data['products'] ?? []) : []) }}</p>
                <p class="mt-4 text-[10px] text-slate-500 font-bold italic">Item dipajang</p>
            </div>

            <div class="glass p-6 rounded-[2rem] border-emerald-500/20 bg-gradient-to-br from-emerald-500/5 to-transparent">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center mb-4">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                </div>
                <h3 class="text-slate-500 text-[10px] font-bold uppercase tracking-widest mb-1">Visual Design</h3>
                <p class="text-2xl font-black text-white italic">Premium</p>
                <div class="mt-4 text-[10px] text-emerald-400 font-bold italic">Design v2.0 Active</div>
            </div>
        @endif
    </div>

    @if($siteType === 'sales')
    <!-- Main Section: Recent Orders & Performance -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Recent Orders Table -->
        <div class="lg:col-span-2 space-y-6">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-black text-white uppercase tracking-widest">Pesanan Terbaru</h3>
                <a href="{{ route('tenant.orders') }}" class="text-xs font-bold text-blue-400 hover:underline italic">Semua Pesanan &rarr;</a>
            </div>
            <div class="glass rounded-3xl overflow-hidden border-white/5">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-white/5 border-b border-white/10">
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">ID Pesanan</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Pelanggan</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Total</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($recentOrders as $order)
                        <tr class="hover:bg-white/5 transition-colors cursor-pointer" onclick="window.location='{{ route('tenant.orders.show', $order) }}'">
                            <td class="px-6 py-4">
                                <p class="text-sm font-black text-white">#{{ $order->order_number }}</p>
                                <p class="text-[10px] text-slate-500 italic">{{ $order->created_at->diffForHumans() }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-slate-300">{{ $order->customer_name }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-black text-blue-400 italic">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-amber-500/20 text-amber-400',
                                        'paid' => 'bg-blue-500/20 text-blue-400',
                                        'completed' => 'bg-emerald-500/20 text-emerald-400',
                                        'cancelled' => 'bg-rose-500/20 text-rose-400',
                                    ];
                                @endphp
                                <span class="px-2 py-1 {{ $statusColors[$order->status] ?? 'bg-slate-500/20 text-slate-400' }} text-[9px] font-black rounded-lg uppercase tracking-widest">
                                    {{ $order->status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-500 font-bold italic">Belum ada pesanan masuk.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Monthly Report / Sidebar Info -->
        <div class="space-y-6">
            <h3 class="text-xl font-black text-white uppercase tracking-widest">Laporan Bulanan</h3>
            <div class="glass p-8 rounded-3xl space-y-6 bg-gradient-to-br from-indigo-500/10 to-transparent border-indigo-500/20">
                @foreach($monthlyRevenue as $rev)
                <div class="space-y-2">
                    <div class="flex justify-between items-end">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ $rev->month }}</span>
                        <span class="text-xs font-black text-white italic">Rp {{ number_format($rev->total, 0, ',', '.') }}</span>
                    </div>
                    <div class="h-1.5 w-full bg-white/5 rounded-full overflow-hidden">
                        <div class="h-full bg-indigo-500 rounded-full" style="width: {{ min(100, ($rev->total / max(1, $totalRevenue)) * 100) }}%"></div>
                    </div>
                </div>
                @endforeach

                @if($monthlyRevenue->isEmpty())
                <div class="text-center py-10 text-slate-600 italic text-sm">Data belum terkumpul.</div>
                @endif

                <div class="pt-6 border-t border-white/5">
                    <p class="text-[10px] text-slate-500 leading-relaxed italic">Tips: Update status pesanan ke <span class="text-emerald-400 font-bold">PAID</span> atau <span class="text-emerald-400 font-bold">COMPLETED</span> agar pendapatan tercatat di statistik ini.</p>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- Branding Site Guide -->
    <div class="glass p-12 rounded-[3rem] border-white/5 bg-gradient-to-br from-white/5 to-transparent flex flex-col items-center text-center space-y-8">
        <div class="w-20 h-20 bg-blue-500/20 text-blue-400 rounded-3xl flex items-center justify-center text-3xl shadow-2xl shadow-blue-500/20">
            <i class="fa-solid fa-rocket animate-bounce"></i>
        </div>
        <div class="max-w-2xl">
            <h3 class="text-3xl font-black text-white uppercase tracking-tighter mb-4 italic">Website Personal Anda Siap Mengudara!</h3>
            <p class="text-slate-400 text-lg leading-relaxed font-medium">Anda telah memilih tipe **Branding Profile**. Sekarang Anda bisa fokus membangun identitas digital yang profesional dengan mengatur Hero Section, Tentang Kami, Layanan, dan Portofolio melalui Site Builder.</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-4">
            <a href="{{ route('tenant.builder') }}" class="px-10 py-5 bg-white text-black font-black uppercase tracking-widest text-xs rounded-2xl hover:bg-slate-100 transition-all shadow-xl shadow-white/10">
                Mulai Kustomisasi <i class="fa-solid fa-wand-magic-sparkles ml-2"></i>
            </a>
            <a href="{{ route('tenant.home') }}" target="_blank" class="px-10 py-5 glass text-white font-black uppercase tracking-widest text-xs rounded-2xl hover:bg-white/5 transition-all">
                Lihat Preview Publik
            </a>
        </div>
    </div>
    @endif
</div>
@endsection

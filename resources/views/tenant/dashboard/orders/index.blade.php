@extends('layouts.tenant-app')

@section('title', 'Manajemen Pesanan')

@section('content')
<div class="space-y-8">
    <div>
        <h2 class="text-3xl font-black text-white tracking-tighter mb-2">Daftar Pesanan</h2>
        <p class="text-slate-400">Pantau dan kelola pesanan dari pelanggan Anda.</p>
    </div>

    <div class="glass rounded-3xl overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-white/5 bg-white/5">
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">No. Pesanan</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Pelanggan</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Total</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Status</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($orders as $order)
                <tr class="hover:bg-white/5 transition-colors">
                    <td class="px-6 py-4">
                        <p class="text-sm font-black text-white">#{{ $order->order_number }}</p>
                        <p class="text-[10px] text-slate-500">{{ $order->created_at->format('d M Y, H:i') }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-bold text-white">{{ $order->customer_name }}</p>
                        <p class="text-[10px] text-slate-500">{{ $order->customer_phone }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-bold text-blue-400">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                        <p class="text-[10px] text-slate-500 uppercase tracking-widest">{{ str_replace('_', ' ', $order->payment_method) }}</p>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusColors = [
                                'pending' => 'bg-amber-500/20 text-amber-400',
                                'paid' => 'bg-blue-500/20 text-blue-400',
                                'completed' => 'bg-emerald-500/20 text-emerald-400',
                                'cancelled' => 'bg-rose-500/20 text-rose-400',
                            ];
                            $color = $statusColors[$order->status] ?? 'bg-slate-500/20 text-slate-400';
                        @endphp
                        <span class="px-2 py-1 {{ $color }} text-[10px] font-bold rounded-lg uppercase tracking-tighter">{{ $order->status }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('tenant.orders.show', $order) }}" class="inline-flex items-center px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-xs font-bold text-white hover:bg-white/10 transition-all">
                            Detail <i class="fa-solid fa-chevron-right ml-2"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-500 font-bold italic">
                        Belum ada pesanan masuk.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

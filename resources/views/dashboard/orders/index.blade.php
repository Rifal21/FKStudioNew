<x-app-layout>
    <x-slot name="header">
        {{ __('Package Orders') }}
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="glass rounded-[2rem] overflow-hidden shadow-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="p-6 text-xs font-black uppercase tracking-widest text-slate-400">Order ID</th>
                            <th class="p-6 text-xs font-black uppercase tracking-widest text-slate-400">Customer</th>
                            <th class="p-6 text-xs font-black uppercase tracking-widest text-slate-400">Website Details</th>
                            <th class="p-6 text-xs font-black uppercase tracking-widest text-slate-400">Package</th>
                            <th class="p-6 text-xs font-black uppercase tracking-widest text-slate-400">Status</th>
                            <th class="p-6 text-xs font-black uppercase tracking-widest text-slate-400 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($orders as $order)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="p-6">
                                    <span class="font-mono text-xs text-slate-500">#{{ substr($order->id, 0, 8) }}</span>
                                    <div class="text-[10px] text-slate-400 mt-1">{{ $order->created_at->format('d M Y') }}</div>
                                </td>
                                <td class="p-6">
                                    <div class="font-bold text-slate-900">{{ $order->user->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $order->user->email }}</div>
                                </td>
                                <td class="p-6">
                                    @if($order->branding_name)
                                        <div class="font-bold text-slate-900">{{ $order->branding_name }}</div>
                                        <div class="text-[10px] text-blue-500 font-mono mt-1">
                                            <i class="fa-solid fa-link mr-1"></i>{{ $order->subdomain }}.localhost
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400 italic">No details</span>
                                    @endif
                                </td>
                                <td class="p-6">
                                    <div class="font-bold text-slate-900">{{ $order->package_name }}</div>
                                    <div class="text-[10px] font-black text-emerald-600 mt-1">{{ $order->package_price }}</div>
                                    <div class="text-[10px] text-slate-400 uppercase tracking-widest mt-1"><i class="fa-solid fa-credit-card mr-1"></i> {{ $order->payment_method }}</div>
                                </td>
                                <td class="p-6">
                                    @php
                                        $statusClasses = [
                                            'pending' => 'bg-amber-100 text-amber-600 border-amber-200',
                                            'paid' => 'bg-emerald-100 text-emerald-600 border-emerald-200',
                                            'completed' => 'bg-blue-100 text-blue-600 border-blue-200',
                                            'cancelled' => 'bg-red-100 text-red-600 border-red-200',
                                        ];
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $statusClasses[$order->status] ?? 'bg-slate-100 text-slate-600' }}">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td class="p-6 text-right space-y-2">
                                    <div class="flex flex-col items-end space-y-2">
                                        <!-- Actions depending on status -->
                                        @if($order->status === 'pending')
                                            <div class="flex items-center space-x-2">
                                                <form action="{{ route('dashboard.orders.update', $order->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="paid">
                                                    <button type="submit" class="px-3 py-1.5 bg-emerald-500 text-white text-[10px] font-black uppercase tracking-widest rounded hover:bg-emerald-600 transition-colors" title="Konfirmasi & Buat Website">
                                                        <i class="fa-solid fa-check mr-1"></i> Konfirmasi
                                                    </button>
                                                </form>
                                                <form action="{{ route('dashboard.orders.update', $order->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="cancelled">
                                                    <button type="submit" class="px-3 py-1.5 bg-red-100 text-red-600 border border-red-200 text-[10px] font-black uppercase tracking-widest rounded hover:bg-red-200 transition-colors" onclick="return confirm('Batalkan pesanan ini?')">
                                                        <i class="fa-solid fa-times"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                        
                                        <div class="flex items-center space-x-2">
                                            @if($order->invoice)
                                                <a href="{{ route('dashboard.invoices.show', $order->invoice->id) }}" class="px-3 py-1.5 bg-slate-100 text-slate-600 text-[10px] font-black uppercase tracking-widest rounded hover:bg-slate-200 transition-colors" title="Lihat Invoice">
                                                    <i class="fa-solid fa-file-invoice mr-1"></i> Inv
                                                </a>
                                            @endif

                                            @if($order->tickets && $order->tickets->count() > 0)
                                                <a href="{{ route('dashboard.tickets.index') }}" class="px-3 py-1.5 bg-blue-100 text-blue-600 text-[10px] font-black uppercase tracking-widest rounded hover:bg-blue-200 transition-colors" title="Lihat Bukti Transfer">
                                                    <i class="fa-solid fa-receipt mr-1"></i> Bukti
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

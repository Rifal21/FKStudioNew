@extends('layouts.tenant-app')

@section('title', 'Detail Pesanan #' . $order->order_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('tenant.orders') }}" class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center text-slate-400 hover:text-white transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <h2 class="text-3xl font-black text-white tracking-tighter">Pesanan #{{ $order->order_number }}</h2>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('tenant.orders.invoice', $order) }}" class="px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-xs font-bold text-white hover:bg-white/10 transition-all">
                <i class="fa-solid fa-file-pdf mr-2 text-rose-500"></i> Invoice
            </a>
            <form action="{{ route('tenant.orders.status', $order) }}" method="POST" class="flex items-center space-x-2">
                @csrf
                @method('PATCH')
                <select name="status" class="bg-slate-900 border border-white/10 rounded-lg text-xs font-bold text-white px-3 py-2">
                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-blue-700 transition-all">Update</button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="md:col-span-2 space-y-6">
            <div class="glass p-8 rounded-3xl space-y-6">
                <h3 class="text-lg font-black text-white uppercase tracking-widest border-b border-white/5 pb-4">Item Pesanan</h3>
                <div class="space-y-4">
                    @foreach($order->items as $item)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-lg bg-slate-800 overflow-hidden border border-white/10">
                                @if($item->product && $item->product->image)
                                    <img src="{{ $item->product->getUrl($item->product->image) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-600"><i class="fa-solid fa-image"></i></div>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-bold text-white">{{ $item->product_name }}</p>
                                <p class="text-xs text-slate-500">Rp {{ number_format($item->price, 0, ',', '.') }} x {{ $item->quantity }}</p>
                            </div>
                        </div>
                        <p class="text-sm font-black text-white">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                    </div>
                    @endforeach
                </div>
                <div class="border-t border-white/5 pt-6 flex justify-between items-center">
                    <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">Total Bayar</p>
                    <p class="text-2xl font-black text-blue-400 italic">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="glass p-8 rounded-3xl space-y-6">
                <h3 class="text-sm font-black text-white uppercase tracking-widest border-b border-white/5 pb-4">Info Pelanggan</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Nama</p>
                        <p class="text-sm font-bold text-white">{{ $order->customer_name }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">WhatsApp</p>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer_phone) }}" target="_blank" class="text-sm font-bold text-emerald-400 hover:underline">
                            {{ $order->customer_phone }} <i class="fa-brands fa-whatsapp ml-1"></i>
                        </a>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Email</p>
                        <p class="text-sm font-bold text-white">{{ $order->customer_email }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Alamat</p>
                        <p class="text-sm text-slate-400 leading-relaxed">{{ $order->customer_address }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Metode Pembayaran</p>
                        <p class="text-sm font-bold text-blue-400 uppercase tracking-tighter">{{ str_replace('_', ' ', $order->payment_method) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

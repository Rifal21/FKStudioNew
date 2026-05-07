@extends('layouts.tenant-app')

@section('title', 'Manajemen Produk')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-black text-white tracking-tighter mb-2">Daftar Produk</h2>
            <p class="text-slate-400">Kelola produk yang akan ditampilkan di halaman e-commerce Anda.</p>
        </div>
        <a href="{{ route('tenant.products.create') }}" class="px-6 py-3 bg-blue-600 text-white rounded-xl text-sm font-black uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-600/30">
            Tambah Produk <i class="fa-solid fa-plus ml-2"></i>
        </a>
    </div>

    <div class="glass rounded-3xl overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-white/5 bg-white/5">
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Gambar</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Nama Produk</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Harga</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Stok</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Status</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($products as $product)
                <tr class="hover:bg-white/5 transition-colors">
                    <td class="px-6 py-4">
                        <div class="w-12 h-12 rounded-lg bg-slate-800 overflow-hidden border border-white/10">
                            @if($product->image)
                                <img src="{{ $product->getUrl($product->image) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-600">
                                    <i class="fa-solid fa-image"></i>
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-bold text-white">{{ $product->name }}</p>
                        <p class="text-[10px] text-slate-500 truncate max-w-xs">{{ $product->description }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-bold text-blue-400">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-slate-300">{{ $product->stock }}</p>
                    </td>
                    <td class="px-6 py-4">
                        @if($product->is_active)
                            <span class="px-2 py-1 bg-emerald-500/20 text-emerald-400 text-[10px] font-bold rounded-lg uppercase tracking-tighter">Aktif</span>
                        @else
                            <span class="px-2 py-1 bg-slate-500/20 text-slate-400 text-[10px] font-bold rounded-lg uppercase tracking-tighter">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('tenant.products.edit', $product) }}" class="w-8 h-8 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center hover:bg-indigo-500 hover:text-white transition-all">
                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                            </a>
                            <form action="{{ route('tenant.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Hapus produk ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-lg bg-rose-500/20 text-rose-400 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-500 font-bold italic">
                        Belum ada produk. Silakan tambah produk pertama Anda.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

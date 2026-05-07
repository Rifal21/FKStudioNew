@extends('layouts.tenant-app')

@section('title', 'Edit Produk')

@section('content')
<div class="max-w-3xl mx-auto space-y-8">
    <div class="flex items-center space-x-4">
        <a href="{{ route('tenant.products.index') }}" class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center text-slate-400 hover:text-white transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h2 class="text-3xl font-black text-white tracking-tighter">Edit Produk</h2>
    </div>

    <form action="{{ route('tenant.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="glass p-8 rounded-3xl space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-widest">Nama Produk</label>
                    <input type="text" name="name" value="{{ $product->name }}" required class="w-full bg-slate-900/50 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                </div>
                
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-widest">Harga Jual (Rp)</label>
                    <input type="number" name="price" value="{{ $product->price }}" required class="w-full bg-slate-900/50 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                </div>
                
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-widest">HPP / Modal (Rp)</label>
                    <input type="number" name="cost_price" value="{{ $product->cost_price }}" required class="w-full bg-slate-900/50 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest">Deskripsi</label>
                <textarea name="description" rows="4" class="w-full bg-slate-900/50 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">{{ $product->description }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-widest">Stok</label>
                    <input type="number" name="stock" value="{{ $product->stock }}" required class="w-full bg-slate-900/50 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-widest">Ganti Gambar Produk</label>
                    <input type="file" name="image" class="w-full bg-slate-900/50 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all text-sm">
                </div>
            </div>

            <div class="flex items-center space-x-3 pt-4">
                <input type="checkbox" name="is_active" id="is_active" {{ $product->is_active ? 'checked' : '' }} class="w-4 h-4 rounded bg-slate-900 border-white/10 text-blue-600 focus:ring-blue-500">
                <label for="is_active" class="text-sm font-bold text-slate-300">Tampilkan produk di website</label>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-10 py-4 bg-blue-600 text-white rounded-2xl text-sm font-black uppercase tracking-widest hover:bg-blue-700 transition-all shadow-xl shadow-blue-600/30">
                Simpan Perubahan <i class="fa-solid fa-check ml-2"></i>
            </button>
        </div>
    </form>
</div>
@endsection

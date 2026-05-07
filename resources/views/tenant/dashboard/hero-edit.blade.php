@extends('layouts.tenant-app')

@section('title', 'Kustomisasi Hero')

@section('content')
<div class="space-y-8">
    <header class="flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-black text-white tracking-tighter mb-2">Kustomisasi Hero Section</h2>
            <p class="text-slate-400">Sesuaikan tampilan utama (bagian paling atas) website Anda.</p>
        </div>
        <a href="{{ route('tenant.dashboard') }}" class="text-slate-400 hover:text-white transition-colors text-sm font-bold">
            <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
        </a>
    </header>

    <div class="glass p-8 rounded-3xl">
        <form method="POST" action="{{ route('tenant.customize.hero.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Text Content -->
                <div class="space-y-6">
                    <div>
                        <label for="headline" class="block text-xs font-black text-slate-300 uppercase tracking-widest mb-2">Headline Utama</label>
                        <input type="text" id="headline" name="headline" value="{{ old('headline', $hero->headline) }}" 
                            class="w-full px-4 py-3 bg-slate-900/50 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all"
                            placeholder="Contoh: Solusi Terbaik Untuk Bisnis Anda">
                        @error('headline') <p class="mt-2 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="subheadline" class="block text-xs font-black text-slate-300 uppercase tracking-widest mb-2">Sub-Headline (Deskripsi)</label>
                        <textarea id="subheadline" name="subheadline" rows="4"
                            class="w-full px-4 py-3 bg-slate-900/50 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all"
                            placeholder="Contoh: Kami membantu ribuan klien mencapai target mereka dengan pendekatan inovatif.">{{ old('subheadline', $hero->subheadline) }}</textarea>
                        @error('subheadline') <p class="mt-2 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="cta_text" class="block text-xs font-black text-slate-300 uppercase tracking-widest mb-2">Teks Tombol CTA</label>
                            <input type="text" id="cta_text" name="cta_text" value="{{ old('cta_text', $hero->cta_text) }}" 
                                class="w-full px-4 py-3 bg-slate-900/50 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all"
                                placeholder="Contoh: Hubungi Kami">
                            @error('cta_text') <p class="mt-2 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="cta_link" class="block text-xs font-black text-slate-300 uppercase tracking-widest mb-2">Link Tombol CTA</label>
                            <input type="text" id="cta_link" name="cta_link" value="{{ old('cta_link', $hero->cta_link) }}" 
                                class="w-full px-4 py-3 bg-slate-900/50 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all"
                                placeholder="Contoh: https://wa.me/...">
                            @error('cta_link') <p class="mt-2 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Image Upload -->
                <div>
                    <label class="block text-xs font-black text-slate-300 uppercase tracking-widest mb-2">Gambar Latar (Background)</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-white/10 border-dashed rounded-xl relative overflow-hidden group hover:border-blue-500/50 transition-colors">
                        @if($hero->background_image)
                            <div class="absolute inset-0 z-0">
                                @if(str_starts_with($hero->background_image, 'fkstudio_storage/'))
                                    <img src="{{ rtrim(env('NEXTCLOUD_WEBDAV_URL'), '/') . '/' . ltrim($hero->background_image, '/') }}" class="w-full h-full object-cover opacity-50">
                                @else
                                    <img src="{{ Storage::url($hero->background_image) }}" class="w-full h-full object-cover opacity-50">
                                @endif
                                <div class="absolute inset-0 bg-slate-900/80 group-hover:bg-slate-900/60 transition-colors"></div>
                            </div>
                        @endif
                        
                        <div class="space-y-1 text-center relative z-10">
                            <i class="fa-solid fa-image text-4xl text-slate-500 mb-3"></i>
                            <div class="flex text-sm text-slate-400 justify-center">
                                <label for="background_image" class="relative cursor-pointer bg-blue-600 rounded-md font-bold text-white px-3 py-1 hover:bg-blue-500 focus-within:outline-none transition-colors">
                                    <span>Pilih Gambar</span>
                                    <input id="background_image" name="background_image" type="file" class="sr-only" accept="image/*">
                                </label>
                            </div>
                            <p class="text-xs text-slate-500 mt-2">PNG, JPG, GIF up to 5MB</p>
                        </div>
                    </div>
                    @error('background_image') <p class="mt-2 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="pt-6 border-t border-white/10 flex justify-end">
                <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-xl text-sm font-black uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-600/30">
                    <i class="fa-solid fa-save mr-2"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

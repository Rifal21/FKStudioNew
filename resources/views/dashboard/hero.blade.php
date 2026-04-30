<x-app-layout>
    <x-slot name="header">
        {{ __('Hero Section') }}
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        <!-- Left Column: Settings Form -->
        <div class="lg:col-span-2 space-y-8" data-aos="fade-right">
            <div class="glass p-8 rounded-[3rem] shadow-xl relative overflow-hidden">
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-blue-500/5 rounded-full blur-3xl"></div>
                
                <h3 class="text-2xl font-black text-slate-900 mb-8 flex items-center">
                    <span class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fa-solid fa-i-cursor"></i>
                    </span>
                    Text Content
                </h3>

                <form action="{{ route('dashboard.hero.update') }}" method="POST" class="space-y-10">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                        <!-- Indonesian Section -->
                        <div class="space-y-6 relative">
                            <div class="absolute -left-4 top-0 bottom-0 w-1 bg-blue-500 rounded-full"></div>
                            <span class="inline-flex items-center px-4 py-1.5 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-widest leading-none">
                                <i class="fa-solid fa-flag mr-2"></i> Indonesian
                            </span>
                            
                            <div class="space-y-2">
                                <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Main Title</label>
                                <input type="text" name="title_id" value="{{ $hero->title_id }}"
                                    class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-medium">
                            </div>
                            
                            <div class="space-y-2">
                                <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Subtitle Description</label>
                                <textarea name="subtitle_id" rows="4" 
                                    class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-medium leading-relaxed">{{ $hero->subtitle_id }}</textarea>
                            </div>
                            
                            <div class="space-y-2">
                                <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Button Label</label>
                                <input type="text" name="cta_text_id" value="{{ $hero->cta_text_id }}"
                                    class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-medium">
                            </div>
                        </div>

                        <!-- English Section -->
                        <div class="space-y-6 relative">
                            <div class="absolute -left-4 top-0 bottom-0 w-1 bg-indigo-500 rounded-full"></div>
                            <span class="inline-flex items-center px-4 py-1.5 bg-indigo-50 text-indigo-600 rounded-full text-[10px] font-black uppercase tracking-widest leading-none">
                                <i class="fa-solid fa-earth-americas mr-2"></i> English
                            </span>
                            
                            <div class="space-y-2">
                                <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Main Title</label>
                                <input type="text" name="title_en" value="{{ $hero->title_en }}"
                                    class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-indigo-500 transition-all font-medium">
                            </div>
                            
                            <div class="space-y-2">
                                <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Subtitle Description</label>
                                <textarea name="subtitle_en" rows="4" 
                                    class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-indigo-500 transition-all font-medium leading-relaxed">{{ $hero->subtitle_en }}</textarea>
                            </div>
                            
                            <div class="space-y-2">
                                <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Button Label</label>
                                <input type="text" name="cta_text_en" value="{{ $hero->cta_text_en }}"
                                    class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-indigo-500 transition-all font-medium">
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-100">
                        <div class="space-y-2 max-w-md">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Call to Action Link</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-link"></i>
                                </div>
                                <input type="text" name="cta_link" value="{{ $hero->cta_link }}"
                                    class="block w-full bg-slate-50 border-none rounded-2xl p-4 pl-12 focus:ring-2 focus:ring-blue-500 transition-all font-bold text-blue-600"
                                    placeholder="#contact">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit"
                            class="px-10 py-5 bg-slate-900 text-white font-black rounded-[2rem] hover:bg-black hover:scale-105 shadow-2xl shadow-slate-900/20 transition-all uppercase tracking-[0.2em] text-xs">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Column: Slider Management -->
        <div class="space-y-8" data-aos="fade-left">
            <div class="glass p-8 rounded-[3rem] shadow-xl">
                <h3 class="text-xl font-black text-slate-900 mb-8 flex items-center">
                    <span class="w-10 h-10 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fa-solid fa-images"></i>
                    </span>
                    Background Slider
                </h3>

                <form action="{{ route('dashboard.hero.slides.store') }}" method="POST"
                    enctype="multipart/form-data"
                    x-data="{ 
                        preview: null,
                        handleFileChange(e) {
                            const file = e.target.files[0];
                            if (file) {
                                this.preview = URL.createObjectURL(file);
                            }
                        }
                    }"
                    class="mb-10 p-8 bg-slate-50 rounded-[2rem] border-2 border-dashed border-slate-200 hover:border-blue-400 transition-colors group text-center relative overflow-hidden">
                    @csrf
                    
                    <div x-show="preview" class="absolute inset-0 z-10 bg-white">
                        <img :src="preview" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/40 flex flex-col items-center justify-center space-y-3">
                            <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-black rounded-xl hover:bg-blue-700 shadow-xl shadow-blue-500/20 transition-all uppercase tracking-widest text-[10px]">
                                Confirm Upload
                            </button>
                            <button type="button" @click="preview = null; $refs.fileInput.value = ''" class="px-8 py-3 bg-white text-slate-900 font-black rounded-xl hover:bg-slate-100 transition-all uppercase tracking-widest text-[10px]">
                                Cancel
                            </button>
                        </div>
                    </div>

                    <div class="mb-4 w-16 h-16 bg-white rounded-2xl shadow-sm mx-auto flex items-center justify-center text-slate-400 group-hover:text-blue-500 transition-colors">
                        <i class="fa-solid fa-cloud-arrow-up text-2xl"></i>
                    </div>
                    <label class="block text-xs font-black uppercase tracking-widest text-slate-400 mb-6 group-hover:text-slate-600">Upload New Slide</label>
                    <input type="file" name="image" required id="slide-upload" x-ref="fileInput" class="hidden" @change="handleFileChange">
                    <label for="slide-upload" class="cursor-pointer inline-flex px-8 py-3 bg-white border border-slate-200 text-slate-700 font-bold rounded-xl hover:bg-slate-900 hover:text-white transition-all">
                        Choose File
                    </label>
                </form>

                <div class="space-y-6">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Active Slides ({{ count($slides) }})</p>
                    <div class="grid grid-cols-1 gap-4">
                        @forelse($slides as $slide)
                            <div class="group relative rounded-[2rem] overflow-hidden aspect-[16/9] bg-slate-100 border-4 border-white shadow-lg card-hover">
                                <img src="{{ $slide->media_url }}"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-center p-6">
                                    <form action="{{ route('dashboard.hero.slides.destroy', $slide) }}"
                                        method="POST" onsubmit="return confirm('Hapus slide ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="px-6 py-3 bg-red-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-red-700 transition-colors flex items-center">
                                            <i class="fa-solid fa-trash-can mr-2"></i> Delete Slide
                                        </button>
                                    </form>
                                </div>
                                <div class="absolute top-4 left-4">
                                    <span class="px-3 py-1 bg-black/40 backdrop-blur-md text-white rounded-lg text-[10px] font-bold">#{{ $loop->iteration }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12 glass rounded-[2rem] border-2 border-dashed">
                                <i class="fa-solid fa-film text-4xl text-slate-200 mb-4 block"></i>
                                <span class="text-slate-400 font-medium">No slider images yet</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

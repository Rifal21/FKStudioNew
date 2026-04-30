<x-app-layout>
    <x-slot name="header">
        {{ __('Our Services') }}
    </x-slot>

    <div class="space-y-10 pb-20">
        <!-- Add Section -->
        <div class="glass p-10 rounded-[3rem] shadow-xl relative overflow-hidden" data-aos="fade-down">
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-blue-500/5 rounded-full blur-3xl"></div>
            
            <h3 class="text-2xl font-black text-slate-900 mb-8 flex items-center">
                <span class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mr-4">
                    <i class="fa-solid fa-plus"></i>
                </span>
                Create New Service
            </h3>

            <form action="{{ route('dashboard.services.store') }}" method="POST" class="space-y-8">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Title (ID)</label>
                            <input type="text" name="title_id" required
                                class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-medium">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Description (ID)</label>
                            <textarea name="description_id" required rows="2" 
                                class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-medium"></textarea>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Title (EN)</label>
                            <input type="text" name="title_en" required
                                class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-indigo-500 transition-all font-medium">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Description (EN)</label>
                            <textarea name="description_en" required rows="2" 
                                class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-indigo-500 transition-all font-medium"></textarea>
                        </div>
                    </div>

                    <!-- Icon Selection -->
                    <div class="md:col-span-2 space-y-4" x-data="{
                        icon: 'fa-solid fa-star',
                        search: '',
                        icons: [
                            'fa-solid fa-code', 'fa-solid fa-laptop-code', 'fa-solid fa-mobile-screen',
                            'fa-solid fa-paint-roller', 'fa-solid fa-palette', 'fa-solid fa-pen-nib',
                            'fa-solid fa-bullhorn', 'fa-solid fa-chart-line', 'fa-solid fa-magnifying-glass',
                            'fa-solid fa-server', 'fa-solid fa-database', 'fa-solid fa-cloud',
                            'fa-solid fa-shield-halved', 'fa-solid fa-robot', 'fa-solid fa-microchip',
                            'fa-solid fa-desktop', 'fa-solid fa-earth-americas', 'fa-solid fa-rocket',
                            'fa-solid fa-cart-shopping', 'fa-solid fa-store', 'fa-solid fa-camera'
                        ],
                        get filteredIcons() {
                            if (this.search === '') return this.icons;
                            return this.icons.filter(i => i.toLowerCase().includes(this.search.toLowerCase()));
                        }
                    }">
                        <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Icon Visual Identity</label>
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 p-8 bg-slate-50 rounded-[2.5rem] border border-slate-100">
                            <!-- Preview & Manual -->
                            <div class="space-y-4">
                                <div class="w-20 h-20 bg-white rounded-3xl shadow-xl flex items-center justify-center text-blue-600 text-3xl mx-auto lg:mx-0 ring-4 ring-blue-50">
                                    <i :class="icon"></i>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-black uppercase text-slate-400">Custom Class</label>
                                    <input type="text" name="icon" x-model="icon" required
                                        class="block w-full bg-white border border-slate-100 rounded-xl p-3 focus:ring-2 focus:ring-blue-500 transition-all font-mono text-sm">
                                </div>
                            </div>

                            <!-- Quick Select -->
                            <div class="lg:col-span-2 space-y-4">
                                <div class="flex items-center space-x-2 bg-white px-4 rounded-xl border border-slate-100 focus-within:ring-2 focus-within:ring-blue-500 transition-all">
                                    <i class="fa-solid fa-search text-slate-400 text-sm"></i>
                                    <input type="text" x-model="search" placeholder="Filter icons..."
                                        class="flex-1 bg-transparent border-none focus:ring-0 py-2 text-sm font-medium">
                                </div>
                                <div class="grid grid-cols-4 sm:grid-cols-6 lg:grid-cols-7 gap-3 max-h-40 overflow-y-auto p-2 custom-scrollbar">
                                    <template x-for="i in filteredIcons" :key="i">
                                        <button type="button" @click="icon = i"
                                            class="aspect-square rounded-2xl border-2 flex items-center justify-center transition-all group relative"
                                            :class="icon === i ? 'border-blue-500 bg-blue-50 text-blue-600' : 'border-white bg-white text-slate-400 hover:border-blue-200 hover:text-blue-500 shadow-sm'">
                                            <i :class="i" class="text-lg group-hover:scale-125 transition-transform"></i>
                                            <div x-show="icon === i" class="absolute -top-1 -right-1 w-3 h-3 bg-blue-500 rounded-full border-2 border-white"></div>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit"
                        class="px-12 py-5 bg-slate-900 text-white font-black rounded-[2rem] hover:bg-black hover:scale-105 shadow-2xl shadow-slate-900/20 transition-all uppercase tracking-[0.2em] text-xs">
                        Publish Service
                    </button>
                </div>
            </form>
        </div>

        <!-- List Section -->
        <div class="space-y-6">
            <h3 class="text-xl font-black text-slate-900 flex items-center ml-4">
                <span class="w-8 h-8 bg-slate-100 text-slate-500 rounded-lg flex items-center justify-center mr-3 text-sm">
                    <i class="fa-solid fa-list-check"></i>
                </span>
                Active Services List
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($services as $service)
                    <div class="glass p-8 rounded-[2.5rem] card-hover group relative overflow-hidden" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-500/5 rounded-full blur-2xl group-hover:bg-blue-500/10 transition-colors"></div>
                        
                        <div class="flex items-start justify-between mb-6">
                            <div class="w-16 h-16 bg-blue-50 rounded-[1.5rem] flex items-center justify-center text-blue-600 text-2xl shadow-sm ring-4 ring-white">
                                <i class="{{ str_starts_with($service->icon, 'fa-') ? $service->icon : 'fa-solid fa-' . ($service->icon == 'swatch' ? 'palette' : ($service->icon == 'code-bracket' ? 'code' : 'rocket')) }}"></i>
                            </div>
                            
                            <form action="{{ route('dashboard.services.destroy', $service->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Hapus layanan ini?')" 
                                    class="w-10 h-10 bg-slate-50 text-slate-400 hover:bg-red-50 hover:text-red-600 rounded-xl transition-all flex items-center justify-center">
                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                </button>
                            </form>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <h4 class="text-lg font-black text-slate-900 leading-tight">{{ $service->title_id }}</h4>
                                <p class="text-xs font-bold text-slate-400 italic">{{ $service->title_en }}</p>
                            </div>
                            
                            <p class="text-sm text-slate-500 line-clamp-3 leading-relaxed">{{ $service->description_id }}</p>
                            
                            <div class="pt-4 border-t border-slate-50 flex items-center justify-between">
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-300 font-mono">{{ $service->icon }}</span>
                                <div class="flex items-center space-x-1">
                                    <div class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></div>
                                    <span class="text-[10px] font-black text-green-600 uppercase tracking-widest">Active</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if(count($services) === 0)
                <div class="text-center py-20 glass rounded-[3rem] border-2 border-dashed">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-200 text-4xl">
                        <i class="fa-solid fa-box-open"></i>
                    </div>
                    <p class="text-slate-400 font-bold">No services added yet</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

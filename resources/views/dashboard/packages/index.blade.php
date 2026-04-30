<x-app-layout>
    <x-slot name="header">
        {{ __('Service Packages') }}
    </x-slot>

    <div class="space-y-10 pb-20" x-data="{
        editMode: false,
        currentPackage: {},
        features_id_raw: '',
        features_en_raw: '',
        initEdit(packageData) {
            this.currentPackage = { ...packageData };
            this.editMode = true;
            this.features_id_raw = (this.currentPackage.features_id || []).join('\n');
            this.features_en_raw = (this.currentPackage.features_en || []).join('\n');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }">
        <!-- Add/Edit Package Section -->
        <div class="glass p-10 rounded-[3rem] shadow-xl relative overflow-hidden" data-aos="fade-down">
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-blue-500/5 rounded-full blur-3xl"></div>
            
            <div class="flex justify-between items-center mb-8">
                <h3 class="text-2xl font-black text-slate-900 flex items-center">
                    <span class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fa-solid" :class="editMode ? 'fa-pen-to-square' : 'fa-plus'"></i>
                    </span>
                    <span x-text="editMode ? 'Edit Package' : 'Create New Package'"></span>
                </h3>
                <button x-show="editMode" @click="editMode = false; currentPackage = {}; features_id_raw = ''; features_en_raw = ''" 
                    class="px-6 py-2 bg-slate-100 text-slate-500 font-bold rounded-xl hover:bg-slate-200 transition-all">
                    Cancel Edit
                </button>
            </div>

            <form :action="editMode ? '{{ route('dashboard.packages.index') }}/' + currentPackage.id : '{{ route('dashboard.packages.store') }}'" 
                  method="POST" class="space-y-8">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PATCH">
                </template>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- General Info -->
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Package Name (ID)</label>
                            <input type="text" name="name_id" required x-model="currentPackage.name_id"
                                class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-medium">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Price Label (e.g. Rp 5.000.000)</label>
                            <input type="text" name="price" required x-model="currentPackage.price"
                                class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-medium">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Description (ID)</label>
                            <textarea name="description_id" rows="2" x-model="currentPackage.description_id"
                                class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-medium"></textarea>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Features (ID) - One per line</label>
                            <textarea name="features_id_raw" rows="5" x-model="features_id_raw" placeholder="Feature 1&#10;Feature 2&#10;Feature 3"
                                class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-medium"></textarea>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">CTA Text (ID)</label>
                            <input type="text" name="cta_text_id" x-model="currentPackage.cta_text_id"
                                class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-medium">
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Package Name (EN)</label>
                            <input type="text" name="name_en" required x-model="currentPackage.name_en"
                                class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-indigo-500 transition-all font-medium">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Order Index</label>
                            <input type="number" name="order" x-model="currentPackage.order"
                                class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-indigo-500 transition-all font-medium">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Description (EN)</label>
                            <textarea name="description_en" rows="2" x-model="currentPackage.description_en"
                                class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-indigo-500 transition-all font-medium"></textarea>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Features (EN) - One per line</label>
                            <textarea name="features_en_raw" rows="5" x-model="features_en_raw" placeholder="Feature 1&#10;Feature 2&#10;Feature 3"
                                class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-indigo-500 transition-all font-medium"></textarea>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">CTA Text (EN)</label>
                            <input type="text" name="cta_text_en" x-model="currentPackage.cta_text_en"
                                class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-indigo-500 transition-all font-medium">
                        </div>
                    </div>

                    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-8 bg-slate-50 p-8 rounded-[2.5rem]">
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">CTA Link (Section ID or External)</label>
                            <input type="text" name="cta_link" x-model="currentPackage.cta_link"
                                class="block w-full bg-white border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-medium">
                        </div>
                        <div class="flex items-center space-x-4 h-full pt-6">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_featured" value="1" x-model="currentPackage.is_featured" class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                <span class="ml-3 text-sm font-black uppercase tracking-widest text-slate-500 italic">Featured / Popular</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit"
                        class="px-12 py-5 bg-slate-900 text-white font-black rounded-[2rem] hover:bg-black hover:scale-105 shadow-2xl shadow-slate-900/20 transition-all uppercase tracking-[0.2em] text-xs">
                        <span x-text="editMode ? 'Update Package' : 'Publish Package'"></span>
                    </button>
                </div>
            </form>
        </div>

        <!-- List Section -->
        <div class="space-y-6">
            <h3 class="text-xl font-black text-slate-900 flex items-center ml-4">
                <span class="w-8 h-8 bg-slate-100 text-slate-500 rounded-lg flex items-center justify-center mr-3 text-sm">
                    <i class="fa-solid fa-tags"></i>
                </span>
                Active Service Packages
            </h3>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                @foreach ($packages as $package)
                    <div class="glass p-10 rounded-[3rem] card-hover group relative overflow-hidden flex flex-col {{ $package->is_featured ? 'ring-2 ring-blue-500/20' : '' }}" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                        @if($package->is_featured)
                            <div class="absolute top-0 right-0 p-6">
                                <span class="bg-blue-600 text-white text-[8px] font-black uppercase tracking-widest px-3 py-1 rounded-full">Popular</span>
                            </div>
                        @endif

                        <div class="flex items-start justify-between mb-8">
                            <div>
                                <h4 class="text-2xl font-black text-slate-900 leading-tight mb-1">{{ $package->name_id }}</h4>
                                <p class="text-4xl font-black text-blue-600 tracking-tighter">{{ $package->price }}</p>
                            </div>
                            
                            <div class="flex space-x-2">
                                <button @click="initEdit({{ $package->toJson() }})" class="w-10 h-10 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-xl transition-all flex items-center justify-center">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </button>
                                <form action="{{ route('dashboard.packages.destroy', $package->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('Hapus paket ini?')" 
                                        class="w-10 h-10 bg-slate-50 text-slate-400 hover:bg-red-50 hover:text-red-600 rounded-xl transition-all flex items-center justify-center">
                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="space-y-6 flex-grow">
                            <p class="text-sm text-slate-500 leading-relaxed italic line-clamp-2">"{{ $package->description_id }}"</p>
                            
                            <ul class="space-y-3">
                                @php
                                    $features = $package->features_id ?? [];
                                @endphp
                                @foreach (array_slice($features, 0, 4) as $feature)
                                    <li class="flex items-center gap-3 text-xs text-slate-600 font-medium">
                                        <i class="fa-solid fa-check text-blue-500"></i>
                                        {{ $feature }}
                                    </li>
                                @endforeach
                                @if(count($features) > 4)
                                    <li class="text-[10px] text-slate-400 font-bold ml-8">+ {{ count($features) - 4 }} more features</li>
                                @endif
                            </ul>
                        </div>

                        <div class="mt-8 pt-8 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-300 font-mono">Order: {{ $package->order }}</span>
                            <div class="flex items-center space-x-1">
                                <div class="w-1.5 h-1.5 bg-green-500 rounded-full"></div>
                                <span class="text-[10px] font-black text-green-600 uppercase tracking-widest">Live</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if(count($packages) === 0)
                <div class="text-center py-20 glass rounded-[3rem] border-2 border-dashed">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-200 text-4xl">
                        <i class="fa-solid fa-tag"></i>
                    </div>
                    <p class="text-slate-400 font-bold">No packages available</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

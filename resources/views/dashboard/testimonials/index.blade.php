<x-app-layout>
    <x-slot name="header">
        {{ __('Client Testimonials') }}
    </x-slot>

    <div class="space-y-10 pb-20" x-data="{
        openAdd: false,
        openEdit: false,
        editData: {
            id: '',
            name: '',
            role_id: '',
            role_en: '',
            content_id: '',
            content_en: '',
            rating: 5,
            action: ''
        },
        startEdit(tm) {
            this.editData = {
                id: tm.id,
                name: tm.name,
                role_id: tm.role_id,
                role_en: tm.role_en,
                content_id: tm.content_id,
                content_en: tm.content_en,
                rating: tm.rating || 5,
                action: '{{ url('/dashboard/testimonials') }}/' + tm.id,
                avatar_url: tm.media_url
            };
            this.newEditPreview = null;
            this.openEdit = true;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        newPreview: null,
        newEditPreview: null,
        handleFileChange(e, mode = 'add') {
            const file = e.target.files[0];
            if (file) {
                if (mode === 'add') this.newPreview = URL.createObjectURL(file);
                else this.newEditPreview = URL.createObjectURL(file);
            }
        }
    }">
        <!-- Add Section -->
        <div class="glass p-10 rounded-[3rem] shadow-xl relative overflow-hidden" data-aos="fade-down">
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-blue-500/5 rounded-full blur-3xl"></div>
            
            <div class="flex justify-between items-center mb-8">
                <h3 class="text-2xl font-black text-slate-900 flex items-center">
                    <span class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fa-solid fa-plus"></i>
                    </span>
                    Register New Feedback
                </h3>
                <button @click="openAdd = !openAdd"
                    class="px-8 py-3 bg-white border border-slate-200 text-slate-700 font-black rounded-[1.5rem] hover:bg-slate-900 hover:text-white transition-all text-xs uppercase tracking-widest">
                    <span x-show="!openAdd">Start Creation</span>
                    <span x-show="openAdd">Collapse Form</span>
                </button>
            </div>

            <div x-show="openAdd" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <form action="{{ route('dashboard.testimonials.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Client Full Name</label>
                            <input type="text" name="name" required placeholder="John Doe"
                                class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-bold">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Client Avatar</label>
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-slate-50 rounded-xl overflow-hidden shadow-sm flex items-center justify-center border border-slate-100">
                                    <template x-if="newPreview">
                                        <img :src="newPreview" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!newPreview">
                                        <i class="fa-solid fa-user text-slate-300"></i>
                                    </template>
                                </div>
                                <input type="file" name="avatar" accept="image/*" @change="handleFileChange($event, 'add')"
                                    class="flex-1 text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Satisfaction Rating</label>
                            <select name="rating"
                                class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-bold appearance-none">
                                <option value="5">⭐⭐⭐⭐⭐ 5 - Excellent</option>
                                <option value="4">⭐⭐⭐⭐ 4 - Very Good</option>
                                <option value="3">⭐⭐⭐ 3 - Good</option>
                                <option value="2">⭐⭐ 2 - Fair</option>
                                <option value="1">⭐ 1 - Poor</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4">
                        <div class="p-8 bg-slate-50 rounded-[2.5rem] border border-slate-100 space-y-6 relative overflow-hidden group">
                            <div class="absolute -right-10 -bottom-10 opacity-5 group-hover:scale-125 transition-transform duration-700">
                                <i class="fa-solid fa-flag text-[10rem]"></i>
                            </div>
                            <span class="inline-flex items-center px-4 py-1.5 bg-blue-100 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-widest leading-none">
                                <i class="fa-solid fa-flag mr-2"></i> Indonesian (ID)
                            </span>
                            <div class="space-y-2 relative">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Position / Company</label>
                                <input type="text" name="role_id" required placeholder="CEO at TechCorp"
                                    class="block w-full bg-white border-none rounded-xl p-3 focus:ring-2 focus:ring-blue-500 transition-all font-bold text-sm">
                            </div>
                            <div class="space-y-2 relative">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Feedback Content</label>
                                <textarea name="content_id" required rows="3" placeholder="Apa kata klien..."
                                    class="block w-full bg-white border-none rounded-xl p-3 focus:ring-2 focus:ring-blue-500 transition-all font-medium text-sm leading-relaxed"></textarea>
                            </div>
                        </div>

                        <div class="p-8 bg-slate-50 rounded-[2.5rem] border border-slate-100 space-y-6 relative overflow-hidden group">
                            <div class="absolute -right-10 -bottom-10 opacity-5 group-hover:scale-125 transition-transform duration-700">
                                <i class="fa-solid fa-earth-americas text-[10rem]"></i>
                            </div>
                            <span class="inline-flex items-center px-4 py-1.5 bg-indigo-100 text-indigo-600 rounded-full text-[10px] font-black uppercase tracking-widest leading-none">
                                <i class="fa-solid fa-earth-americas mr-2"></i> English (EN)
                            </span>
                            <div class="space-y-2 relative">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Position / Company</label>
                                <input type="text" name="role_en" required placeholder="CEO at TechCorp"
                                    class="block w-full bg-white border-none rounded-xl p-3 focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-sm">
                            </div>
                            <div class="space-y-2 relative">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Feedback Content</label>
                                <textarea name="content_en" required rows="3" placeholder="What the client said..."
                                    class="block w-full bg-white border-none rounded-xl p-3 focus:ring-2 focus:ring-indigo-500 transition-all font-medium text-sm leading-relaxed"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit"
                            class="px-12 py-5 bg-slate-900 text-white font-black rounded-[2rem] hover:bg-black hover:scale-105 shadow-2xl shadow-slate-900/20 transition-all uppercase tracking-[0.2em] text-xs">
                            Store Testimonial
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- List Section -->
        <div class="space-y-6">
            <h3 class="text-xl font-black text-slate-900 flex items-center ml-4">
                <span class="w-8 h-8 bg-slate-100 text-slate-500 rounded-lg flex items-center justify-center mr-3 text-sm">
                    <i class="fa-solid fa-quote-left"></i>
                </span>
                Client Wall ({{ count($testimonials) }})
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach ($testimonials as $tm)
                    <div class="glass p-8 rounded-[3rem] card-hover group relative overflow-hidden" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                        <div class="absolute -right-4 -top-4 w-32 h-32 bg-yellow-500/5 rounded-full blur-2xl group-hover:bg-yellow-500/10 transition-colors"></div>
                        
                        <div class="flex items-start justify-between mb-8 relative">
                            <div class="flex items-center space-x-4">
                                <div class="relative">
                                    @if ($tm->avatar)
                                        <img src="{{ $tm->media_url }}" class="w-20 h-20 rounded-[2rem] object-cover ring-4 ring-white shadow-xl shadow-slate-200">
                                    @else
                                        <div class="w-20 h-20 bg-blue-600 rounded-[2rem] flex items-center justify-center font-black text-white text-3xl shadow-xl shadow-blue-200">
                                            {{ substr($tm->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div class="absolute -bottom-1 -right-1 w-8 h-8 bg-white rounded-xl shadow-lg flex items-center justify-center">
                                        <i class="fa-solid fa-quote-left text-blue-600 text-[10px]"></i>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-xl font-black text-slate-900 leading-tight">{{ $tm->name }}</h4>
                                    <p class="text-xs font-black text-blue-600 uppercase tracking-widest mt-1">{{ $tm->role_id }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-0.5 px-3 py-1.5 bg-yellow-50 rounded-full border border-yellow-100">
                                @for ($i = 0; $i < $tm->rating; $i++)
                                    <i class="fa-solid fa-star text-[10px] text-yellow-500"></i>
                                @endfor
                                @for ($i = $tm->rating; $i < 5; $i++)
                                    <i class="fa-solid fa-star text-[10px] text-slate-200"></i>
                                @endfor
                            </div>
                        </div>

                        <div class="space-y-4 mb-8 relative">
                            <div class="p-6 bg-slate-50 rounded-[2rem] border border-slate-100">
                                <p class="text-sm text-slate-600 italic leading-relaxed">"{{ $tm->content_id }}"</p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center space-x-3 pt-6 border-t border-slate-50">
                            <button @click="startEdit({{ $tm->toJson() }})"
                                class="flex-1 py-3 bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-black hover:scale-[1.02] transition-all">
                                Update Data
                            </button>
                            <form action="{{ route('dashboard.testimonials.destroy', $tm->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Hapus testimoni ini?')" 
                                    class="w-12 h-12 bg-red-50 text-red-500 hover:bg-red-500 hover:text-white rounded-2xl transition-all flex items-center justify-center">
                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if(count($testimonials) === 0)
                <div class="text-center py-20 glass rounded-[3rem] border-2 border-dashed">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-200 text-4xl">
                        <i class="fa-solid fa-comment-slash"></i>
                    </div>
                    <p class="text-slate-400 font-bold">No feedback yet</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Edit Modal Overlay -->
    <div x-show="openEdit" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-md" x-cloak>
        
        <div class="bg-white rounded-[3.5rem] shadow-[0_30px_60px_-15px_rgba(0,0,0,0.5)] w-full max-w-2xl relative z-[70] overflow-hidden"
            @click.outside="openEdit = false"
            x-show="openEdit"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 scale-90 translate-y-20"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0">
            
            <div class="p-10 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fa-solid fa-pen-nib"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-slate-900 leading-tight">Edit Feedback</h2>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest" x-text="editData.name"></p>
                    </div>
                </div>
                <button @click="openEdit = false" class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-slate-900 transition-colors">
                    <i class="fa-solid fa-xmark text-2xl"></i>
                </button>
            </div>

            <form :action="editData.action" method="POST" enctype="multipart/form-data" class="p-10 space-y-8 max-h-[70vh] overflow-y-auto custom-scrollbar">
                @csrf @method('PATCH')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Client Name</label>
                        <input type="text" name="name" x-model="editData.name" required
                            class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-bold">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Satisfaction Level</label>
                        <select name="rating" x-model="editData.rating"
                            class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-bold appearance-none">
                            <option value="5">⭐⭐⭐⭐⭐ 5 Stars</option>
                            <option value="4">⭐⭐⭐⭐ 4 Stars</option>
                            <option value="3">⭐⭐⭐ 3 Stars</option>
                            <option value="2">⭐⭐ 2 Stars</option>
                            <option value="1">⭐ 1 Star</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Change Avatar (Optional)</label>
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 bg-slate-50 rounded-2xl overflow-hidden shadow-sm flex items-center justify-center border border-slate-100">
                            <template x-if="newEditPreview">
                                <img :src="newEditPreview" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!newEditPreview && editData.avatar_url">
                                <img :src="editData.avatar_url" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!newEditPreview && !editData.avatar_url">
                                <i class="fa-solid fa-user text-slate-300"></i>
                            </template>
                        </div>
                        <input type="file" name="avatar" @change="handleFileChange($event, 'edit')" class="flex-1 text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-600">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4">
                    <div class="space-y-4">
                        <span class="text-[10px] font-black uppercase tracking-widest text-blue-600 bg-blue-50 px-3 py-1 rounded-full italic">ID Version</span>
                        <div class="space-y-4">
                            <input type="text" name="role_id" x-model="editData.role_id" required placeholder="Role" class="block w-full bg-slate-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-blue-500 transition-all font-bold text-sm">
                            <textarea name="content_id" x-model="editData.content_id" required rows="3" placeholder="Feedback" class="block w-full bg-slate-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-blue-500 transition-all font-medium text-sm leading-relaxed"></textarea>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <span class="text-[10px] font-black uppercase tracking-widest text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full italic">EN Version</span>
                        <div class="space-y-4">
                            <input type="text" name="role_en" x-model="editData.role_en" required placeholder="Role" class="block w-full bg-slate-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-sm">
                            <textarea name="content_en" x-model="editData.content_en" required rows="3" placeholder="Feedback" class="block w-full bg-slate-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-indigo-500 transition-all font-medium text-sm leading-relaxed"></textarea>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-8 border-t border-slate-50">
                    <button type="button" @click="openEdit = false"
                        class="px-8 py-4 rounded-2xl bg-slate-100 text-slate-600 font-black uppercase tracking-widest text-[10px] hover:bg-slate-200 transition-all">Discard</button>
                    <button type="submit"
                        class="px-10 py-4 bg-blue-600 text-white font-black rounded-2xl uppercase tracking-widest text-[10px] shadow-2xl shadow-blue-500/30 hover:bg-blue-700 hover:scale-105 transition-all">Update Feedback</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        {{ __('Team & Leadership') }}
    </x-slot>

    <div class="space-y-10 pb-20">
        <!-- Add Owner Form -->
        <div class="glass p-10 rounded-[3rem] shadow-xl relative overflow-hidden" data-aos="fade-down">
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-blue-500/5 rounded-full blur-3xl"></div>
            
            <h3 class="text-2xl font-black text-slate-900 mb-8 flex items-center">
                <span class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mr-4">
                    <i class="fa-solid fa-user-plus"></i>
                </span>
                Add New Team Member
            </h3>

            <form action="{{ route('dashboard.owners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8"
                x-data="{ 
                    preview: null,
                    handleFileChange(e) {
                        const file = e.target.files[0];
                        if (file) {
                            this.preview = URL.createObjectURL(file);
                        }
                    }
                }">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="lg:col-span-2 space-y-6">
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Full Name</label>
                            <input type="text" name="name" required
                                class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-bold">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Profile Photo</label>
                            <div class="flex items-center space-x-4">
                                <div class="w-16 h-16 bg-slate-50 rounded-2xl overflow-hidden shadow-sm flex items-center justify-center border border-slate-100">
                                    <template x-if="preview">
                                        <img :src="preview" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!preview">
                                        <i class="fa-solid fa-user text-slate-300"></i>
                                    </template>
                                </div>
                                <input type="file" name="image" required @change="handleFileChange"
                                    class="flex-1 text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                        </div>
                    </div>
                    <div class="lg:col-span-2 space-y-6">
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Role (ID)</label>
                            <input type="text" name="role_id" required placeholder="e.g. Direktur Utama"
                                class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-medium">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Role (EN)</label>
                            <input type="text" name="role_en" required placeholder="e.g. Chief Executive Officer"
                                class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-indigo-500 transition-all font-medium">
                        </div>
                    </div>
                    
                    <div class="lg:col-span-2 space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Bio (ID)</label>
                        <textarea name="bio_id" rows="2" 
                            class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-medium text-sm"></textarea>
                    </div>
                    <div class="lg:col-span-2 space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Bio (EN)</label>
                        <textarea name="bio_en" rows="2" 
                            class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-indigo-500 transition-all font-medium text-sm"></textarea>
                    </div>

                    <div class="lg:col-span-2 space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Instagram Profile</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-pink-500">
                                <i class="fa-brands fa-instagram"></i>
                            </div>
                            <input type="url" name="instagram_url" placeholder="https://instagram.com/user"
                                class="block w-full bg-slate-50 border-none rounded-2xl p-4 pl-10 focus:ring-2 focus:ring-pink-500 transition-all font-medium">
                        </div>
                    </div>
                    <div class="lg:col-span-2 space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">LinkedIn Profile</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-blue-600">
                                <i class="fa-brands fa-linkedin"></i>
                            </div>
                            <input type="url" name="linkedin_url" placeholder="https://linkedin.com/in/user"
                                class="block w-full bg-slate-50 border-none rounded-2xl p-4 pl-10 focus:ring-2 focus:ring-blue-600 transition-all font-medium">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit"
                        class="px-12 py-5 bg-slate-900 text-white font-black rounded-[2rem] hover:bg-black hover:scale-105 shadow-2xl shadow-slate-900/20 transition-all uppercase tracking-[0.2em] text-xs">
                        Add Team Member
                    </button>
                </div>
            </form>
        </div>

        <!-- List Section -->
        <div class="space-y-6">
            <h3 class="text-xl font-black text-slate-900 flex items-center ml-4">
                <span class="w-8 h-8 bg-slate-100 text-slate-500 rounded-lg flex items-center justify-center mr-3 text-sm">
                    <i class="fa-solid fa-user-tie"></i>
                </span>
                Active Team Members ({{ count($owners) }})
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($owners as $owner)
                    <div class="glass p-8 rounded-[3rem] card-hover group relative overflow-hidden flex flex-col items-center text-center" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                        <div class="absolute -right-4 -top-4 w-32 h-32 bg-blue-500/5 rounded-full blur-2xl group-hover:bg-blue-500/10 transition-colors"></div>
                        
                        <div class="relative mb-6">
                            <div class="w-32 h-32 rounded-[2.5rem] overflow-hidden ring-4 ring-white shadow-2xl shadow-slate-200">
                                <img src="{{ $owner->media_url }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            </div>
                            <div class="absolute -bottom-2 -right-2 flex space-x-1">
                                @if ($owner->instagram_url)
                                    <a href="{{ $owner->instagram_url }}" target="_blank" 
                                        class="w-10 h-10 bg-white text-pink-600 rounded-xl shadow-lg flex items-center justify-center hover:bg-pink-600 hover:text-white transition-all scale-90 hover:scale-100">
                                        <i class="fa-brands fa-instagram text-sm"></i>
                                    </a>
                                @endif
                                @if ($owner->linkedin_url)
                                    <a href="{{ $owner->linkedin_url }}" target="_blank" 
                                        class="w-10 h-10 bg-white text-blue-600 rounded-xl shadow-lg flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all scale-90 hover:scale-100">
                                        <i class="fa-brands fa-linkedin text-sm"></i>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="space-y-2 mb-6">
                            <h4 class="text-xl font-black text-slate-900 tracking-tight">{{ $owner->name }}</h4>
                            <p class="text-[10px] font-black text-blue-600 uppercase tracking-[0.2em]">{{ $owner->getTranslation('role') }}</p>
                        </div>

                        @if($owner->getTranslation('bio'))
                            <p class="text-sm text-slate-500 line-clamp-3 leading-relaxed mb-6">{{ $owner->getTranslation('bio') }}</p>
                        @endif

                        <div class="w-full pt-6 border-t border-slate-50">
                            <form action="{{ route('dashboard.owners.destroy', $owner->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Hapus anggota tim ini?')" 
                                    class="w-full py-3 bg-red-50 text-red-500 hover:bg-red-500 hover:text-white rounded-2xl transition-all font-black uppercase tracking-widest text-[10px] flex items-center justify-center space-x-2">
                                    <i class="fa-solid fa-trash-can text-[10px]"></i>
                                    <span>Remove Member</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if(count($owners) === 0)
                <div class="text-center py-20 glass rounded-[3rem] border-2 border-dashed">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-200 text-4xl">
                        <i class="fa-solid fa-users-slash"></i>
                    </div>
                    <p class="text-slate-400 font-bold">No team members added yet</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

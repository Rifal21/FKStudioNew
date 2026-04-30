<x-app-layout>
    <x-slot name="header">
        {{ __('About Section') }}
    </x-slot>

    <div class="space-y-12 pb-32">
        <form id="about-form" action="{{ route('dashboard.about.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
        </form>
            
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            <!-- Left: Main Content (Story & Vision) -->
            <div class="lg:col-span-2 space-y-8" data-aos="fade-right">
                <div class="glass p-10 rounded-[3.5rem] shadow-2xl shadow-slate-200/50 relative overflow-hidden">
                    <div class="absolute -right-20 -top-20 w-80 h-80 bg-blue-500/5 rounded-full blur-[100px]"></div>
                    
                    <div class="flex items-center justify-between mb-12">
                        <h3 class="text-2xl font-black text-slate-900 flex items-center">
                            <span class="w-12 h-12 bg-blue-600 text-white rounded-2xl flex items-center justify-center mr-4 shadow-lg shadow-blue-500/30">
                                <i class="fa-solid fa-feather-pointed text-lg"></i>
                            </span>
                            Brand Story & Philosophy
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-16">
                        <!-- Indonesian Section -->
                        <div class="space-y-8">
                            <div class="flex items-center space-x-3 mb-2">
                                <span class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center font-bold text-xs">ID</span>
                                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-600">Konten Indonesia</span>
                            </div>
                            
                            <div class="space-y-6">
                                <div class="group">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block ml-1 group-focus-within:text-blue-600 transition-colors">Judul Utama</label>
                                    <input type="text" name="title_id" value="{{ $about->title_id }}" form="about-form"
                                        class="block w-full bg-slate-50 border-2 border-transparent rounded-[1.5rem] p-4 focus:bg-white focus:border-blue-500 focus:ring-0 transition-all font-bold text-slate-900">
                                </div>
                                
                                <div class="group">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block ml-1 group-focus-within:text-blue-600 transition-colors">Deskripsi Singkat</label>
                                    <textarea name="description_id" rows="5" form="about-form"
                                        class="block w-full bg-slate-50 border-2 border-transparent rounded-[1.5rem] p-4 focus:bg-white focus:border-blue-500 focus:ring-0 transition-all font-medium text-slate-700 leading-relaxed">{{ $about->description_id }}</textarea>
                                </div>

                                <div class="grid grid-cols-1 gap-6 pt-4 border-t border-slate-100">
                                    <div class="group">
                                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block ml-1 group-focus-within:text-blue-600 transition-colors">Visi Perusahaan</label>
                                        <textarea name="vision_id" rows="3" form="about-form"
                                            class="block w-full bg-slate-50 border-2 border-transparent rounded-[1.5rem] p-4 focus:bg-white focus:border-blue-500 focus:ring-0 transition-all font-medium text-slate-700 leading-relaxed">{{ $about->vision_id }}</textarea>
                                    </div>
                                    <div class="group">
                                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block ml-1 group-focus-within:text-blue-600 transition-colors">Misi Perusahaan</label>
                                        <textarea name="mission_id" rows="4" form="about-form"
                                            class="block w-full bg-slate-50 border-2 border-transparent rounded-[1.5rem] p-4 focus:bg-white focus:border-blue-500 focus:ring-0 transition-all font-medium text-slate-700 leading-relaxed">{{ $about->mission_id }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- English Section -->
                        <div class="space-y-8">
                            <div class="flex items-center space-x-3 mb-2">
                                <span class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center font-bold text-xs">EN</span>
                                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-600">English Content</span>
                            </div>
                            
                            <div class="space-y-6">
                                <div class="group">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block ml-1 group-focus-within:text-indigo-600 transition-colors">Main Title</label>
                                    <input type="text" name="title_en" value="{{ $about->title_en }}" form="about-form"
                                        class="block w-full bg-slate-50 border-2 border-transparent rounded-[1.5rem] p-4 focus:bg-white focus:border-indigo-500 focus:ring-0 transition-all font-bold text-slate-900">
                                </div>
                                
                                <div class="group">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block ml-1 group-focus-within:text-indigo-600 transition-colors">Short Description</label>
                                    <textarea name="description_en" rows="5" form="about-form"
                                        class="block w-full bg-slate-50 border-2 border-transparent rounded-[1.5rem] p-4 focus:bg-white focus:border-indigo-500 focus:ring-0 transition-all font-medium text-slate-700 leading-relaxed">{{ $about->description_en }}</textarea>
                                </div>

                                <div class="grid grid-cols-1 gap-6 pt-4 border-t border-slate-100">
                                    <div class="group">
                                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block ml-1 group-focus-within:text-indigo-600 transition-colors">Company Vision</label>
                                        <textarea name="vision_en" rows="3" form="about-form"
                                            class="block w-full bg-slate-50 border-2 border-transparent rounded-[1.5rem] p-4 focus:bg-white focus:border-indigo-500 focus:ring-0 transition-all font-medium text-slate-700 leading-relaxed">{{ $about->vision_en }}</textarea>
                                    </div>
                                    <div class="group">
                                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block ml-1 group-focus-within:text-indigo-600 transition-colors">Company Mission</label>
                                        <textarea name="mission_en" rows="4" form="about-form"
                                            class="block w-full bg-slate-50 border-2 border-transparent rounded-[1.5rem] p-4 focus:bg-white focus:border-indigo-500 focus:ring-0 transition-all font-medium text-slate-700 leading-relaxed">{{ $about->mission_en }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right: Gallery Management -->
            <div class="space-y-8" data-aos="fade-left">
                <div class="glass p-10 rounded-[3.5rem] shadow-2xl shadow-slate-200/50">
                    <h3 class="text-xl font-black text-slate-900 mb-10 flex items-center">
                        <span class="w-10 h-10 bg-purple-600 text-white rounded-xl flex items-center justify-center mr-4 shadow-lg shadow-purple-500/30">
                            <i class="fa-solid fa-images"></i>
                        </span>
                        About Slider
                    </h3>

                    <form action="{{ route('dashboard.about.slides.store') }}" method="POST"
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
                        class="mb-10 p-8 bg-slate-50 rounded-[2.5rem] border-4 border-dashed border-slate-100 hover:border-purple-400 transition-all group text-center relative overflow-hidden">
                        @csrf
                        
                        <div x-show="preview" class="absolute inset-0 z-10 bg-white">
                            <img :src="preview" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/40 flex flex-col items-center justify-center space-y-3">
                                <button type="submit" class="px-8 py-3 bg-purple-600 text-white font-black rounded-xl hover:bg-purple-700 shadow-xl shadow-purple-500/20 transition-all uppercase tracking-widest text-[10px]">
                                    Confirm Upload
                                </button>
                                <button type="button" @click="preview = null; $refs.fileInput.value = ''" class="px-8 py-3 bg-white text-slate-900 font-black rounded-xl hover:bg-slate-100 transition-all uppercase tracking-widest text-[10px]">
                                    Cancel
                                </button>
                            </div>
                        </div>

                        <div class="mb-4 w-20 h-20 bg-white rounded-3xl shadow-sm mx-auto flex items-center justify-center text-slate-300 group-hover:text-purple-500 transition-all group-hover:scale-110">
                            <i class="fa-solid fa-cloud-arrow-up text-3xl"></i>
                        </div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-8">Click button below to select image</p>
                        <input type="file" name="image" required id="about-slide-upload" x-ref="fileInput" class="hidden" @change="handleFileChange">
                        <label for="about-slide-upload" class="cursor-pointer inline-flex px-10 py-4 bg-slate-900 text-white font-black rounded-2xl hover:bg-black hover:scale-105 transition-all shadow-xl shadow-slate-900/20 uppercase tracking-widest text-[10px]">
                            Choose Photo
                        </label>
                    </form>

                    <div class="grid grid-cols-2 gap-4">
                        @foreach ($slides as $slide)
                            <div class="group relative rounded-[1.5rem] overflow-hidden aspect-square bg-slate-100 border-4 border-white shadow-lg card-hover">
                                <img src="{{ $slide->media_url }}" class="w-full h-full object-cover group-hover:scale-125 transition-transform duration-1000">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all flex items-end justify-center pb-6">
                                    <form action="{{ route('dashboard.about.slides.destroy', $slide->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('Silahkan konfirmasi penghapusan slide?')"
                                            class="w-12 h-12 bg-red-600 text-white rounded-2xl flex items-center justify-center hover:scale-110 hover:bg-red-500 transition-all shadow-xl shadow-red-500/40">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Floating Action Button -->
        <div class="fixed bottom-10 left-1/2 -translate-x-1/2 lg:left-auto lg:right-16 lg:translate-x-0 z-[60]">
            <button type="submit" form="about-form"
                class="px-14 py-7 bg-slate-900 text-white font-black rounded-[3.5rem] hover:bg-black hover:scale-105 shadow-[0_20px_50px_rgba(0,0,0,0.3)] transition-all flex items-center space-x-6 group">
                <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center group-hover:rotate-12 transition-transform shadow-lg shadow-blue-500/40">
                    <i class="fa-solid fa-cloud-arrow-up text-lg"></i>
                </div>
                <div class="text-left">
                    <p class="text-[10px] uppercase tracking-[0.3em] text-slate-500 font-bold leading-none mb-1.5">Master Update</p>
                    <p class="text-base font-black leading-none">Publish Changes</p>
                </div>
            </button>
        </div>

        <!-- Team Section -->
        <div class="mt-32 space-y-16">
            <div class="bg-slate-900 rounded-[4rem] p-16 text-white relative overflow-hidden shadow-[0_30px_100px_rgba(0,0,0,0.2)]" data-aos="fade-up">
                <div class="absolute right-0 top-0 w-2/3 h-full bg-gradient-to-l from-blue-600/20 to-transparent blur-[120px] rounded-full"></div>
                <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-12">
                    <div class="space-y-4">
                        <div class="inline-flex items-center px-5 py-2 bg-blue-600/20 border border-blue-500/30 rounded-full text-blue-400 text-xs font-black uppercase tracking-[0.2em] mb-4">
                            Human Capital
                        </div>
                        <h2 class="text-5xl font-black tracking-tighter">Team & Leadership</h2>
                        <p class="text-slate-400 text-lg font-medium max-w-2xl leading-relaxed">
                            Empower your brand by showcasing the talented individuals driving your vision forward. Manage roles, bios, and social connections effortlessly.
                        </p>
                    </div>
                    <div class="shrink-0">
                        <div class="p-8 bg-white/5 rounded-[2.5rem] border border-white/10 backdrop-blur-xl flex items-center space-x-6">
                            <div class="w-20 h-20 bg-blue-600 rounded-3xl flex items-center justify-center shadow-2xl shadow-blue-600/40">
                                <span class="text-4xl font-black text-white">{{ count($owners) }}</span>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase tracking-[0.3em] text-slate-500 font-black mb-1">Active Members</p>
                                <p class="text-xl font-bold text-white">Full Team Capacity</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Team Member Form -->
            <div class="glass p-12 rounded-[4rem] shadow-2xl shadow-slate-200/50 relative overflow-hidden" data-aos="fade-up">
                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-blue-500/5 rounded-full blur-[100px]"></div>
                
                <h3 class="text-2xl font-black text-slate-900 mb-12 flex items-center">
                    <span class="w-12 h-12 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mr-5 shadow-sm">
                        <i class="fa-solid fa-user-plus"></i>
                    </span>
                    Recruit New Member
                </h3>

                <form action="{{ route('dashboard.owners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-12"
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
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
                        <div class="lg:col-span-2 space-y-8">
                            <div class="group">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3 block ml-1 group-focus-within:text-blue-600">Full Name</label>
                                <input type="text" name="name" required
                                    class="block w-full bg-slate-50 border-2 border-transparent rounded-[1.5rem] p-5 focus:bg-white focus:border-blue-500 focus:ring-0 transition-all font-bold text-slate-900">
                            </div>
                            <div class="space-y-3">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block ml-1">Profile Mastershot</label>
                                <div class="flex items-center space-x-6 bg-slate-50 p-4 rounded-[2rem] border-2 border-dashed border-slate-200">
                                    <div class="w-24 h-24 bg-white rounded-[1.5rem] overflow-hidden shadow-xl flex items-center justify-center shrink-0 border-2 border-white">
                                        <template x-if="preview">
                                            <img :src="preview" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!preview">
                                            <i class="fa-solid fa-user-tie text-slate-200 text-3xl"></i>
                                        </template>
                                    </div>
                                    <div class="flex-1 space-y-2">
                                        <input type="file" name="image" required @change="handleFileChange" id="member-photo" class="hidden">
                                        <label for="member-photo" class="inline-flex px-6 py-3 bg-white text-slate-900 font-bold rounded-xl shadow-sm border border-slate-200 hover:bg-slate-900 hover:text-white transition-all cursor-pointer text-xs">
                                            Upload Photo
                                        </label>
                                        <p class="text-[10px] text-slate-400 font-medium italic">Recommended: Square Aspect Ratio</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="lg:col-span-2 space-y-8">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div class="group">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3 block ml-1 group-focus-within:text-blue-600">Position (ID)</label>
                                    <input type="text" name="role_id" required placeholder="e.g. Founder & CEO"
                                        class="block w-full bg-slate-50 border-2 border-transparent rounded-[1.5rem] p-5 focus:bg-white focus:border-blue-500 focus:ring-0 transition-all font-bold text-slate-900">
                                </div>
                                <div class="group">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3 block ml-1 group-focus-within:text-indigo-600">Position (EN)</label>
                                    <input type="text" name="role_en" required placeholder="e.g. Chief Director"
                                        class="block w-full bg-slate-50 border-2 border-transparent rounded-[1.5rem] p-5 focus:bg-white focus:border-indigo-500 focus:ring-0 transition-all font-bold text-slate-900">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div class="group">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3 block ml-1">Instagram URL</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-5 flex items-center pointer-events-none text-pink-500">
                                            <i class="fa-brands fa-instagram text-lg"></i>
                                        </div>
                                        <input type="url" name="instagram_url" placeholder="https://..."
                                            class="block w-full bg-slate-50 border-2 border-transparent rounded-[1.5rem] p-5 pl-14 focus:bg-white focus:border-pink-500 focus:ring-0 transition-all font-medium text-sm">
                                    </div>
                                </div>
                                <div class="group">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3 block ml-1">LinkedIn URL</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-5 flex items-center pointer-events-none text-blue-600">
                                            <i class="fa-brands fa-linkedin text-lg"></i>
                                        </div>
                                        <input type="url" name="linkedin_url" placeholder="https://..."
                                            class="block w-full bg-slate-50 border-2 border-transparent rounded-[1.5rem] p-5 pl-14 focus:bg-white focus:border-blue-600 focus:ring-0 transition-all font-medium text-sm">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="lg:col-span-2 group">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3 block ml-1 group-focus-within:text-blue-600">Biography (ID)</label>
                            <textarea name="bio_id" rows="3" 
                                class="block w-full bg-slate-50 border-2 border-transparent rounded-[1.5rem] p-5 focus:bg-white focus:border-blue-500 focus:ring-0 transition-all font-medium text-slate-700 leading-relaxed"></textarea>
                        </div>
                        <div class="lg:col-span-2 group">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3 block ml-1 group-focus-within:text-indigo-600">Biography (EN)</label>
                            <textarea name="bio_en" rows="3" 
                                class="block w-full bg-slate-50 border-2 border-transparent rounded-[1.5rem] p-5 focus:bg-white focus:border-indigo-500 focus:ring-0 transition-all font-medium text-slate-700 leading-relaxed"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end pt-6">
                        <button type="submit"
                            class="px-14 py-6 bg-blue-600 text-white font-black rounded-[2.5rem] hover:bg-blue-700 hover:scale-105 shadow-2xl shadow-blue-600/30 transition-all uppercase tracking-[0.2em] text-xs">
                            Confirm Registration
                        </button>
                    </div>
                </form>
            </div>

            <!-- Team Members Grid -->
            <div class="space-y-12">
                <div class="flex items-center space-x-6 px-6">
                    <h3 class="text-2xl font-black text-slate-900 whitespace-nowrap">The Squad</h3>
                    <div class="h-px w-full bg-gradient-to-r from-slate-200 to-transparent"></div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
                    @foreach ($owners as $owner)
                        <div class="glass p-10 rounded-[4rem] group relative overflow-hidden flex flex-col items-center text-center border-2 border-transparent hover:border-blue-500/20 hover:shadow-2xl transition-all duration-500" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                            <div class="absolute -right-10 -top-10 w-40 h-40 bg-blue-500/5 rounded-full blur-[50px] group-hover:bg-blue-500/10 transition-colors"></div>
                            
                            <div class="relative mb-8">
                                <div class="w-40 h-40 rounded-[3rem] overflow-hidden ring-8 ring-white shadow-2xl shadow-slate-200 group-hover:rotate-3 transition-transform duration-700">
                                    <img src="{{ $owner->media_url }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000">
                                </div>
                                <div class="absolute -bottom-4 left-1/2 -translate-x-1/2 flex space-x-2">
                                    @if ($owner->instagram_url)
                                        <a href="{{ $owner->instagram_url }}" target="_blank" 
                                            class="w-11 h-11 bg-white text-pink-600 rounded-2xl shadow-xl flex items-center justify-center hover:bg-pink-600 hover:text-white transition-all scale-90 hover:scale-100">
                                            <i class="fa-brands fa-instagram text-lg"></i>
                                        </a>
                                    @endif
                                    @if ($owner->linkedin_url)
                                        <a href="{{ $owner->linkedin_url }}" target="_blank" 
                                            class="w-11 h-11 bg-white text-blue-600 rounded-2xl shadow-xl flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all scale-90 hover:scale-100">
                                            <i class="fa-brands fa-linkedin text-lg"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <div class="space-y-2 mb-8">
                                <h4 class="text-2xl font-black text-slate-900 tracking-tighter">{{ $owner->name }}</h4>
                                <div class="inline-flex px-4 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-widest">
                                    {{ $owner->getTranslation('role') }}
                                </div>
                            </div>

                            @if($owner->getTranslation('bio'))
                                <p class="text-sm text-slate-500 leading-relaxed line-clamp-4 mb-10 px-4 italic font-medium">"{{ $owner->getTranslation('bio') }}"</p>
                            @endif

                            <div class="w-full pt-8 border-t border-slate-50 mt-auto">
                                <form action="{{ route('dashboard.owners.destroy', $owner->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('Hapus anggota tim ini?')" 
                                        class="w-full py-4 bg-red-50 text-red-500 hover:bg-red-500 hover:text-white rounded-[1.5rem] transition-all font-black uppercase tracking-widest text-[10px] flex items-center justify-center space-x-3 group/btn">
                                        <i class="fa-solid fa-trash-can group-hover/btn:animate-bounce"></i>
                                        <span>Exile Member</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                    
                    @if(count($owners) === 0)
                        <div class="col-span-full text-center py-32 glass rounded-[4rem] border-4 border-dashed border-slate-100">
                            <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-8 text-slate-200 text-5xl">
                                <i class="fa-solid fa-users-slash"></i>
                            </div>
                            <h3 class="text-2xl font-black text-slate-400 mb-2">No Active Personnel</h3>
                            <p class="text-slate-300 font-bold">Start by recruiting your first team member above.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

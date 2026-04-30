<x-app-layout>
    <x-slot name="header">
        {{ __('Portfolio Management') }}
    </x-slot>

    <div class="space-y-10 pb-20" x-data="{
        editMode: false,
        currentProject: {},
        newPreview: null,
        initEdit(project) {
            this.currentProject = project;
            this.editMode = true;
            this.newPreview = null;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        handleFileChange(e) {
            const file = e.target.files[0];
            if (file) {
                this.newPreview = URL.createObjectURL(file);
            }
        }
    }">
        <!-- Form Section -->
        <div class="glass p-10 rounded-[3rem] shadow-xl relative overflow-hidden" data-aos="fade-down">
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-blue-500/5 rounded-full blur-3xl"></div>
            
            <div class="flex justify-between items-center mb-8">
                <h3 class="text-2xl font-black text-slate-900 flex items-center">
                    <span class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fa-solid" :class="editMode ? 'fa-pen-to-square' : 'fa-plus'"></i>
                    </span>
                    <span x-text="editMode ? 'Edit Project' : 'Add New Project'"></span>
                </h3>
                <button x-show="editMode" @click="editMode = false" 
                    class="px-6 py-2 bg-slate-100 text-slate-500 font-bold rounded-xl hover:bg-slate-200 transition-all">
                    Cancel Edit
                </button>
            </div>

            <!-- Add/Edit Form -->
            <form :action="editMode ? '{{ route('dashboard.projects.index') }}/' + currentProject.id : '{{ route('dashboard.projects.store') }}'" 
                method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PATCH">
                </template>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Project Title (ID)</label>
                            <input type="text" name="title_id" required x-model="currentProject.title_id"
                                class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-bold">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Category (ID)</label>
                            <input type="text" name="category_id" required x-model="currentProject.category_id"
                                class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-medium"
                                placeholder="e.g. Web Development">
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Project Title (EN)</label>
                            <input type="text" name="title_en" required x-model="currentProject.title_en"
                                class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Category (EN)</label>
                            <input type="text" name="category_en" required x-model="currentProject.category_en"
                                class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-indigo-500 transition-all font-medium">
                        </div>
                    </div>
                    
                    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Description (ID)</label>
                            <textarea name="description_id" rows="3" x-model="currentProject.description_id"
                                class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-medium leading-relaxed"></textarea>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Description (EN)</label>
                            <textarea name="description_en" rows="3" x-model="currentProject.description_en"
                                class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-indigo-500 transition-all font-medium leading-relaxed"></textarea>
                        </div>
                    </div>

                    <!-- Media & Links -->
                    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-8 pt-4 border-t border-slate-50">
                        <div class="space-y-4">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Project Preview Image</label>
                            <div class="flex items-center space-x-4">
                                <div class="w-24 h-16 bg-slate-100 rounded-xl overflow-hidden shadow-sm flex items-center justify-center border-2 border-white ring-1 ring-slate-100 relative">
                                    <template x-if="newPreview">
                                        <img :src="newPreview" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!newPreview && currentProject.image">
                                        <img :src="currentProject.media_url" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!newPreview && !currentProject.image">
                                        <i class="fa-solid fa-image text-slate-300"></i>
                                    </template>
                                </div>
                                <div class="flex-1">
                                    <input type="file" name="image" @change="handleFileChange" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                </div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Project Live URL (Optional)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-link"></i>
                                </div>
                                <input type="text" name="url" x-model="currentProject.url"
                                    class="block w-full bg-slate-50 border-none rounded-2xl p-4 pl-12 focus:ring-2 focus:ring-blue-500 transition-all font-bold text-blue-600"
                                    placeholder="https://example.com">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit"
                        class="px-12 py-5 bg-slate-900 text-white font-black rounded-[2rem] hover:bg-black hover:scale-105 shadow-2xl shadow-slate-900/20 transition-all uppercase tracking-[0.2em] text-xs">
                        <span x-text="editMode ? 'Save Project Changes' : 'Publish Project'"></span>
                    </button>
                </div>
            </form>
        </div>

        <!-- List Section -->
        <div class="space-y-6">
            <h3 class="text-xl font-black text-slate-900 flex items-center ml-4">
                <span class="w-8 h-8 bg-slate-100 text-slate-500 rounded-lg flex items-center justify-center mr-3 text-sm">
                    <i class="fa-solid fa-briefcase"></i>
                </span>
                Portfolio Showcase ({{ count($projects) }})
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach ($projects as $project)
                    <div class="glass p-0 rounded-[3rem] shadow-xl overflow-hidden group flex flex-col md:flex-row card-hover" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                        <!-- Image Side -->
                        <div class="w-full md:w-48 h-48 md:h-auto relative overflow-hidden shrink-0">
                            @if ($project->image)
                                <img src="{{ $project->media_url }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            @else
                                <div class="w-full h-full bg-slate-100 flex items-center justify-center">
                                    <i class="fa-solid fa-camera text-4xl text-slate-200"></i>
                                </div>
                            @endif
                            <div class="absolute top-4 left-4">
                                <span class="px-3 py-1 bg-white/70 backdrop-blur-md rounded-full text-[10px] font-black uppercase tracking-widest text-slate-900 shadow-sm">{{ $project->category_id }}</span>
                            </div>
                        </div>

                        <!-- Content Side -->
                        <div class="flex-1 p-8 flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="text-xl font-black text-slate-900 tracking-tight leading-tight">{{ $project->title_id }}</h4>
                                    <div class="flex space-x-2">
                                        <button @click="initEdit({{ $project->toJson() }})" class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all">
                                            <i class="fa-solid fa-pen text-xs"></i>
                                        </button>
                                        <form action="{{ route('dashboard.projects.destroy', $project->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" onclick="return confirm('Hapus project ini?')" class="w-8 h-8 bg-red-50 text-red-600 rounded-lg flex items-center justify-center hover:bg-red-600 hover:text-white transition-all">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <p class="text-[10px] font-bold text-slate-400 italic mb-4 uppercase tracking-widest">{{ $project->title_en }}</p>
                                <p class="text-sm text-slate-500 line-clamp-2 leading-relaxed mb-4">{{ $project->description_id }}</p>
                            </div>

                            <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                                <div class="flex items-center space-x-2">
                                    <div class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></div>
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $project->category_id }}</span>
                                </div>
                                @if($project->url)
                                    <a href="{{ $project->url }}" target="_blank" class="text-blue-600 font-bold text-xs flex items-center hover:underline">
                                        Visit Project <i class="fa-solid fa-external-link ml-1.5 scale-75"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if(count($projects) === 0)
                <div class="text-center py-20 glass rounded-[3rem] border-2 border-dashed">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-200 text-4xl">
                        <i class="fa-solid fa-folder-open"></i>
                    </div>
                    <p class="text-slate-400 font-bold">No projects showcase yet</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

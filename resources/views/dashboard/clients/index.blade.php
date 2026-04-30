<x-app-layout>
    <x-slot name="header">
        {{ __('Our Partners') }}
    </x-slot>

    <div class="space-y-10 pb-20">
        <!-- Add Client Form -->
        <div class="glass p-10 rounded-[3rem] shadow-xl relative overflow-hidden" data-aos="fade-down">
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-blue-500/5 rounded-full blur-3xl"></div>
            
            <h3 class="text-2xl font-black text-slate-900 mb-8 flex items-center">
                <span class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mr-4">
                    <i class="fa-solid fa-handshake"></i>
                </span>
                Add New Client Logo
            </h3>

            <form action="{{ route('dashboard.clients.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8"
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
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-end">
                    <div class="space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Client / Brand Name</label>
                        <input type="text" name="name" placeholder="e.g. Google"
                            class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-bold">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Official Website URL</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-link text-xs"></i>
                            </div>
                            <input type="url" name="url" placeholder="https://..."
                                class="block w-full bg-slate-50 border-none rounded-2xl p-4 pl-10 focus:ring-2 focus:ring-blue-500 transition-all font-medium text-blue-600">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Logo File <span class="text-red-500">*</span></label>
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-10 bg-slate-50 rounded-xl overflow-hidden shadow-sm flex items-center justify-center border border-slate-100">
                                <template x-if="preview">
                                    <img :src="preview" class="w-full h-full object-contain p-1">
                                </template>
                                <template x-if="!preview">
                                    <i class="fa-solid fa-image text-slate-300"></i>
                                </template>
                            </div>
                            <input type="file" name="logo" required accept="image/*" @change="handleFileChange"
                                class="flex-1 text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-between pt-4">
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">
                        <i class="fa-solid fa-circle-info mr-1 text-blue-500"></i> PNG with transparent background recommended
                    </p>
                    <button type="submit"
                        class="px-12 py-5 bg-slate-900 text-white font-black rounded-[2rem] hover:bg-black hover:scale-105 shadow-2xl shadow-slate-900/20 transition-all uppercase tracking-[0.2em] text-xs">
                        Upload Partner
                    </button>
                </div>
            </form>
        </div>

        <!-- Grid Section -->
        <div class="space-y-6">
            <h3 class="text-xl font-black text-slate-900 flex items-center ml-4">
                <span class="w-8 h-8 bg-slate-100 text-slate-500 rounded-lg flex items-center justify-center mr-3 text-sm">
                    <i class="fa-solid fa-images"></i>
                </span>
                Active Partners Showcase ({{ $clients->count() }})
            </h3>
            
            @if ($clients->isEmpty())
                <div class="text-center py-20 glass rounded-[3rem] border-2 border-dashed">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-200 text-4xl">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>
                    <p class="text-slate-400 font-bold">No partners logos uploaded yet</p>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6">
                    @foreach ($clients as $client)
                        <div class="glass p-6 rounded-[2rem] card-hover group relative flex flex-col items-center justify-center min-h-[140px] text-center" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 50 }}">
                            @if ($client->url)
                                <a href="{{ $client->url }}" target="_blank" class="block w-full h-full flex flex-col items-center justify-center">
                            @endif
                            
                            <div class="w-full aspect-video flex items-center justify-center mb-4 p-2">
                                <img src="{{ $client->media_url }}" alt="{{ $client->name ?? 'Client Logo' }}" 
                                    class="max-h-full max-w-full object-contain filter grayscale group-hover:grayscale-0 transition-all duration-500">
                            </div>
                            
                            @if ($client->name)
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 group-hover:text-blue-600 transition-colors">{{ $client->name }}</span>
                            @endif

                            @if ($client->url)
                                </a>
                            @endif

                            <!-- Delete button -->
                            <form action="{{ route('dashboard.clients.destroy', $client->id) }}" method="POST"
                                class="absolute -top-2 -right-2 opacity-0 group-hover:opacity-100 transition-all scale-75 group-hover:scale-100">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Remove this client logo?')"
                                    class="w-8 h-8 bg-red-600 text-white rounded-xl flex items-center justify-center shadow-lg hover:bg-red-700">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

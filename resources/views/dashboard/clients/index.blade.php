<x-app-layout>
    <x-slot name="header">
        {{ __('Our Partners') }}
    </x-slot>

    <div class="space-y-10 pb-20" x-data="{ editingClient: null }">
        <!-- Add Section -->
        <div class="glass p-10 sm:p-12 rounded-[3.5rem] shadow-[0_32px_64px_-16px_rgba(0,0,0,0.05)] relative overflow-hidden" data-aos="fade-down">
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-blue-500/5 rounded-full blur-3xl"></div>
            <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-indigo-500/5 rounded-full blur-2xl"></div>
            
            <div class="flex items-center justify-between mb-10">
                <h3 class="text-3xl font-black text-slate-900 tracking-tight flex items-center">
                    <span class="w-14 h-14 bg-blue-600 text-white rounded-[1.5rem] flex items-center justify-center mr-5 shadow-xl shadow-blue-600/20 rotate-3 group-hover:rotate-0 transition-transform">
                        <i class="fa-solid fa-plus"></i>
                    </span>
                    Register New Partner
                </h3>
            </div>

            <form action="{{ route('dashboard.clients.store') }}" method="POST" enctype="multipart/form-data" class="space-y-10" x-data="{ preview: null, isSubscribed: false }">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Partner Identity</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-600 transition-colors">
                                <i class="fa-solid fa-building text-sm"></i>
                            </div>
                            <input type="text" name="name" required placeholder="e.g. Google Cloud"
                                class="block w-full bg-slate-50 border-2 border-transparent rounded-[1.5rem] p-4 pl-12 focus:bg-white focus:border-blue-500/20 focus:ring-4 focus:ring-blue-500/5 transition-all font-bold text-slate-700">
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Landing Page URL</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-600 transition-colors">
                                <i class="fa-solid fa-link text-sm"></i>
                            </div>
                            <input type="url" name="url" placeholder="https://..."
                                class="block w-full bg-slate-50 border-2 border-transparent rounded-[1.5rem] p-4 pl-12 focus:bg-white focus:border-blue-500/20 focus:ring-4 focus:ring-blue-500/5 transition-all font-bold text-blue-600/70">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Brand Visual (Logo)</label>
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-12 bg-white rounded-2xl overflow-hidden shadow-inner flex items-center justify-center border border-slate-100 ring-2 ring-slate-100/50">
                                <template x-if="preview">
                                    <img :src="preview" class="w-full h-full object-contain p-1.5">
                                </template>
                                <template x-if="!preview">
                                    <i class="fa-solid fa-image text-slate-300 text-xl"></i>
                                </template>
                            </div>
                            <input type="file" name="logo" required accept="image/*" 
                                @change="const file = $event.target.files[0]; if(file) preview = URL.createObjectURL(file)"
                                class="flex-1 text-[10px] text-slate-500 file:mr-4 file:py-2.5 file:px-6 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-slate-900 file:text-white hover:file:bg-black file:transition-all cursor-pointer">
                        </div>
                    </div>
                </div>

                <!-- Subscription Controls -->
                <div class="p-8 bg-slate-900 rounded-[2.5rem] shadow-2xl relative overflow-hidden group/sub">
                    <div class="absolute top-0 right-0 w-48 h-48 bg-blue-500/10 rounded-full blur-3xl -mr-24 -mt-24 transition-colors group-hover/sub:bg-blue-500/20"></div>
                    
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 relative z-10">
                        <div class="flex items-center space-x-5">
                            <div class="w-14 h-14 bg-blue-600/20 text-blue-400 rounded-2xl flex items-center justify-center text-2xl shadow-inner">
                                <i class="fa-solid fa-server"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-black text-white leading-none mb-1 text-shadow-sm">Server Hosting Subscription</h4>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Enable automated monthly invoicing for this partner</p>
                            </div>
                        </div>
                        
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_server_subscribed" value="1" class="sr-only peer" x-model="isSubscribed">
                            <div class="w-16 h-8 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[6px] after:left-[6px] after:bg-slate-600 after:rounded-full after:h-[1.25rem] after:w-[1.25rem] after:transition-all peer-checked:bg-blue-600 peer-checked:after:bg-white shadow-inner"></div>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8 pt-8 border-t border-slate-800/50" x-show="isSubscribed" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 ml-1">Billing Cycle Date (1-31)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-slate-600">
                                    <i class="fa-solid fa-calendar-check text-sm"></i>
                                </div>
                                <input type="number" name="billing_date" min="1" max="31" placeholder="e.g. 5"
                                    class="block w-full bg-slate-800/50 border-2 border-slate-700/30 rounded-2xl p-4 pl-12 focus:border-blue-500/50 focus:ring-0 transition-all font-black text-white placeholder-slate-700">
                            </div>
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 ml-1">Monthly Subscription Fee (IDR)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-blue-500/80 font-black text-sm">Rp</div>
                                <input type="number" name="subscription_price" placeholder="500000"
                                    class="block w-full bg-slate-800/50 border-2 border-slate-700/30 rounded-2xl p-4 pl-14 focus:border-blue-500/50 focus:ring-0 transition-all font-black text-white placeholder-slate-700">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-end pt-4">
                    <button type="submit"
                        class="px-16 py-6 bg-slate-900 hover:bg-black text-white font-black rounded-[2.5rem] shadow-[0_20px_40px_-12px_rgba(15,23,42,0.3)] hover:scale-[1.03] active:scale-95 transition-all duration-300 uppercase tracking-[0.3em] text-[10px] flex items-center space-x-3">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>Onboard Partner</span>
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
                        <div class="group relative" 
                            data-aos="zoom-in" data-aos-delay="{{ $loop->index * 50 }}">
                            
                            <!-- Main Card -->
                            <div class="glass p-8 rounded-[2.5rem] card-hover flex flex-col items-center justify-center min-h-[180px] text-center relative z-10 border border-white/50 shadow-[0_20px_40px_-15px_rgba(0,0,0,0.03)] group-hover:shadow-[0_32px_64px_-16px_rgba(59,130,246,0.1)] transition-all duration-500 overflow-hidden">
                                <!-- Background Glow on Hover -->
                                <div class="absolute -right-10 -bottom-10 w-24 h-24 bg-blue-500/0 group-hover:bg-blue-500/5 rounded-full blur-2xl transition-all duration-700"></div>
                                
                                @if ($client->url)
                                    <a href="{{ $client->url }}" target="_blank" class="block w-full h-full flex flex-col items-center justify-center">
                                @endif
                                
                                <div class="w-full aspect-video flex items-center justify-center mb-5 p-3 relative">
                                    <img src="{{ $client->media_url }}" alt="{{ $client->name ?? 'Client Logo' }}" 
                                        class="max-h-full max-w-full object-contain filter grayscale opacity-40 group-hover:grayscale-0 group-hover:opacity-100 group-hover:scale-110 transition-all duration-700 ease-out">
                                </div>
                                
                                @if ($client->name)
                                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 group-hover:text-blue-600 transition-colors duration-300">{{ $client->name }}</span>
                                @endif

                                @if ($client->is_server_subscribed)
                                    <div class="mt-4 flex flex-wrap justify-center gap-1.5 opacity-60 group-hover:opacity-100 transition-opacity">
                                        <span class="px-3 py-1 bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 rounded-full text-[8px] font-black uppercase tracking-widest flex items-center">
                                            <span class="w-1 h-1 bg-emerald-500 rounded-full mr-1.5"></span>
                                            Subscribed
                                        </span>
                                        <span class="px-3 py-1 bg-blue-500/10 text-blue-600 border border-blue-500/20 rounded-full text-[8px] font-black uppercase tracking-widest">
                                            Day {{ $client->billing_date }}
                                        </span>
                                    </div>
                                @endif

                                @if ($client->url)
                                    </a>
                                @endif
                            </div>

                            <!-- Floating Actions -->
                            <div class="absolute -top-3 -right-3 flex space-x-2 opacity-0 group-hover:opacity-100 transition-all duration-500 translate-y-2 group-hover:translate-y-0 z-30 pointer-events-none group-hover:pointer-events-auto">
                                <!-- Edit button -->
                                <button type="button" @click.stop="editingClient = {{ $client->toJson() }}"
                                    class="w-10 h-10 bg-white text-blue-600 rounded-[1.25rem] flex items-center justify-center shadow-[0_10px_20px_-5px_rgba(0,0,0,0.1)] hover:bg-blue-600 hover:text-white hover:scale-110 active:scale-95 transition-all duration-300 border border-slate-100">
                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                </button>

                                <!-- Delete button -->
                                <form action="{{ route('dashboard.clients.destroy', $client->id) }}" method="POST" @submit.stop class="m-0">
                                    @csrf @method('DELETE')
                                    <button type="submit" @click.stop="if(!confirm('Remove this partner?')) $event.preventDefault()"
                                        class="w-10 h-10 bg-white text-red-500 rounded-[1.25rem] flex items-center justify-center shadow-[0_10px_20px_-5px_rgba(0,0,0,0.1)] hover:bg-red-500 hover:text-white hover:scale-110 active:scale-95 transition-all duration-300 border border-slate-100">
                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                    </button>
                                </form>
                            </div>

                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Single Edit Modal (Global) -->
        <div x-show="editingClient" 
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 overflow-x-hidden overflow-y-auto" 
            x-cloak>
            
            <!-- Backdrop -->
            <div x-show="editingClient" 
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 transition-opacity bg-slate-900/40 backdrop-blur-sm" 
                @click="editingClient = null"></div>

            <!-- Modal Content -->
            <div x-show="editingClient" 
                x-transition:enter="ease-out duration-500"
                x-transition:enter-start="opacity-0 scale-95 translate-y-8"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="ease-in duration-300"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-8"
                class="relative w-full max-w-2xl bg-white/90 backdrop-blur-xl shadow-[0_32px_64px_-16px_rgba(0,0,0,0.2)] rounded-[3.5rem] border border-white overflow-hidden z-[110]">
                
                <div class="absolute top-0 left-0 right-0 h-32 bg-gradient-to-br from-blue-600/10 via-indigo-500/5 to-transparent -z-10"></div>
                
                <div class="p-10 sm:p-12">
                    <div class="flex justify-between items-start mb-10">
                        <div>
                            <h3 class="text-3xl font-black text-slate-900 tracking-tight leading-tight mb-2">Partner Profile</h3>
                            <p class="text-sm font-bold text-slate-400 uppercase tracking-widest flex items-center">
                                <span class="w-2 h-2 bg-blue-500 rounded-full mr-2 animate-pulse"></span>
                                Update Client & Subscription
                            </p>
                        </div>
                        <button @click="editingClient = null" 
                            class="w-12 h-12 bg-slate-100 hover:bg-red-50 hover:text-red-600 text-slate-400 rounded-2xl transition-all duration-300 flex items-center justify-center group">
                            <i class="fa-solid fa-xmark text-xl group-hover:rotate-90 transition-transform"></i>
                        </button>
                    </div>

                    <form :action="'/dashboard/clients/' + editingClient?.id" method="POST" enctype="multipart/form-data" class="space-y-10" x-data="{ isSubscribed: false }" x-init="$watch('editingClient', value => isSubscribed = value?.is_server_subscribed ? true : false)">
                        @csrf @method('PATCH')
                        
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Brand Identity</label>
                                    <input type="text" name="name" :value="editingClient?.name" required class="block w-full bg-slate-50 border-2 border-transparent rounded-[1.5rem] p-4 pl-6 focus:bg-white focus:border-blue-500/20 focus:ring-4 focus:ring-blue-500/5 transition-all font-bold text-slate-700">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Digital Presence</label>
                                    <input type="url" name="url" :value="editingClient?.url" class="block w-full bg-slate-50 border-2 border-transparent rounded-[1.5rem] p-4 pl-6 focus:bg-white focus:border-blue-500/20 focus:ring-4 focus:ring-blue-500/5 transition-all font-bold text-blue-600/80">
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Visual Assets</label>
                                <div class="flex items-center space-x-6 p-6 bg-slate-50 rounded-[2rem] border-2 border-dashed border-slate-200">
                                    <div class="w-24 h-16 bg-white rounded-2xl overflow-hidden shadow-xl flex items-center justify-center border border-slate-100">
                                        <img :src="editingClient?.media_url" class="w-full h-full object-contain p-2">
                                    </div>
                                    <input type="file" name="logo" accept="image/*" class="flex-1 text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-6 file:rounded-xl file:bg-slate-900 file:text-white cursor-pointer">
                                </div>
                            </div>
                        </div>

                        <div class="p-8 bg-slate-900 rounded-[2.5rem] shadow-2xl relative overflow-hidden group/sub">
                            <div class="flex items-center justify-between mb-8 relative z-10">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-blue-500/20 text-blue-400 rounded-2xl flex items-center justify-center text-xl shadow-inner"><i class="fa-solid fa-server"></i></div>
                                    <div>
                                        <h4 class="text-base font-black text-white leading-none mb-1">Server Subscription</h4>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Premium Hosting Support</p>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_server_subscribed" value="1" class="sr-only peer" x-model="isSubscribed">
                                    <div class="w-14 h-7 bg-slate-700 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-blue-600 after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-slate-400 after:rounded-full after:h-[1.3rem] after:w-[1.3rem] after:transition-all peer-checked:after:bg-white shadow-inner"></div>
                                </label>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 relative z-10" x-show="isSubscribed" x-transition>
                                <div class="space-y-2">
                                    <label class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-500 ml-1">Penagihan Setiap Tanggal</label>
                                    <input type="number" name="billing_date" :value="editingClient?.billing_date" min="1" max="31" class="block w-full bg-slate-800/50 border-2 border-slate-700/50 rounded-2xl p-4 focus:border-blue-500/50 focus:ring-0 transition-all font-black text-white">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-500 ml-1">Nominal Bulanan (IDR)</label>
                                    <input type="number" name="subscription_price" :value="parseInt(editingClient?.subscription_price || 0)" class="block w-full bg-slate-800/50 border-2 border-slate-700/50 rounded-2xl p-4 focus:border-blue-500/50 focus:ring-0 transition-all font-black text-white">
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row items-center gap-4 pt-6">
                            <button type="submit" class="w-full sm:flex-1 py-5 bg-blue-600 hover:bg-blue-700 text-white font-black rounded-[2rem] shadow-xl uppercase tracking-widest text-xs">Save Configuration</button>
                            <button type="button" @click="editingClient = null" class="w-full sm:w-auto px-10 py-5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-black rounded-[2rem] uppercase tracking-widest text-xs">Discard</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

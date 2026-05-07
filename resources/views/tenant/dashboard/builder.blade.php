@extends('layouts.tenant-app')

@section('title', 'Site Builder')

@section('content')
<div class="h-[calc(100vh-120px)] -m-8 flex overflow-hidden" x-data="siteBuilder()">
    <!-- Left Sidebar: Controls -->
    <aside class="w-96 bg-slate-900/50 backdrop-blur-xl border-r border-white/5 flex flex-col overflow-hidden">
        <!-- Builder Header -->
        <div class="p-6 border-b border-white/5 bg-gradient-to-br from-blue-600/10 to-transparent">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-xl font-black text-white uppercase tracking-tighter italic">Studio <span class="theme-text">Builder</span></h2>
                <div class="flex items-center space-x-2">
                    <div x-show="isSaving" class="flex items-center text-[10px] font-bold text-blue-400 uppercase tracking-widest">
                        <i class="fa-solid fa-circle-notch fa-spin mr-2"></i> Saving...
                    </div>
                    <div x-show="!isSaving" class="flex items-center text-[10px] font-bold text-emerald-400 uppercase tracking-widest">
                        <i class="fa-solid fa-check-double mr-2"></i> Autosaved
                    </div>
                </div>
            </div>
            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Live Editor <span class="text-white/20 mx-1">|</span> {{ $siteType === 'sales' ? 'E-Commerce Mode' : 'Personal Branding Mode' }}</p>
        </div>

        <!-- Control Sections -->
        <div class="flex-1 overflow-y-auto custom-scrollbar p-6 space-y-6">
            
            <!-- Global Design Section -->
            <div class="space-y-4">
                <div class="flex items-center space-x-2 text-slate-400 mb-2">
                    <i class="fa-solid fa-palette text-[10px]"></i>
                    <span class="text-[10px] font-black uppercase tracking-[0.2em]">Visual Identity</span>
                </div>
                
                <div class="glass-card p-5 rounded-3xl space-y-4 border border-white/5">
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3">Theme Accent</label>
                        <div class="grid grid-cols-5 gap-3">
                            @foreach(['blue', 'emerald', 'rose', 'amber', 'violet', 'indigo', 'teal', 'orange', 'slate', 'pink'] as $color)
                                <button @click="sections.design.theme_color = '{{ $color }}'; triggerSave()" 
                                    :class="sections.design.theme_color === '{{ $color }}' ? 'ring-2 ring-white ring-offset-2 ring-offset-slate-900 scale-110' : 'opacity-50 hover:opacity-100'"
                                    class="w-full aspect-square rounded-xl transition-all shadow-lg"
                                    style="background-color: {{ $color === 'blue' ? '#3b82f6' : ($color === 'emerald' ? '#10b981' : ($color === 'rose' ? '#f43f5e' : ($color === 'amber' ? '#f59e0b' : ($color === 'violet' ? '#8b5cf6' : ($color === 'indigo' ? '#6366f1' : ($color === 'teal' ? '#14b8a6' : ($color === 'orange' ? '#f97316' : ($color === 'slate' ? '#64748b' : '#ec4899')))))))) }};">
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3">Button Style</label>
                        <div class="grid grid-cols-3 gap-2">
                            <button @click="sections.design.button_style = 'rounded-none'; triggerSave()" :class="sections.design.button_style === 'rounded-none' ? 'bg-white text-black' : 'bg-white/5 text-white hover:bg-white/10'" class="py-2 text-[10px] font-bold uppercase rounded-none transition-all border border-white/5">Sharp</button>
                            <button @click="sections.design.button_style = 'rounded-xl'; triggerSave()" :class="sections.design.button_style === 'rounded-xl' ? 'bg-white text-black' : 'bg-white/5 text-white hover:bg-white/10'" class="py-2 text-[10px] font-bold uppercase rounded-xl transition-all border border-white/5">Soft</button>
                            <button @click="sections.design.button_style = 'rounded-full'; triggerSave()" :class="sections.design.button_style === 'rounded-full' ? 'bg-white text-black' : 'bg-white/5 text-white hover:bg-white/10'" class="py-2 text-[10px] font-bold uppercase rounded-full transition-all border border-white/5">Capsule</button>
                        </div>
                    </div>

                    @if($siteType === 'sales')
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3">Product Layout</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button @click="sections.design.card_style = 'grid'; triggerSave()" :class="sections.design.card_style === 'grid' ? 'bg-blue-600 text-white' : 'bg-white/5 text-white hover:bg-white/10'" class="py-2.5 text-[10px] font-bold uppercase rounded-xl transition-all border border-white/5 flex items-center justify-center">
                                <i class="fa-solid fa-grip mr-2"></i> Grid
                            </button>
                            <button @click="sections.design.card_style = 'list'; triggerSave()" :class="sections.design.card_style === 'list' ? 'bg-blue-600 text-white' : 'bg-white/5 text-white hover:bg-white/10'" class="py-2.5 text-[10px] font-bold uppercase rounded-xl transition-all border border-white/5 flex items-center justify-center">
                                <i class="fa-solid fa-list mr-2"></i> List
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="h-px bg-white/5"></div>

            <!-- Content Blocks -->
            <div class="space-y-4">
                <div class="flex items-center space-x-2 text-slate-400 mb-2">
                    <i class="fa-solid fa-cubes text-[10px]"></i>
                    <span class="text-[10px] font-black uppercase tracking-[0.2em]">Experience Blocks</span>
                </div>

                <!-- Hero Section -->
                <div class="glass-card rounded-[1.5rem] border border-white/5 overflow-hidden" x-data="{ expanded: false }">
                    <button @click="expanded = !expanded" class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-white/5 transition-all">
                        <span class="font-black text-xs text-white uppercase tracking-widest flex items-center">
                            <div class="w-8 h-8 rounded-lg bg-blue-500/20 text-blue-400 flex items-center justify-center mr-3">
                                <i class="fa-solid fa-bolt text-[10px]"></i>
                            </div>
                            Hero Entrance
                        </span>
                        <i class="fa-solid fa-chevron-down text-slate-600 text-[10px] transition-transform" :class="expanded ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="expanded" x-collapse class="px-5 pb-6 space-y-5 border-t border-white/5 pt-5">
                        <div class="space-y-2">
                            <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest">Main Headline</label>
                            <input type="text" x-model="hero.headline" @input="triggerSave" class="w-full bg-slate-950 border border-white/10 rounded-xl px-4 py-3 text-xs text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest">Sub-narrative</label>
                            <textarea x-model="hero.subheadline" @input="triggerSave" rows="3" class="w-full bg-slate-950 border border-white/10 rounded-xl px-4 py-3 text-xs text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-2">
                                <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest">Action Text</label>
                                <input type="text" x-model="hero.cta_text" @input="triggerSave" class="w-full bg-slate-950 border border-white/10 rounded-xl px-4 py-3 text-xs text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest">Action Link</label>
                                <input type="text" x-model="hero.cta_link" @input="triggerSave" class="w-full bg-slate-950 border border-white/10 rounded-xl px-4 py-3 text-xs text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest">Atmosphere Image</label>
                            <div class="relative group cursor-pointer">
                                <input type="file" @change="handleImageUpload($event, 'hero')" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer z-10">
                                <div class="w-full py-4 border-2 border-dashed border-white/10 rounded-2xl flex flex-col items-center justify-center text-slate-500 group-hover:border-blue-500/50 transition-all">
                                    <i class="fa-solid fa-cloud-arrow-up mb-2"></i>
                                    <span class="text-[9px] font-black uppercase tracking-widest">Upload Image</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- About Section (Hide for Sales) -->
                <div x-show="siteType === 'branding'" class="glass-card rounded-[1.5rem] border border-white/5 overflow-hidden" x-data="{ expanded: false }">
                    <button @click="expanded = !expanded" class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-white/5 transition-all">
                        <span class="font-black text-xs text-white uppercase tracking-widest flex items-center">
                            <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center mr-3">
                                <i class="fa-solid fa-id-card text-[10px]"></i>
                            </div>
                            Brand Identity
                        </span>
                        <i class="fa-solid fa-chevron-down text-slate-600 text-[10px] transition-transform" :class="expanded ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="expanded" x-collapse class="px-5 pb-6 space-y-5 border-t border-white/5 pt-5">
                        <div class="space-y-2">
                            <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest">Identity Title</label>
                            <input type="text" x-model="sections.about.title" @input="triggerSave" class="w-full bg-slate-950 border border-white/10 rounded-xl px-4 py-3 text-xs text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest">Brand Narrative</label>
                            <textarea x-model="sections.about.description" @input="triggerSave" rows="4" class="w-full bg-slate-950 border border-white/10 rounded-xl px-4 py-3 text-xs text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all"></textarea>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest">Brand Visual</label>
                            <div class="relative group cursor-pointer">
                                <input type="file" @change="handleImageUpload($event, 'about')" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer z-10">
                                <div class="w-full py-4 border-2 border-dashed border-white/10 rounded-2xl flex flex-col items-center justify-center text-slate-500 group-hover:border-emerald-500/50 transition-all">
                                    <i class="fa-solid fa-image mb-2"></i>
                                    <span class="text-[9px] font-black uppercase tracking-widest">Upload Profile Visual</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Features Section (Expertise) -->
                <div x-show="siteType === 'branding'" class="glass-card rounded-[1.5rem] border border-white/5 overflow-hidden" x-data="{ expanded: false }">
                    <button @click="expanded = !expanded" class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-white/5 transition-all">
                        <span class="font-black text-xs text-white uppercase tracking-widest flex items-center">
                            <div class="w-8 h-8 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center mr-3">
                                <i class="fa-solid fa-star text-[10px]"></i>
                            </div>
                            Core Expertise
                        </span>
                        <i class="fa-solid fa-chevron-down text-slate-600 text-[10px] transition-transform" :class="expanded ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="expanded" x-collapse class="px-5 pb-6 space-y-4 border-t border-white/5 pt-5">
                        <template x-for="(feature, index) in sections.features" :key="index">
                            <div class="p-4 bg-slate-950 rounded-2xl border border-white/5 space-y-3 relative group">
                                <button @click="removeFeature(index)" class="absolute top-2 right-2 w-6 h-6 bg-rose-500/20 text-rose-400 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i class="fa-solid fa-xmark text-[10px]"></i>
                                </button>
                                <input type="text" x-model="feature.title" @input="triggerSave" placeholder="Expertise Title" class="w-full bg-transparent border-0 border-b border-white/10 focus:border-indigo-500 focus:ring-0 p-0 text-xs font-black text-white">
                                <textarea x-model="feature.description" @input="triggerSave" rows="2" placeholder="Description..." class="w-full bg-transparent border-0 focus:ring-0 p-0 text-[11px] text-slate-400"></textarea>
                            </div>
                        </template>
                        <button @click="addFeature()" class="w-full py-3 bg-indigo-500/20 text-indigo-400 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-500/30 transition-all border border-indigo-500/20">
                            Add New Expertise <i class="fa-solid fa-plus ml-1"></i>
                        </button>
                    </div>
                </div>

                <!-- Products/Portfolio Section -->
                <div class="glass-card rounded-[1.5rem] border border-white/5 overflow-hidden" x-data="{ expanded: false }">
                    <button @click="expanded = !expanded" class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-white/5 transition-all">
                        <span class="font-black text-xs text-white uppercase tracking-widest flex items-center">
                            <div class="w-8 h-8 rounded-lg bg-pink-500/20 text-pink-400 flex items-center justify-center mr-3">
                                <i class="fa-solid fa-images text-[10px]"></i>
                            </div>
                            <span x-text="siteType === 'branding' ? 'Creative Gallery' : 'Product Showcase'"></span>
                        </span>
                        <i class="fa-solid fa-chevron-down text-slate-600 text-[10px] transition-transform" :class="expanded ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="expanded" x-collapse class="px-5 pb-6 space-y-4 border-t border-white/5 pt-5">
                        <template x-for="(product, index) in sections.products" :key="index">
                            <div class="p-4 bg-slate-950 rounded-2xl border border-white/5 space-y-4 relative group">
                                <button @click="removeProduct(index)" class="absolute top-2 right-2 w-6 h-6 bg-rose-500/20 text-rose-400 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i class="fa-solid fa-xmark text-[10px]"></i>
                                </button>
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 rounded-xl bg-slate-900 border border-white/10 flex items-center justify-center text-slate-700 overflow-hidden relative">
                                        <input type="file" @change="handleProductImage($event, index)" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer z-10">
                                        <template x-if="product.image || product.image_base64">
                                            <img :src="product.image_base64 || getImageUrl(product.image)" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!product.image && !product.image_base64">
                                            <i class="fa-solid fa-camera"></i>
                                        </template>
                                    </div>
                                    <div class="flex-1">
                                        <input type="text" x-model="product.name" @input="triggerSave" placeholder="Project/Product Name" class="w-full bg-transparent border-0 border-b border-white/10 focus:border-pink-500 focus:ring-0 p-0 text-xs font-black text-white">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <input type="text" x-model="product.price" @input="triggerSave" placeholder="Price/Label" class="w-full bg-slate-900 border border-white/10 rounded-lg px-3 py-2 text-[10px] text-white">
                                    <input type="text" x-model="sections.design.product_cta_text" @input="triggerSave" placeholder="CTA Text" class="w-full bg-slate-900 border border-white/10 rounded-lg px-3 py-2 text-[10px] text-white">
                                </div>
                                <textarea x-model="product.description" @input="triggerSave" rows="2" placeholder="Short summary..." class="w-full bg-slate-900 border border-white/10 rounded-lg px-3 py-2 text-[10px] text-slate-400"></textarea>
                            </div>
                        </template>
                        <button @click="addProduct()" class="w-full py-3 bg-pink-500/20 text-pink-400 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-pink-500/30 transition-all border border-pink-500/20">
                            Add Gallery Item <i class="fa-solid fa-plus ml-1"></i>
                        </button>
                    </div>
                </div>

                <!-- Footer Block -->
                <div class="glass-card rounded-[1.5rem] border border-white/5 overflow-hidden" x-data="{ expanded: false }">
                    <button @click="expanded = !expanded" class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-white/5 transition-all">
                        <span class="font-black text-xs text-white uppercase tracking-widest flex items-center">
                            <div class="w-8 h-8 rounded-lg bg-slate-500/20 text-slate-400 flex items-center justify-center mr-3">
                                <i class="fa-solid fa-address-book text-[10px]"></i>
                            </div>
                            Connect Base
                        </span>
                        <i class="fa-solid fa-chevron-down text-slate-600 text-[10px] transition-transform" :class="expanded ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="expanded" x-collapse class="px-5 pb-6 space-y-4 border-t border-white/5 pt-5">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <label class="text-[8px] font-black text-slate-500 uppercase tracking-widest">WhatsApp</label>
                                <input type="text" x-model="sections.footer.whatsapp" @input="triggerSave" class="w-full bg-slate-950 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[8px] font-black text-slate-500 uppercase tracking-widest">Email</label>
                                <input type="email" x-model="sections.footer.email" @input="triggerSave" class="w-full bg-slate-950 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white">
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[8px] font-black text-slate-500 uppercase tracking-widest">HQ Address</label>
                            <textarea x-model="sections.footer.address" @input="triggerSave" rows="2" class="w-full bg-slate-950 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white"></textarea>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </aside>

    <!-- Right Content: Live Preview -->
    <main class="flex-1 bg-slate-950/20 relative h-full flex flex-col">
        <!-- Device Toggles -->
        <div class="absolute top-6 left-1/2 -translate-x-1/2 z-10 flex items-center glass rounded-2xl shadow-2xl border border-white/10 p-1.5 space-x-1">
            <button @click="device = 'desktop'" :class="device === 'desktop' ? 'bg-white text-black' : 'text-slate-400 hover:text-white'" class="w-12 h-10 rounded-xl flex items-center justify-center transition-all duration-300">
                <i class="fa-solid fa-desktop text-sm"></i>
            </button>
            <button @click="device = 'mobile'" :class="device === 'mobile' ? 'bg-white text-black' : 'text-slate-400 hover:text-white'" class="w-12 h-10 rounded-xl flex items-center justify-center transition-all duration-300">
                <i class="fa-solid fa-mobile-screen text-sm"></i>
            </button>
        </div>

        <div class="flex-1 w-full flex items-center justify-center p-12 overflow-hidden transition-all duration-500">
            <div class="h-full bg-white shadow-[0_0_100px_rgba(0,0,0,0.5)] rounded-[2rem] overflow-hidden transition-all duration-700 border border-white/5 relative"
                 :class="device === 'mobile' ? 'w-[375px] rounded-[3rem]' : 'w-full'">
                <!-- Browser Header Mockup (Desktop Only) -->
                <template x-if="device === 'desktop'">
                    <div class="h-8 bg-slate-100 border-b flex items-center px-4 space-x-1.5">
                        <div class="w-2.5 h-2.5 rounded-full bg-rose-400"></div>
                        <div class="w-2.5 h-2.5 rounded-full bg-amber-400"></div>
                        <div class="w-2.5 h-2.5 rounded-full bg-emerald-400"></div>
                        <div class="flex-1 mx-10 h-5 bg-white rounded-md border border-slate-200 text-[8px] flex items-center px-2 text-slate-400 italic">https://{{ tenant('id') }}.fkstudio.id/preview</div>
                    </div>
                </template>
                <iframe id="preview-iframe" src="{{ route('tenant.home') }}" class="w-full h-full border-0"></iframe>
            </div>
        </div>
    </main>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 20px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }
        .glass-card { background: rgba(255,255,255,0.02); backdrop-filter: blur(5px); }
    </style>

    <script>
        function siteBuilder() {
            return {
                device: 'desktop',
                isSaving: false,
                saveTimeout: null,
                
                // Initialize Data from PHP
                hero: @json($hero),
                sections: @json($sectionsData),
                siteType: '{{ $siteType }}',
                
                triggerSave() {
                    this.isSaving = true;
                    clearTimeout(this.saveTimeout);
                    
                    // Debounce 1000ms
                    this.saveTimeout = setTimeout(() => {
                        this.saveData();
                    }, 1000);
                },

                getImageUrl(path) {
                    if (!path) return '';
                    if (path.startsWith('http') || path.startsWith('data:')) return path;
                    return `/storage/${path}`;
                },
                
                addFeature() {
                    if (!this.sections.features) this.sections.features = [];
                    this.sections.features.push({ title: '', description: '' });
                    this.triggerSave();
                },
                
                removeFeature(index) {
                    this.sections.features.splice(index, 1);
                    this.triggerSave();
                },
                
                addProduct() {
                    if (!this.sections.products) this.sections.products = [];
                    this.sections.products.push({ name: '', price: '', description: '', image: '' });
                    this.triggerSave();
                },
                
                removeProduct(index) {
                    this.sections.products.splice(index, 1);
                    this.triggerSave();
                },
                
                handleImageUpload(event, type) {
                    const file = event.target.files[0];
                    if (!file) return;
                    
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        if (type === 'hero') {
                            this.hero.background_image_base64 = e.target.result;
                        } else if (type === 'about') {
                            this.sections.about.image_base64 = e.target.result;
                        }
                        this.triggerSave();
                    };
                    reader.readAsDataURL(file);
                },

                handleProductImage(event, index) {
                    const file = event.target.files[0];
                    if (!file) return;
                    
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.sections.products[index].image_base64 = e.target.result;
                        this.triggerSave();
                    };
                    reader.readAsDataURL(file);
                },
                
                async saveData() {
                    try {
                        const response = await fetch("{{ route('tenant.builder.save') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                hero: this.hero,
                                sections: this.sections
                            })
                        });
                        
                        if (response.ok) {
                            // Clear base64 data to keep next requests small
                            if (this.hero.background_image_base64) delete this.hero.background_image_base64;
                            if (this.sections.about.image_base64) delete this.sections.about.image_base64;
                            if (this.sections.features) {
                                this.sections.features.forEach(f => { if (f.icon_base64) delete f.icon_base64; });
                            }
                            if (this.sections.products) {
                                this.sections.products.forEach(p => { if (p.image_base64) delete p.image_base64; });
                            }

                            // Reload the iframe with cache buster
                            const iframe = document.getElementById('preview-iframe');
                            const url = new URL(iframe.src);
                            url.searchParams.set('t', Date.now());
                            iframe.src = url.toString();
                            
                            setTimeout(() => { this.isSaving = false; }, 500);
                        }
                    } catch (error) {
                        console.error("Save failed", error);
                        this.isSaving = false;
                    }
                }
            }
        }
    </script>
</div>
@endsection

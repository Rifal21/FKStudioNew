<x-app-layout>
    <x-slot name="header">
        {{ __('Overview') }}
    </x-slot>

    <div class="space-y-8">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-6">
            <div class="glass p-6 rounded-[2rem] card-hover relative overflow-hidden group" data-aos="fade-up" data-aos-delay="100">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl group-hover:bg-blue-500/20 transition-colors"></div>
                <div class="relative flex items-center">
                    <div class="p-4 bg-blue-500/10 rounded-2xl text-blue-600 mr-4">
                        <i class="fa-solid fa-briefcase text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.2em] text-slate-400">Services</p>
                        <p class="text-3xl font-black text-slate-900">{{ $servicesCount }}</p>
                    </div>
                </div>
            </div>

            <div class="glass p-6 rounded-[2rem] card-hover relative overflow-hidden group" data-aos="fade-up" data-aos-delay="200">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-green-500/10 rounded-full blur-2xl group-hover:bg-green-500/20 transition-colors"></div>
                <div class="relative flex items-center">
                    <div class="p-4 bg-green-500/10 rounded-2xl text-green-600 mr-4">
                        <i class="fa-solid fa-layer-group text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.2em] text-slate-400">Projects</p>
                        <p class="text-3xl font-black text-slate-900">{{ $projectsCount }}</p>
                    </div>
                </div>
            </div>

            <div class="glass p-6 rounded-[2rem] card-hover relative overflow-hidden group" data-aos="fade-up" data-aos-delay="300">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-purple-500/10 rounded-full blur-2xl group-hover:bg-purple-500/20 transition-colors"></div>
                <div class="relative flex items-center">
                    <div class="p-4 bg-purple-500/10 rounded-2xl text-purple-600 mr-4">
                        <i class="fa-solid fa-comment-dots text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.2em] text-slate-400">Feedback</p>
                        <p class="text-3xl font-black text-slate-900">{{ $testimonialsCount }}</p>
                    </div>
                </div>
            </div>

            <div class="glass p-6 rounded-[2rem] card-hover relative overflow-hidden group" data-aos="fade-up" data-aos-delay="400">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-orange-500/10 rounded-full blur-2xl group-hover:bg-orange-500/20 transition-colors"></div>
                <div class="relative flex items-center">
                    <div class="p-4 bg-orange-500/10 rounded-2xl text-orange-600 mr-4">
                        <i class="fa-solid fa-user-tie text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.2em] text-slate-400">Team</p>
                        <p class="text-3xl font-black text-slate-900">{{ $ownersCount ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="glass p-6 rounded-[2rem] card-hover relative overflow-hidden group" data-aos="fade-up" data-aos-delay="500">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-pink-500/10 rounded-full blur-2xl group-hover:bg-pink-500/20 transition-colors"></div>
                <div class="relative flex items-center">
                    <div class="p-4 bg-pink-500/10 rounded-2xl text-pink-600 mr-4">
                        <i class="fa-solid fa-tags text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.2em] text-slate-400">Packages</p>
                        <p class="text-3xl font-black text-slate-900">{{ $packagesCount ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="glass p-6 rounded-[2rem] card-hover relative overflow-hidden group" data-aos="fade-up" data-aos-delay="600">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-colors"></div>
                <div class="relative flex items-center">
                    <div class="p-4 bg-emerald-500/10 rounded-2xl text-emerald-600 mr-4">
                        <i class="fa-solid fa-file-invoice-dollar text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.2em] text-slate-400">Invoices</p>
                        <p class="text-3xl font-black text-slate-900">{{ $invoicesCount ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Welcome Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 glass p-10 rounded-[3rem] relative overflow-hidden group" data-aos="fade-right">
                <div class="absolute right-0 bottom-0 opacity-10 translate-x-10 translate-y-10 group-hover:scale-110 transition-transform duration-700">
                    <i class="fa-solid fa-wand-magic-sparkles text-[15rem]"></i>
                </div>
                <div class="relative">
                    <h3 class="text-4xl font-black text-slate-900 mb-4 tracking-tighter">Welcome back, <span class="text-blue-600">{{ Auth::user()->name }}</span>!</h3>
                    <p class="text-lg text-slate-500 max-w-xl leading-relaxed">Kelola seluruh konten landing page Anda dengan mudah melalui panel kontrol baru yang lebih cepat, responsif, dan intuitif.</p>
                    
                    <div class="mt-10 flex flex-wrap gap-4">
                        <a href="{{ route('dashboard.projects.index') }}" class="px-8 py-4 bg-slate-900 text-white font-bold rounded-2xl hover:bg-black transition-all flex items-center space-x-2">
                            <span>Manage Portfolio</span>
                            <i class="fa-solid fa-arrow-right text-xs opacity-50"></i>
                        </a>
                        <a href="/" target="_blank" class="px-8 py-4 bg-white border-2 border-slate-100 text-slate-700 font-bold rounded-2xl hover:bg-slate-50 transition-all flex items-center space-x-2">
                            <span>View Live Site</span>
                            <i class="fa-solid fa-external-link text-xs opacity-50"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="glass p-8 rounded-[3rem] flex flex-col justify-between" data-aos="fade-left">
                <div>
                    <h4 class="text-xl font-black text-slate-900 mb-6 tracking-tight">Quick Actions</h4>
                    <div class="space-y-3">
                        <a href="{{ route('dashboard.hero.edit') }}" class="flex items-center space-x-3 p-4 bg-slate-50 rounded-2xl hover:bg-blue-50 hover:text-blue-600 transition-all group">
                            <i class="fa-solid fa-image w-5 text-slate-400 group-hover:text-blue-500"></i>
                            <span class="font-bold">Update Hero Image</span>
                        </a>
                        <a href="{{ route('dashboard.settings.edit') }}" class="flex items-center space-x-3 p-4 bg-slate-50 rounded-2xl hover:bg-blue-50 hover:text-blue-600 transition-all group">
                            <i class="fa-solid fa-info-circle w-5 text-slate-400 group-hover:text-blue-500"></i>
                            <span class="font-bold">Contact Information</span>
                        </a>
                        <a href="{{ route('dashboard.about.edit') }}" class="flex items-center space-x-3 p-4 bg-slate-50 rounded-2xl hover:bg-blue-50 hover:text-blue-600 transition-all group">
                            <i class="fa-solid fa-address-card w-5 text-slate-400 group-hover:text-blue-500"></i>
                            <span class="font-bold">Edit About & Team</span>
                        </a>
                        <a href="{{ route('dashboard.packages.index') }}" class="flex items-center space-x-3 p-4 bg-slate-50 rounded-2xl hover:bg-blue-50 hover:text-blue-600 transition-all group">
                            <i class="fa-solid fa-tags w-5 text-slate-400 group-hover:text-blue-500"></i>
                            <span class="font-bold">Service Packages</span>
                        </a>
                        <a href="{{ route('dashboard.invoices.create') }}" class="flex items-center space-x-3 p-4 bg-slate-50 rounded-2xl hover:bg-blue-50 hover:text-blue-600 transition-all group">
                            <i class="fa-solid fa-file-invoice w-5 text-slate-400 group-hover:text-blue-500"></i>
                            <span class="font-bold">Generate Invoice</span>
                        </a>
                        <a href="{{ route('profile.edit') }}" class="flex items-center space-x-3 p-4 bg-slate-50 rounded-2xl hover:bg-blue-50 hover:text-blue-600 transition-all group">
                            <i class="fa-solid fa-user-circle w-5 text-slate-400 group-hover:text-blue-500"></i>
                            <span class="font-bold">Profile Account</span>
                        </a>
                    </div>
                </div>
                <div class="mt-8 p-6 bg-blue-600 rounded-[2rem] text-white">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-60 mb-2">System Status</p>
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        <span class="font-bold">All Systems Operational</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

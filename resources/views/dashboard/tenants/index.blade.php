<x-app-layout>
    <x-slot name="header">
        {{ __('Tenant Management') }}
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="glass rounded-[2rem] overflow-hidden shadow-2xl">
            <div class="p-6 border-b border-white/5 flex justify-between items-center bg-slate-900/50">
                <div>
                    <h2 class="text-lg font-black text-white">Active Tenants</h2>
                    <p class="text-sm text-slate-400">Manage all provisioned landing pages</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="p-6 text-xs font-black uppercase tracking-widest text-slate-400">Tenant ID / Subdomain</th>
                            <th class="p-6 text-xs font-black uppercase tracking-widest text-slate-400">Branding Name</th>
                            <th class="p-6 text-xs font-black uppercase tracking-widest text-slate-400">Owner / User</th>
                            <th class="p-6 text-xs font-black uppercase tracking-widest text-slate-400">Domain Links</th>
                            <th class="p-6 text-xs font-black uppercase tracking-widest text-slate-400">Created At</th>
                            <th class="p-6 text-xs font-black uppercase tracking-widest text-slate-400 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($tenants as $tenant)
                            <tr class="hover:bg-slate-50/80 transition-colors group">
                                <td class="p-6">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20 text-white font-black">
                                            {{ strtoupper(substr($tenant->id, 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="font-bold text-slate-900">{{ $tenant->id }}</span>
                                            <div class="text-[10px] text-slate-400 mt-1">Tenant ID</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-6">
                                    <span class="font-bold text-slate-700">{{ $tenant->branding_name ?? 'N/A' }}</span>
                                </td>
                                <td class="p-6">
                                    @if($tenant->owner)
                                        <div class="flex items-center space-x-2">
                                            <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-[10px] font-black text-slate-500 border border-slate-200">
                                                {{ strtoupper(substr($tenant->owner->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="text-xs font-black text-slate-900 leading-none">{{ $tenant->owner->name }}</p>
                                                <p class="text-[10px] text-slate-400 mt-1">{{ $tenant->owner->email }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400 italic">No Owner</span>
                                    @endif
                                </td>
                                <td class="p-6 space-y-1">
                                    @foreach($tenant->domains as $domain)
                                        <a href="http://{{ $domain->domain }}:8000" target="_blank" class="inline-flex items-center text-xs font-mono text-blue-600 hover:text-blue-800 bg-blue-50 px-2 py-1 rounded-md transition-colors">
                                            <i class="fa-solid fa-arrow-up-right-from-square mr-2"></i> {{ $domain->domain }}
                                        </a>
                                    @endforeach
                                </td>
                                <td class="p-6 text-xs text-slate-500">
                                    {{ $tenant->created_at->format('d M Y H:i') }}
                                </td>
                                <td class="p-6 text-right space-x-2">
                                    @if($tenant->domains->count() > 0)
                                        <a href="http://{{ $tenant->domains->first()->domain }}:8000" target="_blank" class="inline-flex items-center px-3 py-2 bg-slate-100 text-slate-600 rounded-lg text-xs font-bold hover:bg-slate-200 transition-colors">
                                            <i class="fa-solid fa-eye mr-2"></i> Visit
                                        </a>
                                    @endif
                                    
                                    <form action="{{ route('dashboard.tenants.destroy', $tenant->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-2 bg-red-50 text-red-600 rounded-lg text-xs font-bold hover:bg-red-100 transition-colors" onclick="return confirm('WARNING: Are you sure you want to permanently delete this tenant, its database, and domains?')">
                                            <i class="fa-solid fa-trash-can mr-2"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-12 text-center">
                                    <div class="inline-flex items-center justify-center w-16 h-16 bg-slate-100 rounded-2xl mb-4 text-slate-400">
                                        <i class="fa-solid fa-server text-2xl"></i>
                                    </div>
                                    <h3 class="text-sm font-bold text-slate-900 mb-1">No Tenants Found</h3>
                                    <p class="text-xs text-slate-500">There are no provisioned websites currently active.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

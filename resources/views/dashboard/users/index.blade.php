<x-app-layout>
    <x-slot name="header">
        {{ __('User Management') }}
    </x-slot>

    <div class="space-y-10 pb-20" x-data="{
        editMode: false,
        currentUser: {},
        initEdit(userData) {
            this.currentUser = { ...userData };
            this.editMode = true;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }">
        <!-- Edit User Section (Modal-like or Inline) -->
        <div x-show="editMode" class="glass p-10 rounded-[3rem] shadow-xl relative overflow-hidden" data-aos="fade-down" x-cloak>
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-blue-500/5 rounded-full blur-3xl"></div>
            
            <div class="flex justify-between items-center mb-8">
                <h3 class="text-2xl font-black text-slate-900 flex items-center">
                    <span class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fa-solid fa-user-pen"></i>
                    </span>
                    Edit User: <span class="ml-2 theme-text" x-text="currentUser.name"></span>
                </h3>
                <button @click="editMode = false; currentUser = {}" 
                    class="px-6 py-2 bg-slate-100 text-slate-500 font-bold rounded-xl hover:bg-slate-200 transition-all">
                    Cancel
                </button>
            </div>

            <form :action="'{{ route('dashboard.users.index') }}/' + currentUser.id" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @csrf
                @method('PATCH')

                <div class="space-y-2">
                    <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Full Name</label>
                    <input type="text" name="name" required x-model="currentUser.name"
                        class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-medium">
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Email Address</label>
                    <input type="email" name="email" required x-model="currentUser.email"
                        class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-medium">
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Role</label>
                    <select name="role" x-model="currentUser.role"
                        class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-medium">
                        <option value="user">User / Customer</option>
                        <option value="super_admin">Super Admin</option>
                    </select>
                </div>

                <div class="md:col-span-3 flex justify-end">
                    <button type="submit"
                        class="px-12 py-4 bg-slate-900 text-white font-black rounded-2xl hover:bg-black hover:scale-105 shadow-xl transition-all uppercase tracking-[0.2em] text-xs">
                        Update User Information
                    </button>
                </div>
            </form>
        </div>

        <!-- Users List -->
        <div class="glass rounded-[3rem] overflow-hidden shadow-2xl">
            <div class="p-10 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-black text-slate-900">Registered Users</h2>
                    <p class="text-sm text-slate-500 font-medium mt-1">Manage platform access and user roles</p>
                </div>
                <div class="flex items-center space-x-2 bg-white px-4 py-2 rounded-2xl shadow-sm border border-slate-100">
                    <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Total:</span>
                    <span class="text-lg font-black text-blue-600">{{ count($users) }}</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/30">
                            <th class="p-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">User Identity</th>
                            <th class="p-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Role</th>
                            <th class="p-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Orders</th>
                            <th class="p-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Joined Date</th>
                            <th class="p-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($users as $user)
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="p-6">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br {{ $user->isSuperAdmin() ? 'from-blue-600 to-indigo-700' : 'from-slate-200 to-slate-300' }} flex items-center justify-center text-white font-black shadow-lg">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-black text-slate-900 leading-none mb-1">{{ $user->name }}</p>
                                            <p class="text-xs text-slate-500 font-medium">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-6">
                                    <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest {{ $user->isSuperAdmin() ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td class="p-6">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-black">
                                            {{ $user->orders->count() }}
                                        </span>
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Orders</span>
                                    </div>
                                </td>
                                <td class="p-6 text-xs font-bold text-slate-500">
                                    {{ $user->created_at->format('d M Y') }}
                                </td>
                                <td class="p-6 text-right space-x-2">
                                    <button @click="initEdit({{ $user->toJson() }})" class="w-10 h-10 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-xl transition-all flex items-center justify-center">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    @if($user->id !== auth()->id())
                                    <form action="{{ route('dashboard.users.destroy', $user->id) }}" method="POST" class="inline-block">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('Hapus user ini? Semua data terkait (pesanan, dsb) mungkin akan hilang.')" 
                                            class="w-10 h-10 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-xl transition-all flex items-center justify-center">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

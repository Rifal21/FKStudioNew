<x-app-layout>
    <x-slot name="header">
        {{ __('Discount Vouchers') }}
    </x-slot>

    <div class="space-y-10 pb-20" x-data="{
        editMode: false,
        currentVoucher: {
            code: '',
            type: 'percent',
            value: 0,
            max_uses: '',
            expires_at: '',
            is_active: 1
        },
        initEdit(voucherData) {
            this.currentVoucher = { ...voucherData };
            // Format expires_at date to yyyy-MM-dd for standard HTML input
            if (this.currentVoucher.expires_at) {
                this.currentVoucher.expires_at = this.currentVoucher.expires_at.split('T')[0];
            }
            this.editMode = true;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        resetForm() {
            this.editMode = false;
            this.currentVoucher = {
                code: '',
                type: 'percent',
                value: 0,
                max_uses: '',
                expires_at: '',
                is_active: 1
            };
        }
    }">
        
        <!-- Add/Edit Voucher Section -->
        <div class="glass p-10 rounded-[3rem] shadow-xl relative overflow-hidden" data-aos="fade-down">
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-indigo-500/5 rounded-full blur-3xl"></div>
            
            <div class="flex justify-between items-center mb-8">
                <h3 class="text-2xl font-black text-slate-900 flex items-center">
                    <span class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fa-solid" :class="editMode ? 'fa-pen-to-square' : 'fa-plus'"></i>
                    </span>
                    <span x-text="editMode ? 'Edit Discount Voucher' : 'Create New Voucher'"></span>
                </h3>
                <button x-show="editMode" @click="resetForm()" 
                    class="px-6 py-2 bg-slate-100 text-slate-500 font-bold rounded-xl hover:bg-slate-200 transition-all">
                    Cancel Edit
                </button>
            </div>

            <form :action="editMode ? '{{ route('dashboard.vouchers.index') }}/' + currentVoucher.id : '{{ route('dashboard.vouchers.store') }}'" 
                  method="POST" class="space-y-8">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PATCH">
                </template>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- General Details -->
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Voucher Code (e.g. LAUNCHING50)</label>
                            <input type="text" name="code" required x-model="currentVoucher.code" placeholder="FKSTUDIO10"
                                class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold uppercase tracking-wider">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Discount Type</label>
                                <select name="type" required x-model="currentVoucher.type"
                                    class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold">
                                    <option value="percent">Percent (%)</option>
                                    <option value="fixed">Fixed Amount (Rp)</option>
                                </select>
                            </div>
                            
                            <div class="space-y-2">
                                <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Nominal / Value</label>
                                <input type="number" name="value" required x-model="currentVoucher.value" min="0" placeholder="10"
                                    class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold">
                            </div>
                        </div>
                    </div>

                    <!-- Constraints -->
                    <div class="space-y-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Max Uses (Kuota Pakai)</label>
                                <input type="number" name="max_uses" x-model="currentVoucher.max_uses" min="0" placeholder="Tanpa batas"
                                    class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold">
                            </div>
                            
                            <div class="space-y-2">
                                <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Expires At (Masa Berlaku)</label>
                                <input type="date" name="expires_at" x-model="currentVoucher.expires_at"
                                    class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold">
                            </div>
                        </div>

                        <div class="flex items-center space-x-4 h-full pt-6">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" x-model="currentVoucher.is_active" :checked="currentVoucher.is_active" class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                <span class="ml-3 text-sm font-black uppercase tracking-widest text-slate-500 italic">Voucher Status Active</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit"
                        class="px-12 py-5 bg-indigo-600 text-white font-black rounded-[2rem] hover:bg-indigo-700 hover:scale-105 shadow-2xl shadow-indigo-600/20 transition-all uppercase tracking-[0.2em] text-xs">
                        <span x-text="editMode ? 'Update Voucher' : 'Publish Voucher'"></span>
                    </button>
                </div>
            </form>
        </div>

        <!-- List Section -->
        <div class="space-y-6">
            <h3 class="text-xl font-black text-slate-900 flex items-center ml-4">
                <span class="w-8 h-8 bg-slate-100 text-slate-500 rounded-lg flex items-center justify-center mr-3 text-sm">
                    <i class="fa-solid fa-tags"></i>
                </span>
                Active Discount Vouchers
            </h3>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                @foreach ($vouchers as $voucher)
                    <div class="glass p-8 rounded-[3rem] card-hover group relative overflow-hidden flex flex-col {{ $voucher->is_active ? 'ring-2 ring-indigo-500/10' : 'opacity-60' }}" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                        
                        <!-- Voucher Ticket Top Decorator -->
                        <div class="absolute top-0 right-0 p-6 flex items-center space-x-2">
                            @if($voucher->is_active && ($voucher->max_uses === null || $voucher->used_count < $voucher->max_uses))
                                <span class="bg-emerald-500 text-white text-[8px] font-black uppercase tracking-widest px-3 py-1 rounded-full">Ready</span>
                            @else
                                <span class="bg-red-500 text-white text-[8px] font-black uppercase tracking-widest px-3 py-1 rounded-full">Inactive / Exhausted</span>
                            @endif
                        </div>

                        <div class="flex items-start justify-between mb-6">
                            <div>
                                <div class="font-mono text-xl font-black text-indigo-600 tracking-wider bg-indigo-50 border border-indigo-100 rounded-2xl px-4 py-2 inline-block uppercase select-all">
                                    {{ $voucher->code }}
                                </div>
                                <div class="mt-4">
                                    <p class="text-3xl font-black text-slate-900">
                                        @if($voucher->type === 'percent')
                                            {{ $voucher->value }}% <span class="text-sm font-bold text-slate-400">Discount</span>
                                        @else
                                            Rp {{ number_format($voucher->value, 0, ',', '.') }} <span class="text-sm font-bold text-slate-400">Off</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex space-x-2">
                                <button @click="initEdit({{ $voucher->toJson() }})" class="w-10 h-10 bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white rounded-xl transition-all flex items-center justify-center">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </button>
                                <form action="{{ route('dashboard.vouchers.destroy', $voucher->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('Hapus voucher ini?')" 
                                        class="w-10 h-10 bg-slate-50 text-slate-400 hover:bg-red-50 hover:text-red-600 rounded-xl transition-all flex items-center justify-center">
                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="space-y-6 flex-grow">
                            <!-- Usage Progress -->
                            <div class="space-y-2">
                                <div class="flex justify-between text-xs text-slate-500 font-bold uppercase tracking-wider">
                                    <span>Usage / Quota:</span>
                                    <span>
                                        {{ $voucher->used_count }} / {{ $voucher->max_uses ?? '∞' }}
                                    </span>
                                </div>
                                @if($voucher->max_uses)
                                    @php
                                        $percent = min(100, round(($voucher->used_count / $voucher->max_uses) * 100));
                                    @endphp
                                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                        <div class="bg-indigo-600 h-2 rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                                    </div>
                                @else
                                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                        <div class="bg-slate-300 h-2 rounded-full" style="width: 10%"></div>
                                    </div>
                                @endif
                            </div>

                            <!-- Expiration Display -->
                            <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-[10px] font-black uppercase tracking-widest">
                                <div class="flex items-center text-slate-400">
                                    <i class="fa-solid fa-calendar mr-1.5 text-slate-300"></i>
                                    @if($voucher->expires_at)
                                        Exp: {{ \Carbon\Carbon::parse($voucher->expires_at)->format('d M Y') }}
                                    @else
                                        No Expiration
                                    @endif
                                </div>
                                <div class="flex items-center space-x-1">
                                    <div class="w-1.5 h-1.5 {{ $voucher->is_active ? 'bg-green-500' : 'bg-red-500' }} rounded-full"></div>
                                    <span class="{{ $voucher->is_active ? 'text-green-600' : 'text-red-500' }}">
                                        {{ $voucher->is_active ? 'Active' : 'Disabled' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if(count($vouchers) === 0)
                <div class="text-center py-20 glass rounded-[3rem] border-2 border-dashed">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-200 text-4xl">
                        <i class="fa-solid fa-ticket"></i>
                    </div>
                    <p class="text-slate-400 font-bold">No vouchers available</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

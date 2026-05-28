<x-app-layout>
    <x-slot name="header">
        {{ __('Manual Payment Methods') }}
    </x-slot>

    @php
        $paymentMethods = $settings->payment_methods;
        if (empty($paymentMethods) || !is_array($paymentMethods)) {
            $paymentMethods = [['bank' => '', 'number' => '', 'name' => '']];
        }
    @endphp

    <div class="space-y-10 pb-20">
        <!-- Top Info Premium Banner -->
        <div class="glass p-8 rounded-[2.5rem] shadow-xl relative overflow-hidden border border-indigo-100/50" data-aos="fade-down">
            <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-indigo-500/5 rounded-full blur-2xl"></div>
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 relative z-10">
                <div class="flex items-center space-x-5 text-center md:text-left flex-col md:flex-row">
                    <div class="w-14 h-14 bg-gradient-to-tr from-indigo-500 to-purple-650 text-white rounded-2xl flex items-center justify-center text-2xl shadow-lg shadow-indigo-500/20 mb-4 md:mb-0">
                        <i class="fa-solid fa-circle-info"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-black text-slate-900 leading-tight">Pengaturan Pembayaran Manual</h4>
                        <p class="text-xs text-slate-500 mt-1 max-w-xl">
                            Semua rekening bank dan QRIS yang Anda kelola di halaman ini akan otomatis muncul pada formulir checkout klien sebagai instruksi pembayaran transfer resmi.
                        </p>
                    </div>
                </div>
                <div class="flex space-x-3 shrink-0">
                    <span class="px-4 py-2 bg-emerald-50 text-emerald-700 rounded-full font-black text-[9px] uppercase tracking-widest flex items-center border border-emerald-100">
                        <span class="w-1.5 h-1.5 bg-emerald-550 rounded-full mr-2 animate-ping"></span> Live Checkout
                    </span>
                    <span class="px-4 py-2 bg-indigo-50 text-indigo-700 rounded-full font-black text-[9px] uppercase tracking-widest border border-indigo-100">
                        Super Admin Mode
                    </span>
                </div>
            </div>
        </div>

        <!-- Main Form -->
        <form action="{{ route('dashboard.payment_methods.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                <!-- Left Column: QRIS Upload Code (Sleek Glass Card) -->
                <div class="lg:col-span-1 space-y-8" data-aos="fade-right">
                    <div class="glass p-8 md:p-10 rounded-[3rem] shadow-xl relative overflow-hidden flex flex-col items-center text-center">
                        <div class="absolute -right-20 -top-20 w-64 h-64 bg-emerald-500/5 rounded-full blur-3xl"></div>
                        
                        <h3 class="text-lg font-black text-slate-900 mb-2 flex items-center justify-center">
                            <span class="w-8 h-8 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center mr-3 text-sm shadow-sm">
                                <i class="fa-solid fa-qrcode"></i>
                            </span>
                            QRIS Payment Code
                        </h3>
                        <p class="text-[10px] text-slate-450 font-black uppercase tracking-widest mb-8">Instant Scanning Code</p>

                        <!-- Upload Area & Preview -->
                        <div class="p-6 bg-slate-50/50 rounded-3xl border border-slate-200/50 flex flex-col items-center w-full"
                            x-data="{ 
                                invoiceQrisPreview: null,
                                qrisRemoved: false,
                                handleInvoiceQris(e) {
                                    const file = e.target.files[0];
                                    if (file) {
                                        this.invoiceQrisPreview = URL.createObjectURL(file);
                                        this.qrisRemoved = false;
                                    }
                                },
                                handleRemoveQris() {
                                    this.qrisRemoved = true;
                                    this.invoiceQrisPreview = null;
                                }
                            }">
                            
                            <!-- QRIS Frame -->
                            <div class="relative group mb-6">
                                <div class="w-56 h-56 bg-white rounded-2xl shadow-md overflow-hidden flex items-center justify-center border-2 border-dashed border-slate-200 p-3 transition-all duration-300 group-hover:border-emerald-500/50">
                                    <template x-if="invoiceQrisPreview">
                                        <img :src="invoiceQrisPreview" class="w-full h-full object-contain rounded-xl">
                                    </template>
                                    <template x-if="!invoiceQrisPreview">
                                        @if ($settings->invoice_qris)
                                            <div x-show="!qrisRemoved" class="w-full h-full">
                                                <img src="{{ $settings->invoice_qris_url }}" class="w-full h-full object-contain rounded-xl">
                                            </div>
                                            <div x-show="qrisRemoved" class="w-full h-full flex flex-col items-center justify-center text-slate-350">
                                                <i class="fa-solid fa-qrcode text-6xl mb-3"></i>
                                                <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Belum Ada QRIS</span>
                                            </div>
                                        @else
                                            <div class="w-full h-full flex flex-col items-center justify-center text-slate-350">
                                                <i class="fa-solid fa-qrcode text-6xl mb-3"></i>
                                                <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Belum Ada QRIS</span>
                                            </div>
                                        @endif
                                    </template>
                                </div>
                                
                                @if ($settings->invoice_qris)
                                    <button type="button" @click="handleRemoveQris" x-show="!qrisRemoved && !invoiceQrisPreview"
                                        class="absolute -top-3 -right-3 w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center shadow-lg hover:bg-red-650 transition-all z-10 hover:scale-105">
                                        <i class="fa-solid fa-xmark text-xs"></i>
                                    </button>
                                @endif
                                <input type="hidden" name="remove_invoice_qris" :value="qrisRemoved ? '1' : '0'">
                            </div>

                            <!-- Visible file input button trigger -->
                            <label class="w-full cursor-pointer">
                                <span class="w-full py-3.5 bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white font-black text-[10px] uppercase tracking-widest rounded-2xl transition-all shadow-sm flex items-center justify-center space-x-2 border border-emerald-250/30">
                                    <i class="fa-solid fa-cloud-arrow-up text-sm"></i>
                                    <span>Unggah QRIS</span>
                                </span>
                                <input type="file" name="invoice_qris" class="hidden" @change="handleInvoiceQris">
                            </label>

                            <p class="text-[9px] text-slate-400 font-medium text-center mt-3">PNG atau JPG persegi, maks 2MB</p>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Manual Bank Accounts (Premium Glass Card) -->
                <div class="lg:col-span-2 space-y-8" data-aos="fade-left">
                    <div class="glass p-8 md:p-10 rounded-[3rem] shadow-xl relative overflow-hidden"
                        x-data='{ 
                            banks: {{ json_encode($paymentMethods) }},
                            addBank() { this.banks.push({ bank: "", number: "", name: "" }) },
                            removeBank(index) { if(this.banks.length > 1) this.banks.splice(index, 1) }
                        }'>
                        <div class="absolute -right-20 -top-20 w-64 h-64 bg-indigo-500/5 rounded-full blur-3xl"></div>

                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                            <div>
                                <h3 class="text-lg font-black text-slate-900 flex items-center">
                                    <span class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center mr-3 text-sm shadow-sm">
                                        <i class="fa-solid fa-building-columns"></i>
                                    </span>
                                    Daftar Rekening Bank
                                </h3>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Manual Transfer Accounts</p>
                            </div>

                            <button type="button" @click="addBank" 
                                class="px-5 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-[10px] uppercase tracking-widest rounded-2xl transition-all shadow-lg shadow-indigo-600/10 flex items-center space-x-1.5 self-end">
                                <i class="fa-solid fa-plus text-xs"></i> <span>Tambah Rekening</span>
                            </button>
                        </div>

                        <!-- Bank Input List -->
                        <div class="space-y-6">
                            <template x-for="(bank, index) in banks" :key="index">
                                <div class="bg-white p-6 rounded-3xl border border-slate-200/70 shadow-sm hover:shadow-md hover:border-indigo-500/25 transition-all duration-300 relative space-y-6">
                                    
                                    <!-- Bank Card Mock Illustration Header -->
                                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                                        <div class="flex items-center space-x-2.5">
                                            <span class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xs font-black" x-text="index + 1"></span>
                                            <span class="text-xs font-black uppercase tracking-widest text-slate-700">Instruksi Rekening #<span x-text="index + 1"></span></span>
                                        </div>
                                        
                                        <!-- Remove Button inside card header -->
                                        <button type="button" @click="removeBank(index)" :disabled="banks.length <= 1"
                                            class="px-3.5 py-2 bg-slate-50 hover:bg-red-50 text-slate-400 hover:text-red-500 rounded-xl transition-all flex items-center space-x-1.5 text-[9px] font-black uppercase tracking-widest disabled:opacity-40 disabled:cursor-not-allowed">
                                            <i class="fa-solid fa-trash-can text-[10px]"></i>
                                            <span>Hapus</span>
                                        </button>
                                    </div>

                                    <!-- Fields Grid -->
                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                                        <!-- Nama Bank -->
                                        <div class="md:col-span-4 space-y-2 relative">
                                            <label class="text-[10px] font-black uppercase tracking-wider text-slate-450 ml-1">Nama Bank</label>
                                            <div class="relative">
                                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                                    <i class="fa-solid fa-building-columns text-sm"></i>
                                                </span>
                                                <input type="text" :name="'payment_methods[' + index + '][bank]'" x-model="bank.bank" required placeholder="BCA / Mandiri..."
                                                    class="block w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-bold focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all placeholder-slate-400 text-sm shadow-inner">
                                            </div>
                                        </div>

                                        <!-- Nomor Rekening -->
                                        <div class="md:col-span-4 space-y-2 relative">
                                            <label class="text-[10px] font-black uppercase tracking-wider text-slate-450 ml-1">Nomor Rekening</label>
                                            <div class="relative">
                                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                                    <i class="fa-solid fa-credit-card text-sm"></i>
                                                </span>
                                                <input type="text" :name="'payment_methods[' + index + '][number]'" x-model="bank.number" required placeholder="123-456-789..."
                                                    class="block w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-bold focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all placeholder-slate-400 text-sm shadow-inner">
                                            </div>
                                        </div>

                                        <!-- Nama Pemilik Rekening -->
                                        <div class="md:col-span-4 space-y-2 relative">
                                            <label class="text-[10px] font-black uppercase tracking-wider text-slate-450 ml-1">Atas Nama (Holder)</label>
                                            <div class="relative">
                                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                                    <i class="fa-solid fa-user text-sm"></i>
                                                </span>
                                                <input type="text" :name="'payment_methods[' + index + '][name]'" x-model="bank.name" required placeholder="Rifal Kurniawan..."
                                                    class="block w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-bold focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all placeholder-slate-400 text-sm shadow-inner">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Footer Actions -->
                        <div class="flex justify-end pt-8 mt-8 border-t border-slate-100/50">
                            <button type="submit"
                                class="w-full sm:w-auto px-12 py-5 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl hover:scale-102 shadow-lg shadow-indigo-600/20 transition-all uppercase tracking-[0.2em] text-xs">
                                Simpan Metode Pembayaran
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>

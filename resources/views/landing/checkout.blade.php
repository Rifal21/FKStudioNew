<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout - {{ $package->getTranslation('name') }} | FKStudio</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0f172a; color: #f8fafc; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .gradient-text { @apply bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-600; }
    </style>
</head>
<body class="antialiased min-h-screen bg-slate-950" 
    x-data="{ 
        mobileMenu: false, 
        scrolled: false, 
        activeSection: '', 
        agree: false,
        buyDomain: false,
        addedDomainPrice: 0,
        domainName: '',
        packagePrice: {{ (float) str_replace(['Rp', '.', ','], '', $package->price) }},
        paymentScheme: 'full',
        
        // Voucher & Tax State
        voucherCode: '',
        appliedCode: '',
        discountAmount: 0,
        voucherSuccess: '',
        voucherError: '',
        isCheckingVoucher: false,
        taxRate: {{ $settings->tax_rate ?? 11.00 }},
        selectedBusinessType: '{{ old('business_type') }}',

        subtotal() {
            return this.packagePrice + (this.buyDomain ? this.addedDomainPrice : 0);
        },
        discount() {
            return this.discountAmount;
        },
        taxAmount() {
            let taxable = Math.max(0, this.subtotal() - this.discount());
            return Math.round(taxable * (this.taxRate / 100));
        },
        grandTotal() {
            return this.subtotal() - this.discount() + this.taxAmount();
        },
        totalPrice() { 
            return this.paymentScheme === 'dp' ? this.grandTotal() * 0.5 : this.grandTotal();
        },
        remainingBalance() {
            return this.grandTotal() - this.totalPrice();
        },
        
        async applyVoucher() {
            if (!this.voucherCode.trim()) {
                this.voucherError = 'Silakan masukkan kode voucher.';
                this.voucherSuccess = '';
                return;
            }
            this.isCheckingVoucher = true;
            this.voucherError = '';
            this.voucherSuccess = '';

            try {
                let response = await fetch('{{ route('checkout.apply_voucher') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        voucher_code: this.voucherCode,
                        package_id: '{{ $package->id }}',
                        buy_domain: this.buyDomain ? '1' : '0',
                        domain_name: this.domainName
                    })
                });
                let result = await response.json();
                if (result.success) {
                    this.discountAmount = result.discount_amount;
                    this.appliedCode = result.code;
                    this.voucherSuccess = result.message;
                    this.voucherError = '';
                } else {
                    this.voucherError = result.message;
                    this.voucherSuccess = '';
                    this.discountAmount = 0;
                    this.appliedCode = '';
                }
            } catch (err) {
                this.voucherError = 'Gagal menghubungi server.';
            } finally {
                this.isCheckingVoucher = false;
            }
        },

        removeVoucher() {
            this.discountAmount = 0;
            this.appliedCode = '';
            this.voucherCode = '';
            this.voucherSuccess = '';
            this.voucherError = '';
        },

        formatCurrency(val) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(val); }
    }" 
    @scroll.window="scrolled = (window.pageYOffset > 50)">
    <div class="fixed top-0 left-0 w-full h-full pointer-events-none overflow-hidden -z-10">
        <div class="absolute top-1/4 -left-20 w-96 h-96 bg-blue-600/10 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-1/4 -right-20 w-96 h-96 bg-indigo-700/10 rounded-full blur-[120px]"></div>
    </div>

    @include('landing.sections.navbar')

    <div class="container mx-auto px-6 pt-32 pb-12 md:pb-24 relative z-10">
        <div class="max-w-6xl mx-auto">
            <div class="mb-12">
                <a href="{{ route('home') }}" class="inline-flex items-center text-slate-400 hover:text-white transition-colors mb-8 group">
                    <i class="fa-solid fa-arrow-left mr-3 group-hover:-translate-x-1 transition-transform"></i>
                    Kembali ke Beranda
                </a>
                <h1 class="text-4xl md:text-6xl font-black text-white tracking-tighter">
                    Selesaikan <span class="gradient-text">Pesanan Anda.</span>
                </h1>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- Order Summary -->
                <div class="lg:col-span-1 space-y-8">
                    <div class="glass p-8 rounded-[2.5rem] border-blue-500/20 relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-6 opacity-10">
                            <i class="fa-solid fa-cube text-6xl"></i>
                        </div>
                        <h3 class="text-xs font-black uppercase tracking-[0.3em] text-blue-500 mb-6">Detail Pesanan</h3>
                        <h2 class="text-2xl font-black text-white mb-2">{{ $package->getTranslation('name') }}</h2>
                        <div class="text-2xl font-black text-slate-400 tracking-tighter mb-4">{{ $package->price }}</div>
                        
                        <!-- Skema Pembayaran (Sleek Compact Selectors inside Detail Pesanan) -->
                        <div class="border-t border-white/5 pt-6 mt-6">
                            <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-500 mb-3"><i class="fa-solid fa-chart-pie mr-1"></i> Skema Pembayaran</h4>
                            <div class="grid grid-cols-2 gap-3">
                                <!-- Option Full -->
                                <button type="button" @click="paymentScheme = 'full'"
                                    class="p-3 rounded-xl border text-left transition-all relative overflow-hidden select-none"
                                    :class="paymentScheme === 'full' ? 'border-blue-500 bg-blue-500/10' : 'border-white/5 bg-white/[0.02] hover:bg-white/[0.04]'">
                                    <div class="font-bold text-xs text-white">Lunas (100%)</div>
                                    <div class="text-[8px] text-slate-400 mt-1 leading-tight">Bayar penuh langsung di awal.</div>
                                </button>
                                
                                <!-- Option DP -->
                                <button type="button" @click="paymentScheme = 'dp'"
                                    class="p-3 rounded-xl border text-left transition-all relative overflow-hidden select-none"
                                    :class="paymentScheme === 'dp' ? 'border-blue-500 bg-blue-500/10' : 'border-white/5 bg-white/[0.02] hover:bg-white/[0.04]'">
                                    <div class="font-bold text-xs text-white">Bertahap (DP 50%)</div>
                                    <div class="text-[8px] text-slate-400 mt-1 leading-tight">Mulai dengan DP 50% hari ini.</div>
                                </button>
                            </div>
                        </div>

                        <!-- Voucher Diskon (Sleek Compact Input inside Detail Pesanan) -->
                        <div class="border-t border-white/5 pt-6 mt-6">
                            <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-emerald-500 mb-3"><i class="fa-solid fa-ticket mr-1"></i> Voucher Diskon</h4>
                            
                            <div class="flex space-x-2">
                                <div class="relative flex-grow">
                                    <input type="text" x-model="voucherCode" placeholder="Kode Voucher" 
                                        class="block w-full px-3 py-2 text-xs bg-white/5 border border-white/10 rounded-xl text-white font-medium focus:ring-2 focus:ring-blue-500 transition-all uppercase"
                                        :disabled="appliedCode !== ''">
                                    <div class="absolute right-2 top-1/2 -translate-y-1/2" x-show="appliedCode !== ''">
                                        <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
                                    </div>
                                </div>
                                <button type="button" @click="applyVoucher" x-show="appliedCode === ''"
                                    class="px-3 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-xl transition-all select-none"
                                    :disabled="isCheckingVoucher">
                                    <span x-show="!isCheckingVoucher">Apply</span>
                                    <span x-show="isCheckingVoucher" class="flex items-center"><i class="fa-solid fa-spinner animate-spin"></i></span>
                                </button>
                                <button type="button" @click="removeVoucher" x-show="appliedCode !== ''"
                                    class="px-3 py-2 bg-red-500/10 hover:bg-red-500/20 text-red-400 font-bold text-xs rounded-xl transition-all border border-red-500/20 select-none">
                                    Batal
                                </button>
                            </div>
                            <!-- Voucher Feedback Messages -->
                            <div x-show="voucherError" x-cloak class="mt-2 text-[10px] text-red-400 flex items-center space-x-1">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                <span x-text="voucherError"></span>
                            </div>
                            <div x-show="voucherSuccess" x-cloak class="mt-2 text-[10px] text-emerald-400 flex items-center space-x-1">
                                <i class="fa-solid fa-circle-check"></i>
                                <span x-text="voucherSuccess"></span>
                            </div>
                        </div>

                        <!-- Detailed Pricing Items -->
                        <div class="border-t border-white/5 pt-6 mt-6 space-y-2">
                            <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 mb-3"><i class="fa-solid fa-receipt mr-1"></i> Rincian Harga</h4>
                            <div class="flex justify-between text-xs text-slate-400">
                                <span>Subtotal Paket:</span>
                                <span class="font-bold text-white" x-text="formatCurrency(packagePrice)"></span>
                            </div>
                            <div x-show="buyDomain" x-cloak class="flex justify-between text-xs text-slate-400">
                                <span>Domain Add-on:</span>
                                <span class="font-bold text-white" x-text="formatCurrency(addedDomainPrice)"></span>
                            </div>
                            <div x-show="discount() > 0" x-cloak class="flex justify-between text-xs text-emerald-400">
                                <span>Potongan Voucher (<span class="font-mono text-[10px]" x-text="appliedCode"></span>):</span>
                                <span class="font-bold" x-text="'- ' + formatCurrency(discount())"></span>
                            </div>
                            <div class="flex justify-between text-xs text-slate-400">
                                <span>PPN (<span x-text="taxRate"></span>%):</span>
                                <span class="font-bold text-white" x-text="formatCurrency(taxAmount())"></span>
                            </div>
                        </div>

                        <!-- Skema Pembayaran preview in summary -->
                        <div class="border-t border-white/5 pt-4 mt-4 space-y-2">
                            <div class="flex justify-between text-xs text-slate-400">
                                <span>Metode Skema:</span>
                                <span class="font-bold text-white uppercase tracking-wider text-[10px]" x-text="paymentScheme === 'dp' ? 'Bertahap / DP (50%)' : 'Bayar Lunas (100%)'"></span>
                            </div>
                            <div x-show="paymentScheme === 'dp'" x-cloak class="flex justify-between text-xs text-slate-400">
                                <span>Sisa Pelunasan (50%):</span>
                                <span class="font-bold text-indigo-400" x-text="formatCurrency(remainingBalance())"></span>
                            </div>
                        </div>

                        <div class="border-t border-white/5 pt-6 mt-6">
                            <div class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-500 mb-2" x-text="paymentScheme === 'dp' ? 'Down Payment (DP) Hari Ini' : 'Total Pembayaran Hari Ini'"></div>
                            <div class="text-4xl font-black text-white tracking-tighter" x-text="formatCurrency(totalPrice())"></div>
                        </div>
                    </div>

                    <div class="glass p-8 rounded-[2.5rem] bg-blue-600/5">
                        <h4 class="font-bold text-white mb-2">Butuh Bantuan?</h4>
                        <p class="text-sm text-slate-400 leading-relaxed mb-4">
                            Jika Anda memiliki pertanyaan sebelum melakukan pembayaran, jangan ragu untuk menghubungi kami.
                        </p>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings->contact_phone) }}" target="_blank" class="text-sm font-black text-blue-400 hover:text-blue-300 transition-colors">
                            WhatsApp Support <i class="fa-solid fa-arrow-up-right-from-square ml-1 text-[10px]"></i>
                        </a>
                    </div>
                </div>

                <!-- Checkout Form -->
                <div class="lg:col-span-2">
                    <form action="{{ route('checkout.process', $package->id) }}" method="POST" class="space-y-8">
                        @csrf
                        <div class="glass p-8 md:p-12 rounded-[3rem] shadow-2xl">
                            @if(session('error'))
                                <div class="mb-8 p-6 bg-red-500/10 border border-red-500/20 rounded-2xl flex items-start space-x-4">
                                    <div class="w-10 h-10 bg-red-500/20 text-red-500 rounded-xl flex items-center justify-center shrink-0">
                                        <i class="fa-solid fa-circle-exclamation"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-red-500">Gagal Memproses Pembayaran</h4>
                                        <p class="text-sm text-red-400/80 mt-1">{{ session('error') }}</p>
                                    </div>
                                </div>
                            @endif
                            <!-- Hidden Inputs for Merged Summary Sidebar Elements -->
                            <input type="hidden" name="payment_scheme" :value="paymentScheme">
                            <input type="hidden" name="voucher_code" :value="appliedCode">

                            <div class="mb-12">
                                <h3 class="text-xl font-black text-white flex items-center">
                                    <span class="w-10 h-10 bg-blue-600/20 text-blue-500 rounded-xl flex items-center justify-center mr-4">
                                        <i class="fa-solid fa-globe"></i>
                                    </span>
                                    Informasi Website & Booking
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Nama Lengkap</label>
                                        <div class="px-6 py-4 bg-white/5 border border-white/10 rounded-2xl text-white font-medium">
                                            {{ Auth::user()->name }}
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Email</label>
                                        <div class="px-6 py-4 bg-white/5 border border-white/10 rounded-2xl text-white font-medium">
                                            {{ Auth::user()->email }}
                                        </div>
                                    </div>

                                    {{-- Nama Website --}}
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Nama Website / Brand <span class="text-red-500">*</span></label>
                                        <input type="text" name="website_name" required placeholder="Contoh: Toko Kopi Senja" value="{{ old('website_name') }}"
                                            class="block w-full px-6 py-4 bg-white/5 border {{ $errors->has('website_name') ? 'border-red-500' : 'border-white/10' }} rounded-2xl text-white font-medium focus:ring-2 focus:ring-blue-500 transition-all placeholder-slate-600">
                                        @error('website_name')
                                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- URL Website --}}
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Subdomain yang Diinginkan <span class="text-blue-400 font-bold">(Gratis .fkstudio.id)</span></label>
                                        <div class="flex bg-white/5 border {{ $errors->has('website_url') ? 'border-red-500' : 'border-white/10' }} rounded-2xl overflow-hidden focus-within:ring-2 focus-within:ring-blue-500 transition-all">
                                            <input type="text" name="website_url" placeholder="brandkamu" value="{{ old('website_url') }}"
                                                oninput="this.value = this.value.toLowerCase().replace(/[\s\.]+/g, '-').replace(/[^a-z0-9\-]/g, '').replace(/-+/g, '-')"
                                                onblur="this.value = this.value.replace(/^-+|-+$/g, '')"
                                                class="flex-grow w-full min-w-0 bg-transparent px-6 py-4 text-white font-medium placeholder-slate-600 border-none outline-none focus:ring-0">
                                            <span class="bg-white/10 px-6 py-4 text-slate-400 font-black flex items-center border-l border-white/5 select-none shrink-0">
                                                .fkstudio.id
                                            </span>
                                        </div>
                                        @error('website_url')
                                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Jenis Bisnis --}}
                                    <div class="space-y-2 md:col-span-2">
                                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Jenis Bisnis / Website <span class="text-red-500">*</span></label>
                                        <select name="business_type" required x-model="selectedBusinessType"
                                            class="block w-full px-6 py-4 bg-white/5 border {{ $errors->has('business_type') ? 'border-red-500' : 'border-white/10' }} rounded-2xl text-white font-medium focus:ring-2 focus:ring-blue-500 transition-all">
                                            <option value="" class="bg-slate-900">-- Pilih Jenis --</option>
                                            <option value="Company Profile" class="bg-slate-900">Company Profile</option>
                                            <option value="Toko Online / E-Commerce" class="bg-slate-900">Toko Online / E-Commerce</option>
                                            <option value="Portofolio" class="bg-slate-900">Portofolio</option>
                                            <option value="Blog / Artikel" class="bg-slate-900">Blog / Artikel</option>
                                            <option value="Landing Page" class="bg-slate-900">Landing Page</option>
                                            <option value="Aplikasi Web" class="bg-slate-900">Aplikasi Web</option>
                                            <option value="Lainnya" class="bg-slate-900">Lainnya</option>
                                        </select>
                                        @error('business_type')
                                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                        @enderror

                                        <!-- Custom Business Type input (shown dynamically via Alpine JS) -->
                                        <div x-show="selectedBusinessType === 'Lainnya'" x-cloak x-transition class="mt-4 space-y-2">
                                            <label class="text-[10px] font-black uppercase tracking-widest text-blue-400 ml-1">Jenis Website Custom Anda <span class="text-red-500">*</span></label>
                                            <input type="text" name="custom_business_type" :required="selectedBusinessType === 'Lainnya'" placeholder="Misal: Portal Berita, Web Undangan Nikah, Platform Kursus..." value="{{ old('custom_business_type') }}"
                                                class="block w-full px-6 py-4 bg-white/5 border border-white/10 rounded-2xl text-white font-medium focus:ring-2 focus:ring-blue-500 transition-all">
                                            @error('custom_business_type')
                                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Brief / Catatan --}}
                                    <div class="space-y-2 md:col-span-2">
                                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Brief / Catatan Tambahan <span class="text-slate-600">(opsional)</span></label>
                                        <textarea name="client_notes" rows="4" placeholder="Ceritakan kebutuhan atau referensi desain yang Anda inginkan..."
                                            class="block w-full px-6 py-4 bg-white/5 border {{ $errors->has('client_notes') ? 'border-red-500' : 'border-white/10' }} rounded-2xl text-white font-medium focus:ring-2 focus:ring-blue-500 transition-all placeholder-slate-600 resize-none">{{ old('client_notes') }}</textarea>
                                        @error('client_notes')
                                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            @if(false)
                            <!-- Modul Domain Reseller IDCloudHost (ON HOLD) -->
                            <div class="mb-12" x-data="{
                                domainQuery: '',
                                extension: '.com',
                                checking: false,
                                checkedResult: null,
                                addedDomain: null,
                                addedPrice: 0,
                                checkDomain() {
                                    if (!this.domainQuery) {
                                        alert('Silakan masukkan nama domain terlebih dahulu.');
                                        return;
                                    }
                                    this.checking = true;
                                    this.checkedResult = null;
                                    
                                    let fullDomain = this.domainQuery.trim().toLowerCase();
                                    if (!fullDomain.endsWith(this.extension)) {
                                        fullDomain += this.extension;
                                    }
                                    
                                    fetch(`/api/domains/check?domain=${fullDomain}`)
                                        .then(res => res.json())
                                        .then(data => {
                                            this.checking = false;
                                            this.checkedResult = {
                                                domain: fullDomain,
                                                status: data.status,
                                                price: data.price,
                                                message: data.message
                                            };
                                        })
                                        .catch(err => {
                                            this.checking = false;
                                            console.error(err);
                                            alert('Gagal mengecek ketersediaan domain.');
                                        });
                                },
                                addDomain() {
                                    this.addedDomain = this.checkedResult.domain;
                                    this.addedPrice = this.checkedResult.price;
                                    $data.buyDomain = true;
                                    $data.addedDomainPrice = this.checkedResult.price;
                                    $data.domainName = this.checkedResult.domain;
                                },
                                removeDomain() {
                                    this.addedDomain = null;
                                    this.addedPrice = 0;
                                    this.checkedResult = null;
                                    $data.buyDomain = false;
                                    $data.addedDomainPrice = 0;
                                    $data.domainName = '';
                                }
                            }">
                                <h3 class="text-xl font-black text-white flex items-center mb-2">
                                    <span class="w-10 h-10 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl flex items-center justify-center mr-4 shadow-lg shadow-blue-500/20">
                                        <i class="fa-solid fa-server"></i>
                                    </span>
                                    Beli Domain Baru
                                </h3>
                                <p class="text-slate-400 text-sm mb-6 ml-14">Amankan alamat website Anda sekarang sebelum orang lain memilikinya!</p>
                                
                                <div class="ml-14 bg-white/[0.02] border border-white/5 rounded-3xl p-6 md:p-8 space-y-6">
                                    <div class="flex items-center space-x-4">
                                        <input type="checkbox" id="trigger_buy_domain" name="buy_domain" value="1" x-model="$data.buyDomain" class="h-5 w-5 rounded border-white/10 bg-white/5 text-blue-600 focus:ring-blue-500" @change="if(!$data.buyDomain) { removeDomain(); }">
                                        <label for="trigger_buy_domain" class="text-sm font-bold text-white cursor-pointer select-none">
                                            Ya, saya ingin membeli domain baru sekalian untuk website saya
                                        </label>
                                    </div>
                                    
                                    <!-- Domain Search Box -->
                                    <div x-show="$data.buyDomain" x-transition x-cloak class="space-y-6 pt-4 border-t border-white/5">
                                        <div class="flex flex-col md:flex-row gap-3">
                                            <div class="flex-grow flex bg-white/5 border border-white/10 rounded-2xl overflow-hidden focus-within:ring-2 focus-within:ring-blue-500 transition-all">
                                                <input type="text" x-model="domainQuery" placeholder="contoh: brandkamu" @keydown.enter.prevent="checkDomain()"
                                                    class="flex-grow bg-transparent px-6 py-4 text-white font-medium placeholder-slate-600 border-none outline-none focus:ring-0">
                                                <select x-model="extension" class="bg-slate-900 px-4 py-4 text-white font-bold border-none outline-none focus:ring-0">
                                                    <option value=".com">.com (Rp 165.000/th)</option>
                                                    <option value=".id">.id (Rp 245.000/th)</option>
                                                    <option value=".my.id">.my.id (Rp 35.000/th)</option>
                                                    <option value=".net">.net (Rp 185.000/th)</option>
                                                </select>
                                            </div>
                                            <button type="button" @click="checkDomain()" :disabled="checking"
                                                class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-black uppercase tracking-wider text-xs rounded-2xl flex items-center justify-center space-x-2 transition-all">
                                                <template x-if="checking">
                                                    <i class="fa-solid fa-spinner animate-spin"></i>
                                                </template>
                                                <template x-if="!checking">
                                                    <i class="fa-solid fa-magnifying-glass"></i>
                                                </template>
                                                <span x-text="checking ? 'Mengecek...' : 'Cari Domain'"></span>
                                            </button>
                                        </div>
                                        
                                        <!-- Hasil Pengecekan Domain -->
                                        <div x-show="checkedResult" x-transition x-cloak>
                                            <!-- Jika tersedia -->
                                            <div x-show="checkedResult && checkedResult.status === 'available'" class="p-5 bg-green-500/10 border border-green-500/20 rounded-2xl flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-8 h-8 bg-green-500/20 text-green-400 rounded-lg flex items-center justify-center shrink-0">
                                                        <i class="fa-solid fa-circle-check"></i>
                                                    </div>
                                                    <div>
                                                        <span class="font-black text-white" x-text="checkedResult ? checkedResult.domain : ''"></span>
                                                        <span class="text-green-400 text-xs font-bold uppercase tracking-wider ml-2 bg-green-500/10 px-2 py-0.5 rounded">Tersedia</span>
                                                    </div>
                                                </div>
                                                <div class="flex items-center justify-between md:justify-end gap-4 shrink-0">
                                                    <span class="text-lg font-black text-white" x-text="checkedResult ? formatCurrency(checkedResult.price) + ' / th' : ''"></span>
                                                    <button type="button" @click="addDomain()" x-show="addedDomain !== checkedResult.domain"
                                                        class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all">
                                                        Tambahkan
                                                    </button>
                                                    <span x-show="addedDomain === checkedResult.domain" class="text-green-400 text-xs font-bold uppercase tracking-wider flex items-center">
                                                        <i class="fa-solid fa-check-double mr-2"></i> Ditambahkan
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <!-- Jika sudah terdaftar -->
                                            <div x-show="checkedResult && checkedResult.status === 'registered'" class="p-5 bg-red-500/10 border border-red-500/20 rounded-2xl flex items-center space-x-3">
                                                <div class="w-8 h-8 bg-red-500/20 text-red-400 rounded-lg flex items-center justify-center shrink-0">
                                                    <i class="fa-solid fa-circle-xmark"></i>
                                                </div>
                                                <div>
                                                    <span class="font-black text-white" x-text="checkedResult ? checkedResult.domain : ''"></span>
                                                    <span class="text-red-400 text-xs font-bold uppercase tracking-wider ml-2 bg-red-500/10 px-2 py-0.5 rounded">Terdaftar</span>
                                                    <p class="text-xs text-slate-500 mt-1">Domain ini sudah dimiliki orang lain. Silakan cari nama domain yang berbeda.</p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Preview Domain Ditambahkan -->
                                        <div x-show="addedDomain" x-transition x-cloak class="p-5 bg-blue-600/10 border border-blue-500/20 rounded-2xl flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-8 h-8 bg-blue-600/20 text-blue-400 rounded-lg flex items-center justify-center">
                                                    <i class="fa-solid fa-cart-shopping"></i>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Domain Ditambahkan</p>
                                                    <p class="font-black text-white text-sm" x-text="addedDomain"></p>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-4">
                                                <span class="text-sm font-black text-white" x-text="formatCurrency(addedPrice)"></span>
                                                <button type="button" @click="removeDomain()" class="text-slate-500 hover:text-red-500 transition-colors">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Hidden Input for Domain Name -->
                                        <input type="hidden" name="domain_name" :value="$data.domainName">
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="mb-12">
                                <h3 class="text-xl font-black text-white flex items-center mb-2">
                                    <span class="w-10 h-10 bg-indigo-600/20 text-indigo-500 rounded-xl flex items-center justify-center mr-4">
                                        <i class="fa-solid fa-credit-card"></i>
                                    </span>
                                    Metode Pembayaran
                                </h3>
                                <p class="text-slate-400 text-sm mb-8 ml-14">Pilih metode pembayaran yang paling memudahkan Anda.</p>
                                
                                <div class="space-y-10">
                                    <!-- Manual Transfer Section -->
                                    <div class="space-y-4">
                                        <div class="flex items-center space-x-3 px-2">
                                            <div class="h-px flex-grow bg-white/5"></div>
                                            <h4 class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-500 whitespace-nowrap">Transfer Manual</h4>
                                            <div class="h-px flex-grow bg-white/5"></div>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            @foreach($settings->payment_methods ?? [] as $index => $bank)
                                                <label class="relative group cursor-pointer block">
                                                    <input type="radio" name="payment_method" value="{{ $bank['bank'] }} - {{ $bank['number'] }}" class="peer hidden" {{ $index == 0 ? 'checked' : '' }}>
                                                    <div class="h-full p-5 bg-white/5 border border-white/10 rounded-2xl transition-all duration-300 peer-checked:border-blue-500 peer-checked:bg-blue-500/10 group-hover:bg-white/10 relative overflow-hidden">
                                                        <div class="absolute top-0 right-0 p-2 opacity-0 peer-checked:opacity-100 transition-opacity">
                                                            <i class="fa-solid fa-circle-check text-blue-500 text-sm"></i>
                                                        </div>
                                                        <div class="flex items-center space-x-4">
                                                            <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center text-xl text-white group-hover:scale-110 transition-transform">
                                                                <i class="fa-solid fa-building-columns"></i>
                                                            </div>
                                                            <div>
                                                                <div class="font-black text-white uppercase tracking-wider text-sm">{{ $bank['bank'] }}</div>
                                                                <div class="text-[10px] text-slate-500">{{ $bank['name'] }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </label>
                                            @endforeach
                                            
                                            @if($settings->invoice_qris)
                                                 <label class="relative group cursor-pointer block">
                                                     <input type="radio" name="payment_method" value="QRIS" class="peer hidden">
                                                     <div class="h-full p-5 bg-white/5 border border-white/10 rounded-2xl transition-all duration-300 peer-checked:border-blue-500 peer-checked:bg-blue-500/10 group-hover:bg-white/10 relative overflow-hidden">
                                                         <div class="absolute top-0 right-0 p-2 opacity-0 peer-checked:opacity-100 transition-opacity">
                                                             <i class="fa-solid fa-circle-check text-blue-500 text-sm"></i>
                                                         </div>
                                                         <div class="flex items-center space-x-4">
                                                             <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center text-xl text-white group-hover:scale-110 transition-transform">
                                                                 <i class="fa-solid fa-qrcode"></i>
                                                             </div>
                                                             <div>
                                                                 <div class="font-black text-white uppercase tracking-wider text-sm">QRIS</div>
                                                                 <div class="text-[10px] text-slate-500">Konfirmasi Manual</div>
                                                             </div>
                                                         </div>
                                                     </div>
                                                 </label>
                                             @endif
                                        </div>
                                    </div>

                                    <!-- Automated Payment Section -->
                                    <div class="space-y-4">
                                        <div class="flex items-center space-x-3 px-2">
                                            <div class="h-px flex-grow bg-white/5"></div>
                                            <h4 class="text-[10px] font-black uppercase tracking-[0.3em] text-blue-500 whitespace-nowrap">Pembayaran Otomatis</h4>
                                            <div class="h-px flex-grow bg-white/5"></div>
                                        </div>
                                        
                                        @if(count($duitkuMethods) > 0)
                                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                                @foreach($duitkuMethods as $method)
                                                    <label class="relative group cursor-pointer block">
                                                        <input type="radio" name="payment_method" value="Duitku|{{ $method['paymentMethod'] }}" class="peer hidden">
                                                        <div class="h-full p-4 bg-white/5 border border-white/10 rounded-2xl transition-all duration-300 peer-checked:border-blue-500 peer-checked:bg-blue-500/10 group-hover:bg-white/10 relative overflow-hidden">
                                                            <div class="absolute top-0 right-0 p-2 opacity-0 peer-checked:opacity-100 transition-opacity">
                                                                <i class="fa-solid fa-circle-check text-blue-500 text-sm"></i>
                                                            </div>
                                                            <div class="flex flex-col items-center text-center space-y-3">
                                                                <div class="w-full h-10 bg-white/10 rounded-lg overflow-hidden flex items-center justify-center p-2 group-hover:scale-105 transition-transform">
                                                                    <img src="{{ $method['paymentImage'] }}" alt="{{ $method['paymentName'] }}" class="max-w-full max-h-full object-contain filter brightness-110">
                                                                </div>
                                                                <div class="text-[10px] font-bold text-white leading-tight line-clamp-1 uppercase tracking-tighter">{{ $method['paymentName'] }}</div>
                                                            </div>
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @else
                                            <label class="relative group cursor-pointer block">
                                                <input type="radio" name="payment_method" value="Duitku" class="peer hidden">
                                                <div class="p-6 bg-white/5 border border-white/10 rounded-2xl transition-all duration-300 peer-checked:border-blue-500 peer-checked:bg-blue-500/10 group-hover:bg-white/10 relative overflow-hidden">
                                                    <div class="absolute top-0 right-0 p-3 opacity-0 peer-checked:opacity-100 transition-opacity">
                                                        <i class="fa-solid fa-circle-check text-blue-500 text-xl"></i>
                                                    </div>
                                                    <div class="flex items-center space-x-4">
                                                        <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center text-2xl text-white group-hover:scale-110 transition-transform">
                                                            <i class="fa-solid fa-wallet"></i>
                                                        </div>
                                                        <div>
                                                            <div class="font-black text-white uppercase tracking-widest">Duitku (Otomatis)</div>
                                                            <div class="text-xs text-slate-400">E-Wallet, VA, & Retail Outlets</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        @endif
                                    </div>
                                </div>
                            </div>

                             <div class="bg-white/5 border border-white/10 rounded-[2rem] p-6 mb-8">
                                <div class="flex items-start space-x-4">
                                   <div class="relative flex items-center">
                                       <input type="checkbox" id="agree" x-model="agree" class="peer h-6 w-6 rounded-lg border-2 border-white/10 bg-white/5 text-blue-600 focus:ring-blue-500 focus:ring-offset-slate-950 transition-all cursor-pointer checked:border-blue-500">
                                       <i class="fa-solid fa-check absolute inset-0 m-auto text-[10px] text-white opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"></i>
                                   </div>
                                   <label for="agree" class="text-sm text-slate-400 leading-relaxed cursor-pointer select-none group">
                                       Saya telah membaca dan menyetujui <a href="#" class="text-blue-400 group-hover:text-blue-300 underline font-bold transition-colors">Syarat & Ketentuan</a> serta <a href="#" class="text-blue-400 group-hover:text-blue-300 underline font-bold transition-colors">Kebijakan Privasi</a> FKStudio.
                                   </label>
                                </div>
                             </div>

                            <button type="submit" 
                                :disabled="!agree"
                                :class="agree ? 'bg-blue-600 hover:bg-blue-700 hover:scale-[1.02]' : 'bg-slate-800 text-slate-500 cursor-not-allowed'"
                                class="w-full py-6 text-white rounded-2xl font-black uppercase tracking-[0.2em] shadow-2xl transition-all">
                                Konfirmasi & Pesan Sekarang
                            </button>
                            
                            <p class="text-center text-[10px] text-slate-500 mt-6 uppercase tracking-widest leading-relaxed">
                                Dengan menekan tombol di atas, Anda menyetujui syarat & ketentuan layanan FKStudio.
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<x-app-layout>
    <x-slot name="header">
        {{ __('Site Settings') }}
    </x-slot>

    <div class="max-w-5xl mx-auto">
        <form action="{{ route('dashboard.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8 pb-20">
            @csrf
            
            <!-- General Branding -->
            <div class="glass p-8 rounded-[3rem] shadow-xl relative overflow-hidden" data-aos="fade-up">
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-blue-500/5 rounded-full blur-3xl"></div>
                <h3 class="text-xl font-black text-slate-900 mb-8 flex items-center">
                    <span class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fa-solid fa-brand"></i>
                    </span>
                    Branding & SEO
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Site Name</label>
                        <input type="text" name="site_name" value="{{ $settings->site_name }}"
                            class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-medium">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">SEO Meta Title</label>
                        <input type="text" name="seo_title" value="{{ $settings->seo_title }}"
                            class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-medium">
                    </div>
                    <div class="md:col-span-2 space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">SEO Description</label>
                        <textarea name="seo_description" rows="2" 
                            class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-medium">{{ $settings->seo_description }}</textarea>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">SEO Keywords</label>
                        <input type="text" name="seo_keywords" value="{{ $settings->seo_keywords }}" placeholder="creative, agency, portfolio, modern"
                            class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-medium">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Google Console Verification</label>
                        <input type="text" name="google_console_verification" value="{{ $settings->google_console_verification }}" placeholder="google-site-verification-code"
                            class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-medium">
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 md:col-span-2 pt-4" 
                        x-data="{ 
                            logoPreview: null,
                            faviconPreview: null,
                            ogPreview: null,
                            handleLogo(e) {
                                const file = e.target.files[0];
                                if (file) this.logoPreview = URL.createObjectURL(file);
                            },
                            handleFavicon(e) {
                                const file = e.target.files[0];
                                if (file) this.faviconPreview = URL.createObjectURL(file);
                            },
                            handleOg(e) {
                                const file = e.target.files[0];
                                if (file) this.ogPreview = URL.createObjectURL(file);
                            }
                        }">
                        <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 flex items-center space-x-4">
                            <div class="relative group">
                                <div class="w-16 h-16 bg-white rounded-xl shadow-sm overflow-hidden flex items-center justify-center border border-slate-200">
                                    <template x-if="logoPreview">
                                        <img :src="logoPreview" class="w-full h-full object-contain p-1">
                                    </template>
                                    <template x-if="!logoPreview">
                                        @if ($settings->site_logo)
                                            <img src="{{ $settings->logo_url }}" class="w-full h-full object-contain p-1">
                                        @else
                                            <i class="fa-solid fa-image text-slate-300 text-2xl"></i>
                                        @endif
                                    </template>
                                </div>
                                <label class="absolute inset-0 flex items-center justify-center bg-black/40 text-white rounded-xl opacity-0 group-hover:opacity-100 cursor-pointer transition-opacity">
                                    <i class="fa-solid fa-camera"></i>
                                    <input type="file" name="site_logo" class="hidden" @change="handleLogo">
                                </label>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900">Site Logo</h4>
                                <p class="text-xs text-slate-500">PNG/SVG recommended</p>
                            </div>
                        </div>

                        <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 flex items-center space-x-4">
                            <div class="relative group">
                                <div class="w-16 h-16 bg-white rounded-xl shadow-sm overflow-hidden flex items-center justify-center border border-slate-200">
                                    <template x-if="faviconPreview">
                                        <img :src="faviconPreview" class="w-full h-full object-contain p-3">
                                    </template>
                                    <template x-if="!faviconPreview">
                                        @if ($settings->site_favicon)
                                            <img src="{{ $settings->favicon_url }}" class="w-full h-full object-contain p-3">
                                        @else
                                            <i class="fa-solid fa-icons text-slate-300 text-2xl"></i>
                                        @endif
                                    </template>
                                </div>
                                <label class="absolute inset-0 flex items-center justify-center bg-black/40 text-white rounded-xl opacity-0 group-hover:opacity-100 cursor-pointer transition-opacity">
                                    <i class="fa-solid fa-camera"></i>
                                    <input type="file" name="site_favicon" class="hidden" @change="handleFavicon">
                                </label>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900">Favicon</h4>
                                <p class="text-xs text-slate-500">ICO/PNG (32x32)</p>
                            </div>
                        </div>

                        <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 flex items-center space-x-4">
                            <div class="relative group">
                                <div class="w-16 h-16 bg-white rounded-xl shadow-sm overflow-hidden flex items-center justify-center border border-slate-200">
                                    <template x-if="ogPreview">
                                        <img :src="ogPreview" class="w-full h-full object-contain p-1">
                                    </template>
                                    <template x-if="!ogPreview">
                                        @if ($settings->og_image)
                                            <img src="{{ $settings->og_image_url }}" class="w-full h-full object-contain p-1">
                                        @else
                                            <i class="fa-solid fa-share-nodes text-slate-300 text-2xl"></i>
                                        @endif
                                    </template>
                                </div>
                                <label class="absolute inset-0 flex items-center justify-center bg-black/40 text-white rounded-xl opacity-0 group-hover:opacity-100 cursor-pointer transition-opacity">
                                    <i class="fa-solid fa-camera"></i>
                                    <input type="file" name="og_image" class="hidden" @change="handleOg">
                                </label>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900">OG Image</h4>
                                <p class="text-xs text-slate-500">Social Media (1200x630)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Localization -->
            <div class="glass p-8 rounded-[3rem] shadow-xl" data-aos="fade-up">
                <h3 class="text-xl font-black text-slate-900 mb-8 flex items-center">
                    <span class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fa-solid fa-language"></i>
                    </span>
                    Footer & Localization
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Footer Text (ID)</label>
                        <textarea name="footer_text_id" rows="2" 
                            class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-medium">{{ $settings->footer_text_id }}</textarea>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Footer Text (EN)</label>
                        <textarea name="footer_text_en" rows="2" 
                            class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-indigo-500 transition-all font-medium">{{ $settings->footer_text_en }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Invoice Branding -->
            <div class="glass p-8 rounded-[3rem] shadow-xl relative overflow-hidden" data-aos="fade-up">
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-emerald-500/5 rounded-full blur-3xl"></div>
                <h3 class="text-xl font-black text-slate-900 mb-8 flex items-center">
                    <span class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                    </span>
                    Invoice Branding
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Company Name on Invoice</label>
                        <input type="text" name="invoice_company_name" value="{{ $settings->invoice_company_name }}"
                            class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-emerald-500 transition-all font-medium">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Signer Name (Penanda Tangan)</label>
                        <input type="text" name="invoice_signer_name" value="{{ $settings->invoice_signer_name }}"
                            class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-emerald-500 transition-all font-medium" placeholder="Contoh: Rifal Kurniawan">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Signer Title (Jabatan)</label>
                        <input type="text" name="invoice_signer_title" value="{{ $settings->invoice_signer_title }}"
                            class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-emerald-500 transition-all font-medium" placeholder="Contoh: Authorized Representative">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Tarif Pajak PPN (%)</label>
                        <input type="number" name="tax_rate" step="0.01" min="0" max="100" value="{{ $settings->tax_rate ?? 11.00 }}"
                            class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-emerald-500 transition-all font-medium" placeholder="Contoh: 11.00">
                    </div>
                    <div class="md:col-span-2 space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Company Address on Invoice</label>
                        <textarea name="invoice_company_address" rows="2" 
                            class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-emerald-500 transition-all font-medium">{{ $settings->invoice_company_address }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 md:col-span-2 pt-4" 
                        x-data="{ 
                            invoiceLogoPreview: null,
                            invoiceSignaturePreview: null,
                            invoiceQrisPreview: null,
                            handleInvoiceLogo(e) {
                                const file = e.target.files[0];
                                if (file) this.invoiceLogoPreview = URL.createObjectURL(file);
                            },
                            handleInvoiceSignature(e) {
                                const file = e.target.files[0];
                                if (file) this.invoiceSignaturePreview = URL.createObjectURL(file);
                            },
                            handleInvoiceQris(e) {
                                const file = e.target.files[0];
                                if (file) this.invoiceQrisPreview = URL.createObjectURL(file);
                            }
                        }">
                        <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 flex items-center space-x-4">
                            <div class="relative group">
                                <div class="w-16 h-16 bg-white rounded-xl shadow-sm overflow-hidden flex items-center justify-center border border-slate-200">
                                    <template x-if="invoiceLogoPreview">
                                        <img :src="invoiceLogoPreview" class="w-full h-full object-contain p-1">
                                    </template>
                                    <template x-if="!invoiceLogoPreview">
                                        @if ($settings->invoice_logo)
                                            <img src="{{ $settings->invoice_logo_url }}" class="w-full h-full object-contain p-1">
                                        @else
                                            <i class="fa-solid fa-file-image text-slate-300 text-2xl"></i>
                                        @endif
                                    </template>
                                </div>
                                <label class="absolute inset-0 flex items-center justify-center bg-black/40 text-white rounded-xl opacity-0 group-hover:opacity-100 cursor-pointer transition-opacity">
                                    <i class="fa-solid fa-camera"></i>
                                    <input type="file" name="invoice_logo" class="hidden" @change="handleInvoiceLogo">
                                </label>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900">Logo</h4>
                                <p class="text-[10px] text-slate-500 uppercase">Kop Surat</p>
                            </div>
                        </div>

                        <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 flex items-center space-x-4">
                            <div class="relative group">
                                <div class="w-16 h-16 bg-white rounded-xl shadow-sm overflow-hidden flex items-center justify-center border border-slate-200">
                                    <template x-if="invoiceSignaturePreview">
                                        <img :src="invoiceSignaturePreview" class="w-full h-full object-contain p-1">
                                    </template>
                                    <template x-if="!invoiceSignaturePreview">
                                        @if ($settings->invoice_signature)
                                            <img src="{{ $settings->invoice_signature_url }}" class="w-full h-full object-contain p-1">
                                        @else
                                            <i class="fa-solid fa-signature text-slate-300 text-2xl"></i>
                                        @endif
                                    </template>
                                </div>
                                <label class="absolute inset-0 flex items-center justify-center bg-black/40 text-white rounded-xl opacity-0 group-hover:opacity-100 cursor-pointer transition-opacity">
                                    <i class="fa-solid fa-camera"></i>
                                    <input type="file" name="invoice_signature" class="hidden" @change="handleInvoiceSignature">
                                </label>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900">Sign</h4>
                                <p class="text-[10px] text-slate-500 uppercase">Signature</p>
                            </div>
                        </div>

                        <!-- Dedicated Payment Methods Banner Reference -->
                        <div class="p-6 bg-indigo-50/45 rounded-3xl border border-indigo-100/50 flex items-center justify-between col-span-2 mt-4">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-lg">
                                    <i class="fa-solid fa-credit-card"></i>
                                </div>
                                <div>
                                    <h4 class="font-black text-slate-900 text-sm">Manual Payment Methods</h4>
                                    <p class="text-[10px] text-slate-500 uppercase tracking-widest mt-0.5">Manage bank accounts & QRIS scan codes</p>
                                </div>
                            </div>
                            <a href="{{ route('dashboard.payment_methods.edit') }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-[10px] uppercase tracking-widest rounded-xl transition-all shadow-md">
                                Manage Payments <i class="fa-solid fa-arrow-right ml-1"></i>
                            </a>
                        </div>
            </div>

            <!-- Contact & Social -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8" data-aos="fade-up">
                <!-- Social Media -->
                <div class="glass p-8 rounded-[3rem] shadow-xl">
                    <h3 class="text-xl font-black text-slate-900 mb-8 flex items-center">
                        <span class="w-10 h-10 bg-pink-100 text-pink-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fa-solid fa-share-nodes"></i>
                        </span>
                        Social Media
                    </h3>
                    @php $socials = $settings->social_links ?? []; @endphp
                    <div class="space-y-4">
                        @foreach(['instagram' => 'fa-instagram', 'facebook' => 'fa-facebook-f', 'twitter' => 'fa-x-twitter', 'linkedin' => 'fa-linkedin-in'] as $key => $icon)
                            <div class="flex items-center space-x-4 bg-slate-50 p-2 pr-4 rounded-2xl border border-slate-100 group transition-all hover:bg-white hover:shadow-lg">
                                <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-white shadow-sm text-slate-400 group-hover:text-blue-500 transition-colors">
                                    <i class="fa-brands {{ $icon }}"></i>
                                </div>
                                <input type="text" name="social[{{ $key }}]" value="{{ $socials[$key] ?? '' }}"
                                    class="flex-1 bg-transparent border-none focus:ring-0 font-medium text-sm"
                                    placeholder="https://{{ $key }}.com/...">
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="glass p-8 rounded-[3rem] shadow-xl">
                    <h3 class="text-xl font-black text-slate-900 mb-8 flex items-center">
                        <span class="w-10 h-10 bg-green-100 text-green-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fa-solid fa-phone"></i>
                        </span>
                        Contact Info
                    </h3>
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Business Email</label>
                            <input type="email" name="contact_email" value="{{ $settings->contact_email }}"
                                class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-bold">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Phone Number</label>
                            <input type="text" name="contact_phone" value="{{ $settings->contact_phone }}"
                                class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-bold">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Office Address</label>
                            <input type="text" name="contact_address" value="{{ $settings->contact_address }}"
                                class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-bold">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="fixed bottom-8 left-1/2 -translate-x-1/2 lg:left-auto lg:right-12 lg:translate-x-0 z-50">
                <button type="submit"
                    class="px-12 py-5 bg-blue-600 text-white font-black rounded-[2.5rem] hover:bg-blue-700 hover:scale-105 shadow-2xl shadow-blue-600/40 transition-all flex items-center space-x-3 group">
                    <i class="fa-solid fa-cloud-arrow-up group-hover:animate-bounce"></i>
                    <span class="uppercase tracking-[0.2em] text-xs">Save All Changes</span>
                </button>
            </div>
        </form>
    </div>
</x-app-layout>

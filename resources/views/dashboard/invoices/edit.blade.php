<x-app-layout>
    <x-slot name="header">
        {{ __('Edit Invoice') }} : {{ $invoice->invoice_number }}
    </x-slot>

    <div class="max-w-5xl mx-auto pb-20">
        <form action="{{ route('dashboard.invoices.update', $invoice->id) }}" method="POST" x-data="{
            items: {{ json_encode($invoice->items) }},
            discount: {{ $invoice->discount ?? 0 }},
            discount_type: '{{ $invoice->discount_type ?? 'percent' }}',
            tax: {{ $invoice->tax ?? 0 }},
            tax_type: '{{ $invoice->tax_type ?? 'percent' }}',
            addItem() {
                this.items.push({ description: '', quantity: 1, unit_price: 0 });
            },
            removeItem(index) {
                if (this.items.length > 1) this.items.splice(index, 1);
            },
            get subtotal() {
                return this.items.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
            },
            get discountAmount() {
                return this.discount_type === 'percent' 
                    ? (this.subtotal * (this.discount / 100)) 
                    : this.discount;
            },
            get taxAmount() {
                return this.tax_type === 'percent'
                    ? ((this.subtotal - this.discountAmount) * (this.tax / 100))
                    : this.tax;
            },
            get finalTotal() {
                return this.subtotal - this.discountAmount + this.taxAmount;
            }
        }">
            @csrf
            @method('PATCH')
            
            <div class="space-y-8">
                <!-- Basic Info -->
                <div class="glass p-10 rounded-[3.5rem] shadow-xl relative overflow-hidden" data-aos="fade-up">
                    <div class="absolute -right-20 -top-20 w-64 h-64 bg-blue-500/5 rounded-full blur-3xl"></div>
                    <h3 class="text-xl font-black text-slate-900 mb-8 flex items-center">
                        <span class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fa-solid fa-info"></i>
                        </span>
                        Invoice Details
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Invoice Number</label>
                            <input type="text" name="invoice_number" value="{{ $invoice->invoice_number }}" required readonly
                                class="block w-full bg-slate-100 border-none rounded-2xl p-4 focus:ring-0 font-black text-slate-400 cursor-not-allowed">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Issue Date</label>
                            <input type="date" name="issue_date" value="{{ $invoice->issue_date->format('Y-m-d') }}" required
                                class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-bold">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Due Date (Optional)</label>
                            <input type="date" name="due_date" value="{{ $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '' }}"
                                class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-bold">
                        </div>
                    </div>
                </div>

                <!-- Client Info -->
                <div class="glass p-10 rounded-[3.5rem] shadow-xl" data-aos="fade-up">
                    <h3 class="text-xl font-black text-slate-900 mb-8 flex items-center">
                        <span class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fa-solid fa-user-tie"></i>
                        </span>
                        Client Information
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Client Name</label>
                            <input type="text" name="client_name" value="{{ $invoice->client_name }}" required placeholder="Project Owner / Company"
                                class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-bold">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Client Email</label>
                            <input type="email" name="client_email" value="{{ $invoice->client_email }}" placeholder="client@example.com"
                                class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-bold">
                        </div>
                        <div class="md:col-span-2 space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Client Address</label>
                            <textarea name="client_address" rows="2" placeholder="Full Billing Address"
                                class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-medium">{{ $invoice->client_address }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Items -->
                <div class="glass p-10 rounded-[3.5rem] shadow-xl" data-aos="fade-up">
                    <div class="flex justify-between items-center mb-8">
                        <h3 class="text-xl font-black text-slate-900 flex items-center">
                            <span class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center mr-4">
                                <i class="fa-solid fa-list-check"></i>
                            </span>
                            Line Items
                        </h3>
                        <button type="button" @click="addItem"
                            class="px-6 py-2 bg-emerald-500 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-emerald-600 transition-all shadow-lg shadow-emerald-500/20">
                            <i class="fa-solid fa-plus mr-2"></i> Add Item
                        </button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end bg-slate-50/50 p-6 rounded-3xl border border-slate-100 group transition-all hover:bg-white hover:shadow-lg">
                                <div class="md:col-span-6 space-y-2">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Description</label>
                                    <input type="text" :name="`items[${index}][description]`" x-model="item.description" required placeholder="Service / Product name"
                                        class="block w-full bg-white border-none rounded-xl p-3 focus:ring-2 focus:ring-blue-500 transition-all font-bold text-sm">
                                </div>
                                <div class="md:col-span-2 space-y-2">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Qty</label>
                                    <input type="number" :name="`items[${index}][quantity]`" x-model.number="item.quantity" required min="1"
                                        class="block w-full bg-white border-none rounded-xl p-3 focus:ring-2 focus:ring-blue-500 transition-all font-bold text-sm">
                                </div>
                                <div class="md:col-span-3 space-y-2">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Price (IDR)</label>
                                    <input type="number" :name="`items[${index}][unit_price]`" x-model.number="item.unit_price" required
                                        class="block w-full bg-white border-none rounded-xl p-3 focus:ring-2 focus:ring-blue-500 transition-all font-black text-sm text-emerald-600">
                                </div>
                                <div class="md:col-span-1 flex justify-center pb-2">
                                    <button type="button" @click="removeItem(index)" 
                                        class="w-10 h-10 bg-red-50 text-red-500 rounded-xl flex items-center justify-center hover:bg-red-500 hover:text-white transition-all">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Total Section -->
                    <div class="mt-10 pt-8 border-t border-slate-100 flex flex-col items-end">
                        <div class="w-full max-w-sm space-y-4">
                            <div class="flex justify-between items-center text-slate-400 font-bold uppercase tracking-widest text-xs">
                                <span>Subtotal</span>
                                <span x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(subtotal)"></span>
                            </div>

                            <!-- Discount Input -->
                            <div class="flex justify-between items-center group">
                                <div class="flex flex-col space-y-1">
                                    <span class="text-slate-400 font-bold uppercase tracking-widest text-xs">Discount</span>
                                    <div class="flex items-center space-x-2">
                                        <div class="relative w-28">
                                            <input type="number" name="discount" x-model.number="discount" 
                                                class="w-full bg-slate-50 border-none rounded-lg p-2 text-xs font-black text-blue-600 focus:ring-1 focus:ring-blue-500">
                                            <span class="absolute right-2 top-2 text-[10px] font-black text-slate-300" x-text="discount_type === 'percent' ? '%' : 'IDR'"></span>
                                        </div>
                                        <select name="discount_type" x-model="discount_type" class="bg-slate-100 border-none rounded-lg p-2 text-[10px] font-black uppercase focus:ring-0">
                                            <option value="percent">%</option>
                                            <option value="fixed">Fixed</option>
                                        </select>
                                    </div>
                                </div>
                                <span class="text-blue-600 font-bold text-xs" x-text="'- ' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(discountAmount)"></span>
                            </div>

                            <!-- Tax Input -->
                            <div class="flex justify-between items-center group">
                                <div class="flex flex-col space-y-1">
                                    <span class="text-slate-400 font-bold uppercase tracking-widest text-xs">Tax (PPN)</span>
                                    <div class="flex items-center space-x-2">
                                        <div class="relative w-28">
                                            <input type="number" name="tax" x-model.number="tax" 
                                                class="w-full bg-slate-50 border-none rounded-lg p-2 text-xs font-black text-emerald-600 focus:ring-1 focus:ring-emerald-500">
                                            <span class="absolute right-2 top-2 text-[10px] font-black text-slate-300" x-text="tax_type === 'percent' ? '%' : 'IDR'"></span>
                                        </div>
                                        <select name="tax_type" x-model="tax_type" class="bg-slate-100 border-none rounded-lg p-2 text-[10px] font-black uppercase focus:ring-0">
                                            <option value="percent">%</option>
                                            <option value="fixed">Fixed</option>
                                        </select>
                                    </div>
                                </div>
                                <span class="text-emerald-600 font-bold text-xs" x-text="'+ ' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(taxAmount)"></span>
                            </div>

                            <div class="pt-4 border-t border-slate-100 flex justify-between items-center text-slate-900 font-black uppercase tracking-widest">
                                <span class="text-sm">Total Amount</span>
                                <span class="text-2xl text-blue-600" x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(finalTotal)"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes & Status -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="md:col-span-2 glass p-10 rounded-[3.5rem] shadow-xl">
                        <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1 mb-4 block">Additional Notes</label>
                        <textarea name="notes" rows="4" placeholder="Payment terms, bank details, etc."
                            class="block w-full bg-slate-50 border-none rounded-[2rem] p-6 focus:ring-2 focus:ring-blue-500 transition-all font-medium text-sm leading-relaxed">{{ $invoice->notes }}</textarea>
                    </div>
                    <div class="glass p-10 rounded-[3.5rem] shadow-xl space-y-8">
                        <div class="space-y-4">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1 block">Status</label>
                            <select name="status" class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-black appearance-none">
                                <option value="Draft" {{ $invoice->status == 'Draft' ? 'selected' : '' }}>Draft</option>
                                <option value="Sent" {{ $invoice->status == 'Sent' ? 'selected' : '' }}>Sent to Client</option>
                                <option value="Paid" {{ $invoice->status == 'Paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                        </div>
                        <button type="submit"
                            class="w-full py-6 bg-slate-900 text-white font-black rounded-3xl uppercase tracking-[0.2em] text-xs shadow-2xl shadow-slate-900/30 hover:bg-black hover:scale-[1.02] transition-all">
                            Update Invoice
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        {{ __('Package Orders') }}
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="glass rounded-[2rem] overflow-hidden shadow-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="p-6 text-xs font-black uppercase tracking-widest text-slate-400">Order ID</th>
                            <th class="p-6 text-xs font-black uppercase tracking-widest text-slate-400">Customer</th>
                            <th class="p-6 text-xs font-black uppercase tracking-widest text-slate-400">Website Details</th>
                            <th class="p-6 text-xs font-black uppercase tracking-widest text-slate-400">Package</th>
                            <th class="p-6 text-xs font-black uppercase tracking-widest text-slate-400">Payment Status</th>
                            <th class="p-6 text-xs font-black uppercase tracking-widest text-slate-400">Progress Pengerjaan</th>
                            <th class="p-6 text-xs font-black uppercase tracking-widest text-slate-400 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($orders as $order)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="p-6">
                                    <span class="font-mono text-xs text-slate-500">#{{ substr($order->id, 0, 8) }}</span>
                                    <div class="text-[10px] text-slate-400 mt-1">{{ $order->created_at->format('d M Y') }}</div>
                                </td>
                                <td class="p-6">
                                    <div class="font-bold text-slate-900">{{ $order->user->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $order->user->email }}</div>
                                </td>
                                <td class="p-6">
                                    @if($order->website_name)
                                        <div class="font-bold text-slate-900">{{ $order->website_name }}</div>
                                        <div class="text-[10px] text-slate-500 mt-1">Jenis: {{ $order->business_type }}</div>
                                        @if($order->website_url && !$order->buy_domain)
                                            <div class="text-[10px] text-blue-500 font-mono mt-1">
                                                <i class="fa-solid fa-link mr-1"></i>{{ $order->website_url }}
                                            </div>
                                        @endif
                                        @if($order->client_notes)
                                            <div class="text-[10px] text-slate-400 mt-1 italic border-l-2 border-slate-200 pl-2 max-w-xs truncate" title="{{ $order->client_notes }}">
                                                "{{ $order->client_notes }}"
                                            </div>
                                        @endif

                                        @if($order->buy_domain && $order->domain_name)
                                            <div class="mt-3 p-3 bg-slate-50 border border-slate-100 rounded-xl max-w-xs">
                                                <div class="text-[9px] font-black text-indigo-600 uppercase tracking-widest flex items-center">
                                                    <i class="fa-solid fa-server mr-1"></i> Beli Domain Baru
                                                </div>
                                                <div class="font-mono text-xs font-bold text-slate-800 mt-1 break-all">{{ $order->domain_name }}</div>
                                                <div class="text-[9px] text-slate-500 font-bold mt-0.5">Harga: Rp {{ number_format($order->domain_price, 0, ',', '.') }}</div>
                                                
                                                @php
                                                    $domStatusClasses = [
                                                        'pending' => 'bg-amber-100 text-amber-700 border-amber-200',
                                                        'registered' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                                        'failed' => 'bg-red-100 text-red-700 border-red-200',
                                                    ];
                                                    $domStatusLabels = [
                                                        'pending' => 'Menunggu Registrasi',
                                                        'registered' => 'Aktif / Terdaftar',
                                                        'failed' => 'Registrasi Gagal',
                                                    ];
                                                @endphp
                                                <div class="flex items-center justify-between mt-2.5 pt-2 border-t border-slate-100 gap-2">
                                                    <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-wider border {{ $domStatusClasses[$order->domain_status] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                                        {{ $domStatusLabels[$order->domain_status] ?? $order->domain_status }}
                                                    </span>

                                                    @if($order->status === 'paid' && $order->domain_status !== 'registered')
                                                        <form action="{{ route('dashboard.orders.register_domain', $order->id) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit" class="px-2 py-1 bg-indigo-600 text-white text-[8px] font-black uppercase tracking-wider rounded hover:bg-indigo-700 transition-colors shadow shadow-indigo-600/10">
                                                                Daftarkan
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-xs text-slate-400 italic">No details</span>
                                    @endif
                                </td>
                                <td class="p-6">
                                    <div class="font-bold text-slate-900">{{ $order->package_name }}</div>
                                    
                                    <div class="mt-2 space-y-0.5 text-[9px] text-slate-500 border-t border-slate-100 pt-1.5">
                                        <div class="flex justify-between">
                                            <span>Subtotal:</span>
                                            <span class="font-bold text-slate-700">Rp {{ number_format($order->subtotal_amount > 0 ? $order->subtotal_amount : $order->total_amount, 0, ',', '.') }}</span>
                                        </div>
                                        @if($order->discount_amount > 0)
                                            <div class="flex justify-between text-emerald-600 font-medium">
                                                <span>Diskon ({{ $order->voucher_code }}):</span>
                                                <span>- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                                            </div>
                                        @endif
                                        @php
                                            $orderTaxRate = ($order->subtotal_amount - $order->discount_amount) > 0 
                                                ? round(($order->tax_amount / ($order->subtotal_amount - $order->discount_amount)) * 100) 
                                                : 11;
                                        @endphp
                                        <div class="flex justify-between">
                                            <span>PPN ({{ $orderTaxRate }}%):</span>
                                            <span class="font-bold text-slate-700">Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between text-slate-800 font-black border-t border-dashed border-slate-200 pt-1">
                                            <span>Total:</span>
                                            <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                    
                                    @if($order->payment_scheme === 'dp')
                                        <div class="text-[9px] font-black text-indigo-600 bg-indigo-50 border border-indigo-100 rounded px-1.5 py-0.5 mt-2 inline-block uppercase tracking-wider">DP (50%)</div>
                                        <div class="text-[9px] text-slate-500 mt-1">DP: Rp {{ number_format($order->dp_amount, 0, ',', '.') }}</div>
                                        <div class="text-[9px] text-slate-500">Sisa: Rp {{ number_format($order->remaining_balance, 0, ',', '.') }}</div>
                                    @else
                                        <div class="text-[9px] font-black text-emerald-600 bg-emerald-50 border border-emerald-100 rounded px-1.5 py-0.5 mt-2 inline-block uppercase tracking-wider">Lunas (100%)</div>
                                    @endif
                                    
                                    <div class="text-[9px] text-slate-400 uppercase tracking-widest mt-2"><i class="fa-solid fa-credit-card mr-1"></i> {{ str_replace('Duitku|', '', $order->payment_method ?: 'Belum memilih') }}</div>

                                    @php
                                        $paymentTickets = $order->tickets->filter(fn($t) => !empty($t->attachment));
                                    @endphp
                                    @if($paymentTickets->isNotEmpty())
                                        <div class="mt-3 pt-2 border-t border-slate-100 text-left">
                                            <div class="text-[8px] font-black text-blue-500 uppercase tracking-widest mb-1.5 flex items-center">
                                                <i class="fa-solid fa-receipt mr-1"></i> Bukti Transfer ({{ $paymentTickets->count() }})
                                            </div>
                                            <div class="flex flex-wrap gap-1.5">
                                                @foreach($paymentTickets as $pt)
                                                    <a href="{{ Storage::url($pt->attachment) }}" target="_blank" class="block w-9 h-9 rounded-lg overflow-hidden border border-slate-200 group relative hover:border-blue-500 transition-colors" title="Buka bukti transfer: {{ $pt->subject }}">
                                                        <img src="{{ Storage::url($pt->attachment) }}" class="w-full h-full object-cover">
                                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                                            <i class="fa-solid fa-eye text-white text-[8px]"></i>
                                                        </div>
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td class="p-6">
                                    @php
                                        $statusClasses = [
                                            'pending' => 'bg-amber-100 text-amber-600 border-amber-200',
                                            'paid' => 'bg-emerald-100 text-emerald-600 border-emerald-200',
                                            'completed' => 'bg-blue-100 text-blue-600 border-blue-200',
                                            'cancelled' => 'bg-red-100 text-red-600 border-red-200',
                                        ];
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $statusClasses[$order->status] ?? 'bg-slate-100 text-slate-600' }}">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td class="p-6">
                                    @php
                                        $workStatusClasses = [
                                            'pending' => 'bg-slate-100 text-slate-600 border-slate-200',
                                            'in_progress' => 'bg-blue-100 text-blue-600 border-blue-200',
                                            'revision' => 'bg-amber-100 text-amber-600 border-amber-200',
                                            'completed' => 'bg-emerald-100 text-emerald-600 border-emerald-200',
                                            'cancelled' => 'bg-red-100 text-red-600 border-red-200',
                                        ];
                                        $workStatusLabels = [
                                            'pending' => 'Menunggu',
                                            'in_progress' => 'Pengerjaan',
                                            'revision' => 'Revisi',
                                            'completed' => 'Selesai',
                                            'cancelled' => 'Batal',
                                        ];
                                    @endphp
                                    
                                    <div class="space-y-2">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $workStatusClasses[$order->work_status] ?? 'bg-slate-100 text-slate-600' }}">
                                            {{ $workStatusLabels[$order->work_status] ?? $order->work_status }}
                                        </span>
                                        
                                        <form action="{{ route('dashboard.orders.work_status', $order->id) }}" method="POST" class="flex flex-col space-y-1.5 pt-1">
                                            @csrf
                                            @method('PATCH')
                                            <div class="flex flex-col space-y-1">
                                                <select name="work_status" onchange="this.form.submit()" class="text-[9px] font-bold rounded border-slate-200 bg-white p-1 text-slate-700 py-0.5 leading-tight focus:ring-blue-500">
                                                    <option value="pending" {{ $order->work_status == 'pending' ? 'selected' : '' }}>Menunggu</option>
                                                    <option value="in_progress" {{ $order->work_status == 'in_progress' ? 'selected' : '' }}>Pengerjaan</option>
                                                    <option value="revision" {{ $order->work_status == 'revision' ? 'selected' : '' }}>Revisi</option>
                                                    <option value="completed" {{ $order->work_status == 'completed' ? 'selected' : '' }}>Selesai</option>
                                                    <option value="cancelled" {{ $order->work_status == 'cancelled' ? 'selected' : '' }}>Batal</option>
                                                </select>
                                                <input type="date" name="delivery_date" value="{{ $order->delivery_date ? $order->delivery_date->format('Y-m-d') : '' }}" onchange="this.form.submit()" class="text-[9px] font-bold rounded border-slate-200 bg-white p-1 text-slate-700 py-0.5 leading-tight w-full focus:ring-blue-500" title="Tanggal Selesai">
                                            </div>
                                        </form>
                                    </div>
                                </td>
                                <td class="p-6 text-right">
                                    <div class="flex flex-col items-end space-y-2">
                                        <!-- Actions depending on status -->
                                        @if($order->status === 'pending')
                                            <div class="flex items-center space-x-2">
                                                <form action="{{ route('dashboard.orders.update', $order->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="paid">
                                                    <button type="submit" class="px-3 py-1.5 bg-emerald-500 text-white text-[10px] font-black uppercase tracking-widest rounded hover:bg-emerald-600 transition-colors" title="Konfirmasi & Proses">
                                                        <i class="fa-solid fa-check mr-1"></i> Konfirmasi @if($order->payment_scheme === 'dp') DP @endif
                                                    </button>
                                                </form>
                                                <form action="{{ route('dashboard.orders.update', $order->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="cancelled">
                                                    <button type="submit" class="px-3 py-1.5 bg-red-100 text-red-600 border border-red-200 text-[10px] font-black uppercase tracking-widest rounded hover:bg-red-200 transition-colors" onclick="return confirm('Batalkan pesanan ini?')">
                                                        <i class="fa-solid fa-times"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif

                                         <!-- Konfirmasi Pelunasan for DP payment scheme -->
                                         @if($order->status === 'paid' && $order->payment_scheme === 'dp' && $order->final_invoice_id && $order->finalInvoice && $order->finalInvoice->status === 'Unpaid')
                                             <div class="flex items-center space-x-2">
                                                 <form action="{{ route('dashboard.orders.update', $order->id) }}" method="POST">
                                                     @csrf
                                                     @method('PATCH')
                                                     <input type="hidden" name="status" value="completed">
                                                     <button type="submit" class="px-3 py-1.5 bg-emerald-600 text-white text-[10px] font-black uppercase tracking-widest rounded hover:bg-emerald-700 transition-colors flex items-center gap-1 shadow shadow-emerald-600/10" title="Konfirmasi Pembayaran Pelunasan & Selesaikan Order">
                                                         <i class="fa-solid fa-circle-check mr-1"></i> Konfirmasi Pelunasan
                                                     </button>
                                                 </form>
                                             </div>
                                         @endif

                                        <!-- Tagih Pelunasan button for DP payment scheme -->
                                        @if($order->payment_scheme === 'dp' && $order->status === 'paid' && !$order->final_invoice_id)
                                            <form action="{{ route('dashboard.orders.tagih_pelunasan', $order->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 bg-purple-600 text-white text-[10px] font-black uppercase tracking-widest rounded hover:bg-purple-700 transition-colors flex items-center gap-1 shadow shadow-purple-600/10" onclick="return confirm('Kirim invoice pelunasan sisa 50% ke klien?')">
                                                    <i class="fa-solid fa-file-invoice-dollar text-[11px]"></i> Tagih Pelunasan
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <div class="flex items-center space-x-2">
                                            @if($order->invoice)
                                                <a href="{{ route('dashboard.invoices.show', $order->invoice->id) }}" class="px-3 py-1.5 bg-slate-100 text-slate-600 text-[10px] font-black uppercase tracking-widest rounded hover:bg-slate-200 transition-colors" title="Invoice @if($order->payment_scheme === 'dp') DP @else Utama @endif">
                                                    <i class="fa-solid fa-file-invoice mr-1"></i> Inv @if($order->payment_scheme === 'dp') DP @endif
                                                </a>
                                            @endif

                                            @if($order->finalInvoice)
                                                <a href="{{ route('dashboard.invoices.show', $order->finalInvoice->id) }}" class="px-3 py-1.5 bg-purple-50 text-purple-600 text-[10px] font-black uppercase tracking-widest rounded hover:bg-purple-100 transition-colors" title="Invoice Pelunasan">
                                                    <i class="fa-solid fa-file-invoice-dollar mr-1"></i> Inv Pelunasan
                                                </a>
                                            @endif

                                            @if($order->tickets && $order->tickets->count() > 0)
                                                <a href="{{ route('dashboard.tickets.index') }}" class="px-3 py-1.5 bg-blue-100 text-blue-600 text-[10px] font-black uppercase tracking-widest rounded hover:bg-blue-200 transition-colors" title="Lihat Bukti Transfer">
                                                    <i class="fa-solid fa-receipt mr-1"></i> Bukti
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

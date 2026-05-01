<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; margin: 0; padding: 0; }
            .invoice-sheet { 
                box-shadow: none !important; 
                margin: 0 !important; 
                padding: 15mm !important; 
                width: 100% !important;
                height: 297mm !important;
                page-break-after: avoid;
            }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }
        @page {
            size: A4;
            margin: 0;
        }
        .invoice-sheet {
            max-width: 210mm;
            min-height: 297mm;
        }
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-900">
    <!-- Toolbar -->
    <div class="no-print bg-white border-b sticky top-0 z-50 px-6 py-3 flex justify-between items-center shadow-sm">
        <div class="flex items-center space-x-3">
            <a href="{{ route('dashboard.invoices.index') }}" class="text-slate-400 hover:text-slate-900 transition-colors">
                <i class="fa-solid fa-chevron-left"></i>
            </a>
            <span class="font-bold text-sm uppercase tracking-widest text-slate-500">Invoice Preview</span>
        </div>
        <button onclick="window.print()" class="px-6 py-2 bg-blue-600 text-white font-black rounded-lg hover:bg-blue-700 transition-all text-xs uppercase tracking-widest flex items-center">
            <i class="fa-solid fa-print mr-2"></i> Print / Save PDF
        </button>
    </div>

    <!-- Invoice Sheet -->
    <div class="invoice-sheet mx-auto my-8 bg-white shadow-2xl p-16 flex flex-col relative overflow-hidden">
        
        <!-- Top Bar Decor -->
        <div class="absolute top-0 left-0 w-full h-2 bg-blue-600"></div>

        <!-- Header -->
        <div class="flex justify-between items-start mb-12">
            <div class="flex items-center space-x-6">
                @if($settings->invoice_logo)
                    <img src="{{ $settings->invoice_logo_url }}" class="h-12 object-contain">
                @else
                    <h2 class="text-2xl font-black tracking-tighter">FK<span class="text-blue-600">Studio</span></h2>
                @endif
                <div class="w-px h-10 bg-slate-200"></div>
                <div class="text-[10px] leading-tight text-slate-500 max-w-[200px]">
                    <p class="font-black text-slate-900 mb-0.5 uppercase tracking-tighter">{{ $settings->invoice_company_name ?: 'FKStudio Agency' }}</p>
                    <p>{{ $settings->invoice_company_address ?: 'Jl. Raya Digital No. 21, Jakarta' }}</p>
                </div>
            </div>
            <div class="text-right">
                <h1 class="text-4xl font-black uppercase tracking-tighter text-slate-900 leading-none mb-2">Invoice</h1>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-600">{{ $invoice->invoice_number }}</p>
            </div>
        </div>

        <!-- Info Grid -->
        <div class="grid grid-cols-2 gap-12 mb-10 pb-8 border-b border-slate-100">
            <div class="space-y-4">
                <div>
                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-2">Billed To</p>
                    <p class="text-sm font-black text-slate-900 mb-0.5">{{ $invoice->client_name }}</p>
                    @if($invoice->client_email) <p class="text-[10px] text-blue-600 font-bold mb-1">{{ $invoice->client_email }}</p> @endif
                    <p class="text-[10px] text-slate-500 leading-relaxed italic">{!! nl2br(e($invoice->client_address)) !!}</p>
                </div>
            </div>
            <div class="flex justify-end text-right">
                <div class="grid grid-cols-2 gap-x-8 gap-y-4">
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1">Issue Date</p>
                        <p class="text-[11px] font-bold text-slate-900">{{ $invoice->issue_date->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1">Due Date</p>
                        <p class="text-[11px] font-bold text-slate-900">{{ $invoice->due_date ? $invoice->due_date->format('d M Y') : 'Upon Receipt' }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1">Status</p>
                        <span class="text-[10px] font-black uppercase tracking-widest {{ $invoice->status == 'Paid' ? 'text-emerald-600' : 'text-slate-400' }}">
                            {{ $invoice->status }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items -->
        <div class="flex-grow">
            <table class="w-full text-left border-collapse mb-8">
                <thead>
                    <tr class="bg-slate-50 border-y border-slate-100">
                        <th class="px-4 py-3 text-[9px] font-black uppercase tracking-widest text-slate-400">Description</th>
                        <th class="px-4 py-3 text-[9px] font-black uppercase tracking-widest text-slate-400 text-center w-16">Qty</th>
                        <th class="px-4 py-3 text-[9px] font-black uppercase tracking-widest text-slate-400 text-right w-32">Price</th>
                        <th class="px-4 py-3 text-[9px] font-black uppercase tracking-widest text-slate-400 text-right w-32">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($invoice->items as $item)
                        <tr>
                            <td class="px-4 py-4 text-[11px] font-bold text-slate-800">{{ $item->description }}</td>
                            <td class="px-4 py-4 text-[11px] text-center text-slate-600">{{ $item->quantity }}</td>
                            <td class="px-4 py-4 text-[11px] text-right text-slate-600">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="px-4 py-4 text-[11px] text-right font-black text-slate-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Bottom Section -->
        <div class="mt-auto">
            <div class="flex justify-between items-start space-x-12">
                <!-- Payment & Notes -->
                <div class="flex-grow grid grid-cols-2 gap-8">
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-3">Payment Methods</p>
                        <div class="space-y-3">
                            @if($settings->payment_methods)
                                @foreach($settings->payment_methods as $payment)
                                    <div class="text-[10px] leading-tight">
                                        <p class="font-black text-slate-900 mb-0.5 uppercase">{{ $payment['bank'] }}</p>
                                        <p class="text-slate-500 font-medium">{{ $payment['number'] }} / {{ $payment['name'] }}</p>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        
                        @if($invoice->notes)
                            <div class="mt-6 p-4 bg-slate-50 rounded-xl">
                                <p class="text-[8px] font-black uppercase tracking-widest text-slate-400 mb-1.5">Internal Notes</p>
                                <p class="text-[9px] text-slate-500 leading-relaxed italic">{!! nl2br(e($invoice->notes)) !!}</p>
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-center items-start pt-2">
                        @if($settings->invoice_qris)
                            <div class="text-center p-3 border border-slate-100 rounded-2xl bg-white shadow-sm">
                                <img src="{{ $settings->invoice_qris_url }}" class="w-64 h-64 object-contain mx-auto mb-3">
                                <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">Scan to Pay (QRIS)</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Totals & Sign -->
                <div class="w-64 space-y-3 flex flex-col items-end">
                    <div class="w-full space-y-2">
                        <div class="flex justify-between items-center text-[10px] font-bold text-slate-400 uppercase tracking-widest px-2">
                            <span>Subtotal</span>
                            @php 
                                $subtotal = $invoice->items->sum('subtotal');
                                $discountAmount = ($invoice->discount_type === 'percent') 
                                    ? ($subtotal * ($invoice->discount / 100)) 
                                    : $invoice->discount;
                                $taxAmount = ($invoice->tax_type === 'percent')
                                    ? (($subtotal - $discountAmount) * ($invoice->tax / 100))
                                    : $invoice->tax;
                            @endphp
                            <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        
                        @if($invoice->discount > 0)
                            <div class="flex justify-between items-center text-[10px] font-bold text-blue-500 uppercase tracking-widest px-2">
                                <span>Discount ({{ $invoice->discount_type === 'percent' ? number_format($invoice->discount, 0).'%' : 'Fixed' }})</span>
                                <span>- Rp {{ number_format($discountAmount, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        @if($invoice->tax > 0)
                            <div class="flex justify-between items-center text-[10px] font-bold text-emerald-500 uppercase tracking-widest px-2">
                                <span>Tax ({{ $invoice->tax_type === 'percent' ? number_format($invoice->tax, 0).'%' : 'Fixed' }})</span>
                                <span>+ Rp {{ number_format($taxAmount, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between items-center bg-slate-900 text-white p-5 rounded-2xl shadow-xl shadow-slate-900/10 mt-4">
                            <span class="text-[9px] font-black uppercase tracking-widest opacity-60">Total Amount</span>
                            <span class="text-lg font-black tracking-tighter">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="text-center w-full pt-6">
                        <p class="text-[8px] font-black uppercase tracking-[0.2em] text-slate-300 mb-6">Authorized Signature</p>
                        @if($settings->invoice_signature)
                            <img src="{{ $settings->invoice_signature_url }}" class="h-16 mx-auto mb-2 object-contain">
                        @else
                            <div class="h-16 w-32 border-b border-dashed border-slate-200 mx-auto mb-2"></div>
                        @endif
                        <p class="text-[11px] font-black text-slate-900 uppercase tracking-tight">{{ $settings->invoice_signer_name ?: 'Owner of FKStudio' }}</p>
                        <p class="text-[8px] font-medium text-slate-400 uppercase tracking-widest">Founder & Lead Developer</p>
                    </div>
                </div>
            </div>

            <div class="mt-12 text-center">
                <p class="text-[9px] font-bold text-slate-300 uppercase tracking-[0.3em]">Thank you for choosing FKStudio</p>
            </div>
        </div>
    </div>
</body>
</html>

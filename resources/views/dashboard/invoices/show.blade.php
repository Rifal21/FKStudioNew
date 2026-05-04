<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }} | FKStudio</title>
    
    <!-- SEO & Social Sharing -->
    <meta name="title" content="Invoice {{ $invoice->invoice_number }} - FKStudio">
    <meta name="description" content="Official Invoice from FKStudio for {{ $invoice->client_name }}. Total: Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}">
    
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('invoices.public.show', $invoice->id) }}">
    <meta property="og:title" content="Invoice {{ $invoice->invoice_number }} - FKStudio">
    <meta property="og:description" content="Official Invoice from FKStudio for {{ $invoice->client_name }}. Total: Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}">
    <meta property="og:image" content="{{ $settings->invoice_logo_url ?: $settings->logo_url }}">

    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ route('invoices.public.show', $invoice->id) }}">
    <meta property="twitter:title" content="Invoice {{ $invoice->invoice_number }} - FKStudio">
    <meta property="twitter:description" content="Official Invoice from FKStudio for {{ $invoice->client_name }}. Total: Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}">
    <meta property="twitter:image" content="{{ $settings->invoice_logo_url ?: $settings->logo_url }}">

    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        @media print {
            .no-print { display: none !important; }
            body { 
                background: white !important; 
                margin: 0 !important; 
                padding: 0 !important; 
                -webkit-print-color-adjust: exact !important; 
                print-color-adjust: exact !important; 
            }
            .invoice-sheet { 
                box-shadow: none !important; 
                border: none !important;
                margin: 0 !important; 
                padding: 15mm !important; 
                width: 210mm !important;
                height: 297mm !important;
                page-break-after: avoid;
                overflow: hidden !important;
            }
            .watermark {
                display: block !important;
                opacity: 0.1 !important;
            }
            /* Force background colors for print */
            .bg-slate-50 { background-color: #f8fafc !important; }
            .bg-blue-600 { background-color: #2563eb !important; }
            .bg-emerald-50 { background-color: #ecfdf5 !important; }
            .bg-amber-50 { background-color: #fffbeb !important; }
            .text-blue-600 { color: #2563eb !important; }
            .text-emerald-600 { color: #059669 !important; }
            .text-amber-600 { color: #d97706 !important; }
            .text-white { color: #ffffff !important; }
        }
        @page {
            size: A4;
            margin: 0;
        }
        .invoice-sheet {
            max-width: 210mm;
            min-height: 297mm;
        }
        .watermark {
            position: absolute;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 10rem;
            font-weight: 900;
            color: rgba(0,0,0,0.03);
            z-index: 0;
            pointer-events: none;
            text-transform: uppercase;
            white-space: nowrap;
        }
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-900">
    <!-- Toolbar -->
    <div class="no-print bg-white/80 backdrop-blur-md border-b sticky top-0 z-50 px-6 py-3 flex justify-between items-center shadow-sm">
        <div class="flex items-center space-x-3">
            <a href="{{ route('dashboard.invoices.index') }}" class="w-10 h-10 flex items-center justify-center rounded-full bg-slate-100 text-slate-500 hover:bg-slate-900 hover:text-white transition-all">
                <i class="fa-solid fa-arrow-left text-xs"></i>
            </a>
            <div>
                <h3 class="font-black text-xs uppercase tracking-widest text-slate-400">Invoice Management</h3>
                <p class="text-[10px] font-bold text-blue-600 tracking-tighter">{{ $invoice->invoice_number }}</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            @auth
                <button onclick="copyShareLink()" class="px-4 py-2.5 bg-slate-900 text-white font-black rounded-xl hover:bg-black transition-all text-[10px] uppercase tracking-widest flex items-center shadow-lg shadow-black/10">
                    <i class="fa-solid fa-share-nodes mr-2 text-xs"></i> Share Link
                </button>
            @endauth
            <a href="{{ route('dashboard.invoices.download', $invoice->id) }}" class="px-6 py-2.5 bg-emerald-600 text-white font-black rounded-xl hover:bg-emerald-700 transition-all text-[10px] uppercase tracking-widest flex items-center shadow-lg shadow-emerald-500/20">
                <i class="fa-solid fa-download mr-2 text-xs"></i> Download PDF
            </a>
            <button onclick="window.print()" class="px-6 py-2.5 bg-blue-600 text-white font-black rounded-xl hover:bg-blue-700 transition-all text-[10px] uppercase tracking-widest flex items-center shadow-lg shadow-blue-500/20">
                <i class="fa-solid fa-print mr-2 text-xs"></i> Print Invoice
            </button>
        </div>
    </div>

    <!-- Invoice Sheet -->
    <div class="invoice-sheet mx-auto my-4 bg-white shadow-2xl p-6 md:p-8 flex flex-col relative overflow-hidden ring-1 ring-slate-100">
        
        <div class="watermark uppercase tracking-[0.5em]">FKSTUDIO</div>

        <!-- Header -->
        <div class="relative z-10 flex justify-between items-start print:flex-row mb-6">
            <div class="flex flex-col space-y-4">
                @if($settings->invoice_logo)
                    <img src="{{ $settings->invoice_logo_url }}" class="h-14 object-contain self-start">
                @else
                    <h2 class="text-3xl font-black tracking-tighter">FK<span class="text-blue-600">Studio</span></h2>
                @endif
                
                <div class="space-y-1">
                    <p class="text-[11px] font-black text-slate-900 uppercase tracking-tighter">{{ $settings->invoice_company_name ?: 'FKStudio Agency' }}</p>
                    <p class="text-[10px] text-slate-400 leading-relaxed max-w-[240px] font-medium">
                        {{ $settings->invoice_company_address ?: 'Jl. Raya Digital No. 21, Jakarta' }}
                    </p>
                </div>
            </div>

            <div class="text-right flex flex-col items-end">
                <div class="bg-blue-600 text-white px-4 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-[0.3em] mb-4">
                    Invoice
                </div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tighter mb-1">{{ $invoice->invoice_number }}</h1>
                <div class="flex items-center space-x-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                    <span>Date</span>
                    <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                    <span class="text-slate-900">{{ $invoice->issue_date->format('d F Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Details Grid -->
        <div class="relative z-10 grid grid-cols-12 gap-8 mb-4 pb-4 border-b-2 border-slate-50">
            <div class="col-span-7">
                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-blue-600 mb-4">Billed To</p>
                <div class="space-y-1.5">
                    <h3 class="text-lg font-black text-slate-900 tracking-tight">{{ $invoice->client_name }}</h3>
                    @if($invoice->client_email) 
                        <p class="text-[11px] text-blue-600 font-bold flex items-center">
                            <i class="fa-solid fa-envelope mr-2 text-[8px] opacity-50"></i>
                            {{ $invoice->client_email }}
                        </p> 
                    @endif
                    <p class="text-[11px] text-slate-500 leading-relaxed max-w-sm">
                        {!! nl2br(e($invoice->client_address)) !!}
                    </p>
                </div>
            </div>
            <div class="col-span-5 flex flex-col items-end justify-end">
                <div class="text-right space-y-4">
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Invoice Status</p>
                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest {{ $invoice->status == 'Paid' ? 'bg-emerald-50 text-emerald-600 ring-1 ring-emerald-200' : 'bg-amber-50 text-amber-600 ring-1 ring-amber-200' }}">
                            <span class="w-1.5 h-1.5 rounded-full mr-2 {{ $invoice->status == 'Paid' ? 'bg-emerald-500' : 'bg-amber-500 animate-pulse' }}"></span>
                            {{ $invoice->status }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="relative z-10 flex-grow">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-200">
                        <th class="py-2 text-[10px] font-black uppercase tracking-widest text-slate-400">Description</th>
                        <th class="py-2 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center w-20">Qty</th>
                        <th class="py-2 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right w-32">Price</th>
                        <th class="py-2 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right w-32">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($invoice->items as $item)
                        <tr class="group">
                            <td class="py-1.5">
                                <p class="text-[11px] font-bold text-slate-900 mb-0 tracking-tight">{{ $item->description }}</p>
                            </td>
                            <td class="py-1.5 text-center">
                                <span class="text-[11px] font-bold text-slate-600">{{ $item->quantity }}</span>
                            </td>
                            <td class="py-1.5 text-right">
                                <span class="text-[11px] font-medium text-slate-500 tracking-tight">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                            </td>
                            <td class="py-1.5 text-right">
                                <span class="text-[11px] font-black text-slate-900 tracking-tighter">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Footer Section -->
        <div class="relative z-10 mt-4 pt-4 border-t-2 border-slate-50">
            <div class="flex flex-col md:flex-row print:flex-row justify-between items-start gap-16 md:gap-24 print:gap-12">
                
                <!-- Left: Payment & QRIS -->
                <div class="flex-grow space-y-2">
                    <div class="grid grid-cols-2 gap-8">
                        <div>
                            <p class="text-[9px] font-black uppercase tracking-[0.2em] text-blue-600 mb-2">Payment Methods</p>
                            <div class="space-y-4">
                                @if($settings->payment_methods)
                                    @foreach($settings->payment_methods as $payment)
                                        <div class="group">
                                            <p class="text-[10px] font-black text-slate-900 uppercase tracking-tight mb-0.5 group-hover:text-blue-600 transition-colors">{{ $payment['bank'] }}</p>
                                            <p class="text-[11px] text-slate-500 font-bold tracking-tighter">{{ $payment['number'] }}</p>
                                            <p class="text-[9px] text-slate-400 font-medium uppercase">{{ $payment['name'] }}</p>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        
                        @if($settings->invoice_qris)
                            <div class="flex flex-col items-center">
                                <div class="p-2 border-2 border-slate-100 rounded-2xl bg-white shadow-xl shadow-slate-200/50">
                                    <img src="{{ $settings->invoice_qris_url }}" class="w-32 h-32 object-contain">
                                </div>
                                <p class="mt-3 text-[8px] font-black uppercase tracking-widest text-slate-300">Scan for Faster Payment</p>
                            </div>
                        @endif
                    </div>

                    @if($invoice->notes)
                        <div class="bg-slate-50/50 p-6 rounded-2xl border border-slate-100">
                            <p class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 mb-3 flex items-center">
                                <i class="fa-solid fa-circle-info mr-2 opacity-30 text-[8px]"></i>
                                Notes / Terms
                            </p>
                            <p class="text-[10px] text-slate-500 leading-relaxed font-medium">
                                {!! nl2br(e($invoice->notes)) !!}
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Right: Calculations & Signature -->
                <div class="w-full md:w-80 space-y-2">
                    <div class="bg-slate-50 rounded-2xl p-4 space-y-2">
                        @php 
                            $subtotal = $invoice->items->sum('subtotal');
                            $discountAmount = ($invoice->discount_type === 'percent') 
                                ? ($subtotal * ($invoice->discount / 100)) 
                                : $invoice->discount;
                            $taxAmount = ($invoice->tax_type === 'percent')
                                ? (($subtotal - $discountAmount) * ($invoice->tax / 100))
                                : $invoice->tax;
                        @endphp
                        
                        <div class="flex justify-between items-center text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            <span>Subtotal</span>
                            <span class="text-slate-900 tracking-tight">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        
                        @if($invoice->discount > 0)
                            <div class="flex justify-between items-center text-[10px] font-bold text-blue-500 uppercase tracking-widest">
                                <span>Discount</span>
                                <span class="tracking-tight">- Rp {{ number_format($discountAmount, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        @if($invoice->tax > 0)
                            <div class="flex justify-between items-center text-[10px] font-bold text-emerald-500 uppercase tracking-widest">
                                <span>Tax</span>
                                <span class="tracking-tight">+ Rp {{ number_format($taxAmount, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        <div class="pt-4 mt-2 border-t border-slate-200 flex justify-between items-center">
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-900">Total</span>
                            <span class="text-2xl font-black text-blue-600 tracking-tight">
                                <span class="text-sm align-top mr-1">Rp</span>{{ number_format($invoice->total_amount, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <div class="text-center pt-1">
                        @if($settings->invoice_signature)
                            <img src="{{ $settings->invoice_signature_url }}" class="h-12 mx-auto mb-1 object-contain opacity-90 grayscale hover:grayscale-0 transition-all cursor-crosshair">
                        @else
                            <div class="h-12 w-24 border-b-2 border-dashed border-slate-100 mx-auto mb-1 flex items-center justify-center">
                                <span class="text-[8px] uppercase tracking-widest text-slate-200">Signature</span>
                            </div>
                        @endif
                        <p class="text-[12px] font-black text-slate-900 uppercase tracking-tighter mb-0.5">{{ $settings->invoice_signer_name ?: 'Owner of FKStudio' }}</p>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest opacity-60">{{ $settings->invoice_signer_title ?: 'Authorized Representative' }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-slate-50 text-center">
                <p class="text-[9px] font-black text-slate-200 uppercase tracking-[0.5em] mb-2">Thank you for your business</p>
                <div class="flex justify-center space-x-4">
                    <span class="w-1.5 h-1.5 bg-blue-600 rounded-full opacity-20"></span>
                    <span class="w-1.5 h-1.5 bg-blue-600 rounded-full opacity-20"></span>
                    <span class="w-1.5 h-1.5 bg-blue-600 rounded-full opacity-20"></span>
                </div>
            </div>
        </div>
    </div>
    <script>
        function copyShareLink() {
            const link = "{{ route('invoices.public.show', $invoice->id) }}";
            navigator.clipboard.writeText(link).then(() => {
                alert('Public share link copied to clipboard!');
            }).catch(err => {
                console.error('Failed to copy: ', err);
            });
        }
    </script>
</body>
</html>


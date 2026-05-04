<x-app-layout>
    <x-slot name="header">
        {{ __('Invoice Management') }}
    </x-slot>

    <div class="space-y-8 pb-20">
        <!-- Quick Actions & Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6" data-aos="fade-down">
            <div class="glass p-8 rounded-[2.5rem] flex items-center justify-between group hover:bg-emerald-500/5 transition-all">
                <div>
                    <p class="text-xs font-black uppercase tracking-widest text-slate-400 mb-1">Total Generated</p>
                    <h4 class="text-3xl font-black text-slate-900">{{ count($invoices) }}</h4>
                </div>
                <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-file-invoice"></i>
                </div>
            </div>
            
            <div class="md:col-span-2 glass p-8 rounded-[2.5rem] flex items-center justify-between group hover:bg-blue-600/5 transition-all">
                <div class="max-w-md">
                    <h4 class="text-xl font-black text-slate-900 mb-1">Generate New Invoice</h4>
                    <p class="text-xs text-slate-500 font-medium">Create professional invoices for your clients in seconds with custom branding.</p>
                </div>
                <a href="{{ route('dashboard.invoices.create') }}" 
                    class="px-8 py-4 bg-slate-900 text-white font-black rounded-2xl hover:bg-black hover:scale-105 transition-all shadow-xl shadow-slate-900/20 text-xs uppercase tracking-widest">
                    <i class="fa-solid fa-plus mr-2"></i> Create New
                </a>
            </div>
        </div>

        <!-- Invoices List -->
        <div class="glass rounded-[3rem] overflow-hidden shadow-xl" data-aos="fade-up">
            <div class="p-8 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <h3 class="text-lg font-black text-slate-900 uppercase tracking-widest flex items-center">
                    <span class="w-2 h-2 bg-blue-500 rounded-full mr-3 animate-pulse"></span>
                    Recent Invoices
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Number</th>
                            <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Client</th>
                            <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Date</th>
                            <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Total</th>
                            <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Status</th>
                            <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($invoices as $invoice)
                            <tr class="group hover:bg-slate-50/50 transition-colors">
                                <td class="px-8 py-6">
                                    <span class="font-black text-slate-900">{{ $invoice->invoice_number }}</span>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-900">{{ $invoice->client_name }}</span>
                                        <span class="text-[10px] text-slate-400 font-medium">{{ $invoice->client_email }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="text-sm font-medium text-slate-600">{{ $invoice->issue_date->format('M d, Y') }}</span>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="font-black text-blue-600">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest
                                        @if($invoice->status == 'Paid') bg-emerald-100 text-emerald-600
                                        @elseif($invoice->status == 'Sent') bg-blue-100 text-blue-600
                                        @else bg-slate-100 text-slate-500 @endif">
                                        {{ $invoice->status }}
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('dashboard.invoices.show', $invoice->id) }}" target="_blank"
                                            class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-500 hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all shadow-sm" title="Preview">
                                            <i class="fa-solid fa-eye text-xs"></i>
                                        </a>
                                        <a href="{{ route('dashboard.invoices.download', $invoice->id) }}"
                                            class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-emerald-500 hover:bg-emerald-600 hover:text-white hover:border-emerald-600 transition-all shadow-sm" title="Download PDF">
                                            <i class="fa-solid fa-download text-xs"></i>
                                        </a>
                                        <a href="{{ route('dashboard.invoices.edit', $invoice->id) }}"
                                            class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-500 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all shadow-sm">
                                            <i class="fa-solid fa-pencil text-xs"></i>
                                        </a>
                                        <form action="{{ route('dashboard.invoices.destroy', $invoice->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" onclick="return confirm('Delete this invoice?')"
                                                class="w-10 h-10 bg-red-50 text-red-500 rounded-xl flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-sm">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4 text-slate-200 text-3xl">
                                            <i class="fa-solid fa-file-circle-minus"></i>
                                        </div>
                                        <p class="text-slate-400 font-bold">No invoices found. Create your first one!</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

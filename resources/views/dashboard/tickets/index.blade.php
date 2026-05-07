<x-app-layout>
    <x-slot name="header">
        {{ __('Confirmation Tickets') }}
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="glass rounded-[2rem] overflow-hidden shadow-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="p-6 text-xs font-black uppercase tracking-widest text-slate-400">Date</th>
                            <th class="p-6 text-xs font-black uppercase tracking-widest text-slate-400">Customer</th>
                            <th class="p-6 text-xs font-black uppercase tracking-widest text-slate-400">Subject / Order</th>
                            <th class="p-6 text-xs font-black uppercase tracking-widest text-slate-400">Proof</th>
                            <th class="p-6 text-xs font-black uppercase tracking-widest text-slate-400">Status</th>
                            <th class="p-6 text-xs font-black uppercase tracking-widest text-slate-400 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($tickets as $ticket)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="p-6 text-xs text-slate-500">
                                    {{ $ticket->created_at->format('d M Y H:i') }}
                                </td>
                                <td class="p-6">
                                    <div class="font-bold text-slate-900">{{ $ticket->user->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $ticket->user->email }}</div>
                                </td>
                                <td class="p-6">
                                    <div class="font-bold text-slate-900">{{ $ticket->subject }}</div>
                                    @if($ticket->order)
                                        <div class="text-[10px] text-blue-500 font-black uppercase tracking-widest">
                                            Order: #{{ substr($ticket->order->id, 0, 8) }} ({{ $ticket->order->package_price }})
                                        </div>
                                    @endif
                                    <div class="mt-2 text-sm text-slate-600 line-clamp-2 italic">"{{ $ticket->message }}"</div>
                                </td>
                                <td class="p-6">
                                    @if($ticket->attachment)
                                        <a href="{{ Storage::url($ticket->attachment) }}" target="_blank" class="block w-16 h-16 rounded-xl overflow-hidden border border-slate-200 group relative">
                                            <img src="{{ Storage::url($ticket->attachment) }}" class="w-full h-full object-cover">
                                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                                <i class="fa-solid fa-eye text-white text-xs"></i>
                                            </div>
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-400 italic">No attachment</span>
                                    @endif
                                </td>
                                <td class="p-6">
                                    @php
                                        $statusClasses = [
                                            'open' => 'bg-blue-100 text-blue-600',
                                            'in_progress' => 'bg-amber-100 text-amber-600',
                                            'resolved' => 'bg-emerald-100 text-emerald-600',
                                            'closed' => 'bg-slate-100 text-slate-600',
                                        ];
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $statusClasses[$ticket->status] ?? 'bg-slate-100 text-slate-600' }}">
                                        {{ str_replace('_', ' ', $ticket->status) }}
                                    </span>
                                </td>
                                <td class="p-6 text-right">
                                    <form action="{{ route('dashboard.tickets.update', $ticket->id) }}" method="POST" class="inline-flex items-center space-x-2">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" onchange="this.form.submit()" class="text-xs border-none bg-slate-100 rounded-lg focus:ring-2 focus:ring-blue-500">
                                            @foreach(['open', 'in_progress', 'resolved', 'closed'] as $status)
                                                <option value="{{ $status }}" {{ $ticket->status == $status ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

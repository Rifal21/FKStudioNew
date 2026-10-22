<x-app-layout>
    <x-slot name="header">Tiket Konfirmasi</x-slot>

    @php
        $ticketStatuses = [
            'open' => 'Terbuka',
            'in_progress' => 'Diproses',
            'resolved' => 'Selesai',
            'closed' => 'Ditutup',
        ];
        $ticketVariants = [
            'open' => 'info',
            'in_progress' => 'warning',
            'resolved' => 'success',
            'closed' => 'neutral',
        ];
    @endphp

    <div class="mx-auto max-w-7xl space-y-6">
        <x-ui.page-header title="Tiket Konfirmasi" description="Bukti transfer dan permintaan konfirmasi dari pelanggan.">
            <x-slot:actions>
                <x-ui.badge variant="brand">{{ $tickets->count() }} tiket</x-ui.badge>
            </x-slot:actions>
        </x-ui.page-header>

        @if ($tickets->isEmpty())
            <x-ui.card>
                <x-ui.empty-state icon="fa-ticket" title="Belum ada tiket" text="Tiket konfirmasi dari pelanggan akan muncul di sini." />
            </x-ui.card>
        @else
            {{-- Desktop table --}}
            <div class="table-wrap hidden lg:block">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Pelanggan</th>
                            <th scope="col">Subjek / Pesanan</th>
                            <th scope="col">Bukti</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-right">Ubah status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tickets as $ticket)
                            <tr>
                                <td class="whitespace-nowrap text-muted">{{ $ticket->created_at->format('d M Y H:i') }}</td>
                                <td>
                                    <div class="font-semibold">{{ $ticket->user->name }}</div>
                                    <div class="text-caption text-muted">{{ $ticket->user->email }}</div>
                                </td>
                                <td class="max-w-sm">
                                    <div class="font-semibold">{{ $ticket->subject }}</div>
                                    @if ($ticket->order)
                                        <div class="text-caption font-semibold text-brand-text">
                                            Pesanan #{{ substr($ticket->order->id, 0, 8) }} ({{ $ticket->order->package_price }})
                                        </div>
                                    @endif
                                    <p class="mt-1 line-clamp-2 text-small italic text-muted">"{{ $ticket->message }}"</p>
                                </td>
                                <td>
                                    @if ($ticket->attachment)
                                        <a href="{{ Storage::url($ticket->attachment) }}" target="_blank" rel="noopener"
                                           class="group relative block h-16 w-16 overflow-hidden rounded-md border border-line transition hover:border-brand-500"
                                           aria-label="Buka bukti: {{ $ticket->subject }}">
                                            <img src="{{ Storage::url($ticket->attachment) }}" alt="" loading="lazy" class="h-full w-full object-cover">
                                            <span class="absolute inset-0 flex items-center justify-center bg-ink/50 text-small text-white opacity-0 transition-opacity group-hover:opacity-100">
                                                <i class="fa-solid fa-eye" aria-hidden="true"></i>
                                            </span>
                                        </a>
                                    @else
                                        <span class="text-small italic text-muted">Tanpa lampiran</span>
                                    @endif
                                </td>
                                <td>
                                    <x-ui.badge :variant="$ticketVariants[$ticket->status] ?? 'neutral'" dot>
                                        {{ $ticketStatuses[$ticket->status] ?? str_replace('_', ' ', $ticket->status) }}
                                    </x-ui.badge>
                                </td>
                                <td class="text-right">
                                    <form action="{{ route('dashboard.tickets.update', $ticket->id) }}" method="POST" class="ml-auto w-40">
                                        @csrf
                                        @method('PATCH')
                                        <label for="ticket-status-d-{{ $ticket->id }}" class="sr-only">Status tiket</label>
                                        <select id="ticket-status-d-{{ $ticket->id }}" name="status" onchange="this.form.requestSubmit()" class="select">
                                            @foreach ($ticketStatuses as $value => $label)
                                                <option value="{{ $value }}" {{ $ticket->status == $value ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile cards --}}
            <div class="space-y-4 lg:hidden">
                @foreach ($tickets as $ticket)
                    <article class="card card-pad space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-caption text-muted">{{ $ticket->created_at->format('d M Y H:i') }}</div>
                                <div class="font-bold">{{ $ticket->user->name }}</div>
                                <div class="break-all text-caption text-muted">{{ $ticket->user->email }}</div>
                            </div>
                            <x-ui.badge :variant="$ticketVariants[$ticket->status] ?? 'neutral'" dot>
                                {{ $ticketStatuses[$ticket->status] ?? str_replace('_', ' ', $ticket->status) }}
                            </x-ui.badge>
                        </div>

                        <div>
                            <div class="font-semibold">{{ $ticket->subject }}</div>
                            @if ($ticket->order)
                                <div class="text-caption font-semibold text-brand-text">
                                    Pesanan #{{ substr($ticket->order->id, 0, 8) }} ({{ $ticket->order->package_price }})
                                </div>
                            @endif
                            <p class="mt-1 line-clamp-3 text-small italic text-muted">"{{ $ticket->message }}"</p>
                        </div>

                        @if ($ticket->attachment)
                            <a href="{{ Storage::url($ticket->attachment) }}" target="_blank" rel="noopener"
                               class="block h-32 w-full overflow-hidden rounded-md border border-line" aria-label="Buka bukti: {{ $ticket->subject }}">
                                <img src="{{ Storage::url($ticket->attachment) }}" alt="Bukti transfer" loading="lazy" class="h-full w-full object-cover">
                            </a>
                        @else
                            <p class="text-small italic text-muted">Tanpa lampiran</p>
                        @endif

                        <form action="{{ route('dashboard.tickets.update', $ticket->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <label for="ticket-status-m-{{ $ticket->id }}" class="label">Ubah status</label>
                            <select id="ticket-status-m-{{ $ticket->id }}" name="status" onchange="this.form.requestSubmit()" class="select">
                                @foreach ($ticketStatuses as $value => $label)
                                    <option value="{{ $value }}" {{ $ticket->status == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </form>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>

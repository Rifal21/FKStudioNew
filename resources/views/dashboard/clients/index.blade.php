<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Clients') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-gray-900">

            @if (session('success'))
                <div class="mb-6 px-4 py-3 bg-green-100 text-green-800 rounded-xl font-medium">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Add Client Form -->
            <div class="bg-white p-6 shadow rounded-lg mb-8">
                <h3 class="text-lg font-bold mb-4 border-b pb-2">Add Client Logo</h3>
                <form action="{{ route('dashboard.clients.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Client Name <span
                                    class="text-gray-400 text-xs">(optional)</span></label>
                            <input type="text" name="name" placeholder="e.g. Google"
                                class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Website URL <span
                                    class="text-gray-400 text-xs">(optional)</span></label>
                            <input type="url" name="url" placeholder="https://..."
                                class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Logo File <span
                                    class="text-red-500">*</span></label>
                            <input type="file" name="logo" required accept="image/*"
                                class="w-full border-gray-300 rounded-md text-sm">
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Recommended: PNG with transparent background, minimum
                        200×80px. Max 2MB.</p>
                    <div class="mt-4">
                        <button type="submit"
                            class="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 shadow text-sm uppercase tracking-widest transition-all">
                            + Add Client
                        </button>
                    </div>
                </form>
            </div>

            <!-- Clients Grid -->
            <div class="bg-white p-6 shadow rounded-lg">
                <h3 class="text-lg font-bold mb-6 border-b pb-2">Client Logos ({{ $clients->count() }})</h3>

                @if ($clients->isEmpty())
                    <div class="text-center py-12 text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-sm font-medium">No client logos yet. Add one above.</p>
                    </div>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach ($clients as $client)
                            <div
                                class="relative group border border-gray-100 rounded-xl p-3 flex flex-col items-center bg-gray-50 hover:border-blue-200 transition-all">
                                @if ($client->url)
                                    <a href="{{ $client->url }}" target="_blank" class="block w-full">
                                @endif
                                <img src="{{ asset('storage/' . $client->logo) }}"
                                    alt="{{ $client->name ?? 'Client Logo' }}" class="h-12 w-full object-contain mb-2">
                                @if ($client->url)
                                    </a>
                                @endif
                                @if ($client->name)
                                    <p class="text-xs text-gray-500 text-center truncate w-full">{{ $client->name }}</p>
                                @endif

                                <!-- Delete button -->
                                <form action="{{ route('dashboard.clients.destroy', $client->id) }}" method="POST"
                                    class="absolute -top-2 -right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('Remove this client logo?')"
                                        class="w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center shadow text-xs font-bold leading-none">
                                        ✕
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>

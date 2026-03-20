<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Services') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-gray-900">

            <div class="bg-white p-6 shadow rounded-lg mb-8">
                <h3 class="text-lg font-bold mb-4">Add New Service</h3>
                <form action="{{ route('dashboard.services.store') }}" method="POST"
                    class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium">Title (ID)</label>
                        <input type="text" name="title_id" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Title (EN)</label>
                        <input type="text" name="title_en" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Description (ID)</label>
                        <textarea name="description_id" required rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Description (EN)</label>
                        <textarea name="description_en" required rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>
                    <div class="col-span-1 md:col-span-2" x-data="{
                        icon: 'fa-solid fa-star',
                        search: '',
                        icons: [
                            'fa-solid fa-code', 'fa-solid fa-laptop-code', 'fa-solid fa-mobile-screen',
                            'fa-solid fa-paint-roller', 'fa-solid fa-palette', 'fa-solid fa-pen-nib',
                            'fa-solid fa-bullhorn', 'fa-solid fa-chart-line', 'fa-solid fa-magnifying-glass',
                            'fa-solid fa-server', 'fa-solid fa-database', 'fa-solid fa-cloud',
                            'fa-solid fa-shield-halved', 'fa-solid fa-robot', 'fa-solid fa-microchip',
                            'fa-solid fa-desktop', 'fa-solid fa-earth-americas', 'fa-solid fa-rocket',
                            'fa-solid fa-cart-shopping', 'fa-solid fa-store', 'fa-solid fa-camera'
                        ],
                        get filteredIcons() {
                            if (this.search === '') return this.icons;
                            return this.icons.filter(i => i.toLowerCase().includes(this.search.toLowerCase()));
                        }
                    }">
                        <label class="block text-sm font-medium text-gray-700">Icon Selection</label>
                        <div
                            class="mt-1 p-4 border border-gray-200 rounded-md bg-gray-50 flex flex-col md:flex-row gap-4">
                            <div class="flex-1">
                                <label class="text-xs text-gray-500 mb-1 block">Live Preview & Custom Class</label>
                                <div class="flex space-x-3">
                                    <div
                                        class="w-12 h-10 border border-gray-300 rounded-md flex items-center justify-center bg-white text-blue-600 shadow-sm">
                                        <i :class="icon" class="text-lg"></i>
                                    </div>
                                    <input type="text" name="icon" x-model="icon" required
                                        placeholder="fa-solid fa-star"
                                        class="flex-1 block w-full border-gray-300 rounded-md shadow-sm text-sm"
                                        title="You can type any FontAwesome 6 class here">
                                </div>
                                <p class="mt-2 text-xs text-gray-500">
                                    You can paste any class from <a href="https://fontawesome.com/search?o=r&m=free"
                                        target="_blank"
                                        class="text-blue-500 font-bold underline hover:text-blue-700">FontAwesome Free
                                        Icons</a>.
                                </p>
                            </div>

                            <div class="flex-1">
                                <label class="text-xs text-gray-500 mb-1 block">Quick Select</label>
                                <input type="text" x-model="search" placeholder="Filter quick icons..."
                                    class="mb-2 block w-full border-gray-300 rounded-md shadow-sm text-xs px-2 py-1">
                                <div class="flex flex-wrap gap-2 max-h-24 overflow-y-auto p-1">
                                    <template x-for="i in filteredIcons" :key="i">
                                        <button type="button" @click="icon = i"
                                            class="w-8 h-8 rounded border flex items-center justify-center transition-all bg-white shadow-sm"
                                            :class="icon === i ? 'border-blue-500 text-blue-600 ring-1 ring-blue-500' :
                                                'border-gray-200 text-gray-600 hover:bg-blue-50 hover:border-blue-300'">
                                            <i :class="i"></i>
                                        </button>
                                    </template>
                                    <div x-show="filteredIcons.length === 0" class="text-xs text-gray-400 italic py-1">
                                        No icons match. Type class manually on the left.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-1 md:col-span-2 flex items-end mt-2">
                        <button type="submit"
                            class="w-full py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition shadow-md">Add
                            Service</button>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 uppercase tracking-widest text-xs font-bold">
                        <tr>
                            <th class="px-6 py-3 text-left text-gray-500">Service Info</th>
                            <th class="px-6 py-3 text-left">Icon</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($services as $service)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $service->title_id }}</div>
                                    <div class="text-sm text-gray-500">{{ $service->title_en }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-1">
                                        <i
                                            class="{{ str_starts_with($service->icon, 'fa-') ? $service->icon : 'fa-solid fa-' . ($service->icon == 'swatch' ? 'palette' : ($service->icon == 'code-bracket' ? 'code' : 'rocket')) }} text-2xl text-blue-600"></i>
                                        <span class="text-[10px] text-gray-500 font-mono">{{ $service->icon }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right flex justify-end space-x-2">
                                    <form action="{{ route('dashboard.services.destroy', $service->id) }}"
                                        method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('Silahkan konfirmasi penghapusan?')"
                                            class="text-red-600 hover:text-red-900 font-bold uppercase text-xs">Delete</button>
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

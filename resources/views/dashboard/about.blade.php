<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('About Section Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-gray-900">

            <form action="{{ route('dashboard.about.update') }}" method="POST" enctype="multipart/form-data"
                class="space-y-6">
                @csrf
                <div class="bg-white p-6 shadow rounded-lg">
                    <h3 class="text-lg font-bold mb-6 border-b pb-2">About Content (Multilingual)</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Indonesian Section -->
                        <div class="space-y-4 border-r pr-8">
                            <span
                                class="inline-block px-3 py-1 bg-blue-100 text-blue-600 rounded text-xs font-bold uppercase mb-2 leading-none">Indonesian</span>
                            <div>
                                <label class="block text-sm font-medium">Title (ID)</label>
                                <input type="text" name="title_id" value="{{ $about->title_id }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Description (ID)</label>
                                <textarea name="description_id" rows="5" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ $about->description_id }}</textarea>
                            </div>
                        </div>

                        <!-- English Section -->
                        <div class="space-y-4">
                            <span
                                class="inline-block px-3 py-1 bg-indigo-100 text-indigo-600 rounded text-xs font-bold uppercase mb-2 leading-none">English</span>
                            <div>
                                <label class="block text-sm font-medium">Title (EN)</label>
                                <input type="text" name="title_en" value="{{ $about->title_en }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Description (EN)</label>
                                <textarea name="description_en" rows="5" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ $about->description_en }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 shadow rounded-lg mt-6">
                    <h3 class="text-lg font-bold mb-4 border-b pb-2">About Section Media</h3>
                    <div>
                        <label class="block text-sm font-medium">Main Image</label>
                        <input type="file" name="image"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>

                <!-- Stats -->
                <div class="bg-white p-6 shadow rounded-lg mt-6" x-data="{ stats: {{ $about->stats ?? '[]' }} }">
                    <h3 class="text-lg font-bold mb-4 border-b pb-2">Business Stats</h3>
                    <div class="space-y-4">
                        <template x-for="(stat, index) in stats" :key="index">
                            <div class="grid grid-cols-3 gap-4 border p-4 rounded-xl relative">
                                <div>
                                    <label class="text-xs font-bold uppercase text-gray-400">Label (ID)</label>
                                    <input type="text" name="stats_labels_id[]" :value="stat.label_id"
                                        class="w-full border-gray-300 rounded-md">
                                </div>
                                <div>
                                    <label class="text-xs font-bold uppercase text-gray-400">Label (EN)</label>
                                    <input type="text" name="stats_labels_en[]" :value="stat.label_en"
                                        class="w-full border-gray-300 rounded-md">
                                </div>
                                <div>
                                    <label class="text-xs font-bold uppercase text-gray-400">Value</label>
                                    <input type="text" name="stats_values[]" :value="stat.value"
                                        class="w-full border-gray-300 rounded-md">
                                </div>
                                <button type="button" @click="stats.splice(index, 1)"
                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 opacity-0 hover:opacity-100 transition-opacity">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </template>
                        <button type="button" @click="stats.push({label_id: '', label_en: '', value: ''})"
                            class="w-full py-2 border-2 border-dashed border-gray-200 text-gray-500 rounded-xl hover:bg-gray-50 transition-colors uppercase font-bold text-xs">+
                            Add Statistic</button>
                    </div>
                </div>

                <div class="flex justify-end mt-8">
                    <button type="submit"
                        class="px-8 py-3 bg-blue-600 text-white font-black rounded-xl hover:bg-blue-700 shadow-xl shadow-blue-500/20 transition-all uppercase tracking-widest text-sm">Update
                        About Section</button>
                </div>
            </form>

            <div class="mt-12 bg-white p-6 shadow rounded-lg">
                <h3 class="text-lg font-bold mb-6 border-b pb-2">About Slides (Left-side Image Slider)</h3>
                <form action="{{ route('dashboard.about.slides.store') }}" method="POST" enctype="multipart/form-data"
                    class="mb-8">
                    @csrf
                    <div class="flex items-center space-x-4">
                        <input type="file" name="image" required class="flex-1 border-gray-300 rounded-md">
                        <button type="submit"
                            class="px-6 py-2 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 shadow-lg">Add
                            Slide</button>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Recommended aspect ratio: 4:3 or similar for the left-side
                        About
                        slider.</p>
                </form>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    @foreach ($slides as $slide)
                        <div class="relative group rounded-xl overflow-hidden shadow-sm border border-gray-200">
                            <img src="{{ asset('storage/' . $slide->image) }}" class="w-full h-32 object-cover">
                            <div
                                class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <form action="{{ route('dashboard.about.slides.destroy', $slide->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        onclick="return confirm('Silahkan konfirmasi penghapusan slide?')"
                                        class="text-white bg-red-600 hover:bg-red-700 font-bold px-4 py-2 rounded-lg text-xs uppercase tracking-widest">Delete</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

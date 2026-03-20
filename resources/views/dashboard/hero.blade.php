<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hero Section Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-gray-900">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                <!-- Left Column: Settings Form -->
                <div class="lg:col-span-2 space-y-6">
                    <form action="{{ route('dashboard.hero.update') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-6">
                        @csrf
                        <div class="bg-white p-6 shadow rounded-lg">
                            <h3 class="text-lg font-bold mb-6 border-b pb-2">Main Heading & Subtitle</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <!-- Indonesian Section -->
                                <div class="space-y-4 border-r pr-8">
                                    <span
                                        class="inline-block px-3 py-1 bg-blue-100 text-blue-600 rounded text-xs font-bold uppercase mb-2 leading-none">Indonesian
                                        (Primary)</span>
                                    <div>
                                        <label class="block text-sm font-medium">Hero Title (ID)</label>
                                        <input type="text" name="title_id" value="{{ $hero->title_id }}"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium">Hero Subtitle (ID)</label>
                                        <textarea name="subtitle_id" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ $hero->subtitle_id }}</textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium">CTA Button Text (ID)</label>
                                        <input type="text" name="cta_text_id" value="{{ $hero->cta_text_id }}"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    </div>
                                </div>

                                <!-- English Section -->
                                <div class="space-y-4">
                                    <span
                                        class="inline-block px-3 py-1 bg-indigo-100 text-indigo-600 rounded text-xs font-bold uppercase mb-2 leading-none">English</span>
                                    <div>
                                        <label class="block text-sm font-medium">Hero Title (EN)</label>
                                        <input type="text" name="title_en" value="{{ $hero->title_en }}"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium">Hero Subtitle (EN)</label>
                                        <textarea name="subtitle_en" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ $hero->subtitle_en }}</textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium">CTA Button Text (EN)</label>
                                        <input type="text" name="cta_text_en" value="{{ $hero->cta_text_en }}"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6">
                                <label class="block text-sm font-medium">CTA Link (URL)</label>
                                <input type="text" name="cta_link" value="{{ $hero->cta_link }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                    placeholder="#contact">
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="px-8 py-3 bg-blue-600 text-white font-black rounded-xl hover:bg-blue-700 shadow-xl shadow-blue-500/20 transition-all uppercase tracking-widest text-sm">Update
                                Hero Text</button>
                        </div>
                    </form>
                </div>

                <!-- Right Column: Slider Management -->
                <div class="space-y-6">
                    <div class="bg-white p-6 shadow rounded-lg">
                        <h3 class="text-lg font-bold mb-6 border-b pb-2">Background Slider</h3>

                        <form action="{{ route('dashboard.hero.slides.store') }}" method="POST"
                            enctype="multipart/form-data"
                            class="mb-8 p-4 bg-slate-50 rounded-xl border-2 border-dashed">
                            @csrf
                            <label class="block text-sm font-bold uppercase tracking-widest text-slate-500 mb-2">Add New
                                Slide</label>
                            <input type="file" name="image" required
                                class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 mb-4">
                            <button type="submit"
                                class="w-full py-2 bg-slate-900 text-white font-bold rounded-lg hover:bg-black transition-colors">Upload
                                Slide</button>
                        </form>

                        <div class="space-y-4">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Active Slides</p>
                            @forelse($slides as $slide)
                                <div class="relative group rounded-xl overflow-hidden border">
                                    <img src="{{ asset('storage/' . $slide->image) }}"
                                        class="w-full h-32 object-cover">
                                    <div
                                        class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <form action="{{ route('dashboard.hero.slides.destroy', $slide) }}"
                                            method="POST" onsubmit="return confirm('Hapus slide ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="px-4 py-2 bg-red-600 text-white rounded-lg font-bold text-xs uppercase tracking-widest">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-slate-400 italic text-sm">No slider images yet. Using
                                    default.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

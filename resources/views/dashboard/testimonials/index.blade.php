<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Testimonials') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{
        openAdd: false,
        openEdit: false,
        editData: {
            id: '',
            name: '',
            role_id: '',
            role_en: '',
            content_id: '',
            content_en: '',
            rating: 5,
            action: ''
        },
        startEdit(tm) {
            this.editData = {
                id: tm.id,
                name: tm.name,
                role_id: tm.role_id,
                role_en: tm.role_en,
                content_id: tm.content_id,
                content_en: tm.content_en,
                rating: tm.rating || 5,
                action: '{{ url('/dashboard/testimonials') }}/' + tm.id
            };
            this.openEdit = true;
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-gray-900">

            @if (session('success'))
                <div class="mb-6 px-4 py-3 bg-green-100 text-green-800 rounded-xl font-medium">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Add Testimonial Form -->
            <div class="bg-white p-6 shadow rounded-lg mb-8">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold uppercase tracking-widest text-slate-400">Add Testimonial</h3>
                    <button @click="openAdd = !openAdd"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold uppercase tracking-widest hover:bg-blue-700 transition-all">
                        <span x-show="!openAdd">+ New</span>
                        <span x-show="openAdd">Close</span>
                    </button>
                </div>

                <div x-show="openAdd" x-transition class="mt-4 border-t pt-6">
                    <form action="{{ route('dashboard.testimonials.store') }}" method="POST"
                        enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label
                                    class="block text-sm font-bold uppercase tracking-widest text-gray-700 mb-1">Client
                                    Name</label>
                                <input type="text" name="name" required placeholder="e.g. John Doe"
                                    class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-bold uppercase tracking-widest text-gray-700 mb-1">Avatar
                                    (Optional)</label>
                                <input type="file" name="avatar" accept="image/*"
                                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-bold uppercase tracking-widest text-gray-700 mb-1">Rating</label>
                                <select name="rating"
                                    class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="5">5 - Excellent</option>
                                    <option value="4">4 - Very Good</option>
                                    <option value="3">3 - Good</option>
                                    <option value="2">2 - Fair</option>
                                    <option value="1">1 - Poor</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <h4 class="font-bold text-blue-600 border-b pb-2">Indonesian (ID)</h4>
                                <div>
                                    <label
                                        class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-1">Role
                                        (ID)</label>
                                    <input type="text" name="role_id" required placeholder="e.g. CEO of Tech"
                                        class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label
                                        class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-1">Content
                                        (ID)</label>
                                    <textarea name="content_id" required rows="3" placeholder="Testimoni dalam Bahasa Indonesia..."
                                        class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                                </div>
                            </div>
                            <div class="space-y-4 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <h4 class="font-bold text-indigo-600 border-b pb-2">English (EN)</h4>
                                <div>
                                    <label
                                        class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-1">Role
                                        (EN)</label>
                                    <input type="text" name="role_en" required placeholder="e.g. CEO of Tech"
                                        class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label
                                        class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-1">Content
                                        (EN)</label>
                                    <textarea name="content_en" required rows="3" placeholder="Testimonial in English..."
                                        class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end pt-4">
                            <button type="submit"
                                class="px-8 py-2 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 shadow-lg">Save
                                New</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- List -->
            <div class="grid md:grid-cols-2 gap-6">
                @forelse($testimonials as $tm)
                    <div
                        class="p-6 border border-gray-100 rounded-3xl relative bg-white shadow-sm hover:shadow-md transition-all group">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center space-x-4">
                                @if ($tm->avatar)
                                    <img src="{{ asset('storage/' . $tm->avatar) }}"
                                        class="w-12 h-12 rounded-2xl object-cover shadow-sm">
                                @else
                                    <div
                                        class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center font-extrabold text-blue-600 text-xl border border-blue-100">
                                        {{ substr($tm->name, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="font-bold text-gray-900">{{ $tm->name }}</div>
                                    <div class="text-xs text-blue-600 font-bold uppercase tracking-widest">
                                        {{ $tm->role_id }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center text-yellow-400">
                                @for ($i = 0; $i < $tm->rating; $i++)
                                    <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20">
                                        <path
                                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                    </svg>
                                @endfor
                            </div>
                        </div>
                        <div class="mt-4 space-y-2 opacity-60 group-hover:opacity-100 transition-opacity">
                            <p class="text-sm italic">"{{ $tm->content_id }}"</p>
                        </div>

                        <!-- Actions -->
                        <div class="mt-6 flex space-x-2">
                            <button @click="startEdit({{ $tm->toJson() }})"
                                class="flex-1 py-2 bg-slate-50 text-slate-600 text-xs font-bold rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-all border border-slate-100">
                                EDIT
                            </button>
                            <form action="{{ route('dashboard.testimonials.destroy', $tm->id) }}" method="POST"
                                onsubmit="return confirm('Hapus testimoni ini?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="w-10 h-10 flex items-center justify-center bg-red-50 text-red-400 rounded-xl hover:bg-red-500 hover:text-white transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="md:col-span-2 text-center py-12 text-gray-400">Belum ada testimoni.</div>
                @endforelse
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="openEdit" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
            <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm" @click="openEdit = false"></div>

            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl relative z-10 overflow-hidden"
                x-show="openEdit" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                    <h2 class="text-xl font-bold text-slate-800">Edit Testimonial</h2>
                    <button @click="openEdit = false" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>

                <form :action="editData.action" method="POST" enctype="multipart/form-data"
                    class="p-8 space-y-6 max-h-[80vh] overflow-y-auto">
                    @csrf @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Client
                                Name</label>
                            <input type="text" name="name" x-model="editData.name" required
                                class="w-full border-slate-200 rounded-xl">
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Rating</label>
                            <select name="rating" x-model="editData.rating"
                                class="w-full border-slate-200 rounded-xl">
                                <option value="5">5 Stars</option>
                                <option value="4">4 Stars</option>
                                <option value="3">3 Stars</option>
                                <option value="2">2 Stars</option>
                                <option value="1">1 Star</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Avatar
                            (Optional - Leave blank to keep current)</label>
                        <input type="file" name="avatar" class="w-full text-sm">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <h4 class="text-sm font-bold text-blue-600">ID Version</h4>
                            <input type="text" name="role_id" x-model="editData.role_id" required
                                placeholder="Role (ID)" class="w-full border-slate-200 rounded-xl">
                            <textarea name="content_id" x-model="editData.content_id" required rows="3" placeholder="Content (ID)"
                                class="w-full border-slate-200 rounded-xl"></textarea>
                        </div>
                        <div class="space-y-4">
                            <h4 class="text-sm font-bold text-indigo-600">EN Version</h4>
                            <input type="text" name="role_en" x-model="editData.role_en" required
                                placeholder="Role (EN)" class="w-full border-slate-200 rounded-xl">
                            <textarea name="content_en" x-model="editData.content_en" required rows="3" placeholder="Content (EN)"
                                class="w-full border-slate-200 rounded-xl"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-6">
                        <button type="button" @click="openEdit = false"
                            class="px-6 py-2 rounded-xl border font-bold uppercase tracking-widest text-xs">Cancel</button>
                        <button type="submit"
                            class="px-8 py-2 bg-blue-600 text-white rounded-xl font-bold uppercase tracking-widest text-xs shadow-lg shadow-blue-200">Update
                            Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</x-app-layout>

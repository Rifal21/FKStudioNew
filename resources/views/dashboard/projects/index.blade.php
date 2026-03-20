<x-app-layout>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Projects') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{
        editMode: false,
        currentProject: {},
        initEdit(project) {
            this.currentProject = project;
            this.editMode = true;
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-gray-900">

            <!-- Add Project Form -->
            <div class="bg-white p-6 shadow rounded-lg mb-8" x-show="!editMode">
                <h3 class="text-lg font-bold mb-4">Add New Project</h3>
                <form action="{{ route('dashboard.projects.store') }}" method="POST" enctype="multipart/form-data"
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
                        <label class="block text-sm font-medium">Category (ID)</label>
                        <input type="text" name="category_id" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Category (EN)</label>
                        <input type="text" name="category_en" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Description (ID)</label>
                        <textarea name="description_id" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Description (EN)</label>
                        <textarea name="description_en" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Image (Optional)</label>
                        <input type="file" name="image"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <p class="text-[10px] text-gray-400 mt-1 italic">Jika kosong, preview akan diambil otomatis dari
                            URL di bawah.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">URL Link (Optional)</label>
                        <input type="text" name="url"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                            class="w-full py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700">Add
                            Project</button>
                    </div>
                </form>
            </div>

            <!-- Edit Project Form (Modal-like) -->
            <div class="bg-blue-50 p-6 shadow rounded-lg mb-8 border-2 border-blue-200" x-show="editMode" x-cloak>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-blue-800">Edit Project: <span
                            x-text="currentProject.title_id"></span></h3>
                    <button @click="editMode = false" class="text-gray-500 hover:text-gray-700 font-bold">&times;
                        Close</button>
                </div>
                <form :action="'{{ route('dashboard.projects.index') }}/' + currentProject.id" method="POST"
                    enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="block text-sm font-medium">Title (ID)</label>
                        <input type="text" name="title_id" required x-model="currentProject.title_id"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Title (EN)</label>
                        <input type="text" name="title_en" required x-model="currentProject.title_en"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Category (ID)</label>
                        <input type="text" name="category_id" required x-model="currentProject.category_id"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Category (EN)</label>
                        <input type="text" name="category_en" required x-model="currentProject.category_en"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Description (ID)</label>
                        <textarea name="description_id" rows="2" x-model="currentProject.description_id"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Description (EN)</label>
                        <textarea name="description_en" rows="2" x-model="currentProject.description_en"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Replace Image (Optional)</label>
                        <input type="file" name="image"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">URL Link (Optional)</label>
                        <input type="text" name="url" x-model="currentProject.url"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div class="flex items-end space-x-2">
                        <button type="submit"
                            class="flex-1 py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700">Update
                            Project</button>
                        <button type="button" @click="editMode = false"
                            class="px-4 py-2 bg-gray-200 text-gray-700 font-bold rounded-lg hover:bg-gray-300">Cancel</button>
                    </div>
                </form>
            </div>

            <!-- Project List -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 uppercase tracking-widest text-xs font-bold">
                        <tr>
                            <th class="px-6 py-3 text-left text-gray-500">Preview</th>
                            <th class="px-6 py-3 text-left text-gray-500">Project Info</th>
                            <th class="px-6 py-3 text-left">Category</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($projects as $project)
                            <tr>
                                <td class="px-6 py-4">
                                    @if ($project->image)
                                        <img src="{{ asset('storage/' . $project->image) }}"
                                            class="w-16 h-10 object-cover rounded shadow-sm">
                                    @else
                                        <div
                                            class="w-16 h-10 bg-slate-100 rounded flex items-center justify-center border text-[10px] text-slate-400">
                                            No Image</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $project->title_id }}</div>
                                    <div class="text-sm text-gray-500">{{ $project->title_en }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-3 py-1 bg-blue-100 text-blue-600 rounded-lg text-xs font-bold uppercase tracking-widest">{{ $project->category_id }}</span>
                                </td>
                                <td class="px-6 py-4 text-right flex justify-end items-center space-x-4">
                                    <button @click="initEdit({{ $project->toJson() }})"
                                        class="text-blue-600 hover:text-blue-900 font-bold uppercase text-xs">Edit</button>

                                    <form action="{{ route('dashboard.projects.destroy', $project->id) }}"
                                        method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('Dihapus ya?')"
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

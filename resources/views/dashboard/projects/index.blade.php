<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Projects') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-gray-900">

            <div class="bg-white p-6 shadow rounded-lg mb-8">
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

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 uppercase tracking-widest text-xs font-bold">
                        <tr>
                            <th class="px-6 py-3 text-left text-gray-500">Project Info</th>
                            <th class="px-6 py-3 text-left">Category</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($projects as $project)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $project->title_id }}</div>
                                    <div class="text-sm text-gray-500">{{ $project->title_en }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-3 py-1 bg-blue-100 text-blue-600 rounded-lg text-xs font-bold uppercase tracking-widest">{{ $project->category_id }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form action="{{ route('dashboard.projects.destroy', $project->id) }}"
                                        method="POST">
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

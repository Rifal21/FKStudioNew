<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Owners / Team') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-gray-900">

            <div class="bg-white p-6 shadow rounded-lg mb-8">
                <h3 class="text-lg font-bold mb-4">Add New Owner</h3>
                <form action="{{ route('dashboard.owners.store') }}" method="POST" enctype="multipart/form-data"
                    class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium">Name</label>
                        <input type="text" name="name" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Image</label>
                        <input type="file" name="image" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Role (ID)</label>
                        <input type="text" name="role_id" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Role (EN)</label>
                        <input type="text" name="role_en" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Bio (ID)</label>
                        <textarea name="bio_id" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Bio (EN)</label>
                        <textarea name="bio_en" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-pink-600">Instagram URL</label>
                        <input type="url" name="instagram_url" placeholder="https://instagram.com/user"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-blue-600">LinkedIn URL</label>
                        <input type="url" name="linkedin_url" placeholder="https://linkedin.com/in/user"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                            class="w-full py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700">Add
                            Owner</button>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 uppercase tracking-widest text-xs font-bold">
                        <tr>
                            <th class="px-6 py-3 text-left">Owner Info</th>
                            <th class="px-6 py-3 text-left">Role</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($owners as $owner)
                            <tr>
                                <td class="px-6 py-4 flex items-center space-x-3">
                                    <img src="{{ asset('storage/' . $owner->image) }}"
                                        class="w-10 h-10 rounded-xl object-cover">
                                    <div>
                                        <div class="font-bold text-gray-900">{{ $owner->name }}</div>
                                        <div class="text-[10px] text-gray-500 uppercase tracking-widest">
                                            {{ $owner->getTranslation('role') }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        @if ($owner->instagram_url)
                                            <a href="{{ $owner->instagram_url }}" target="_blank"
                                                class="text-pink-600"><i class="fa-brands fa-instagram"></i></a>
                                        @endif
                                        @if ($owner->linkedin_url)
                                            <a href="{{ $owner->linkedin_url }}" target="_blank"
                                                class="text-blue-600"><i class="fa-brands fa-linkedin"></i></a>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form action="{{ route('dashboard.owners.destroy', $owner->id) }}" method="POST">
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

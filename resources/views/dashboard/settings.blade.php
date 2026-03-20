<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Site Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-gray-900">

            <form action="{{ route('dashboard.settings.update') }}" method="POST" enctype="multipart/form-data"
                class="space-y-6">
                @csrf
                <div class="bg-white p-6 shadow rounded-lg">
                    <h3 class="text-lg font-bold mb-4 border-b pb-2">General Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium">Site Name</label>
                            <input type="text" name="site_name" value="{{ $settings->site_name }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium">SEO Title</label>
                            <input type="text" name="seo_title" value="{{ $settings->seo_title }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium">SEO Description</label>
                            <textarea name="seo_description" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ $settings->seo_description }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Site Logo</label>
                            <input type="file" name="site_logo"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @if ($settings->site_logo)
                                <img src="{{ asset('storage/' . $settings->site_logo) }}"
                                    class="mt-2 h-12 bg-slate-100 p-1 rounded">
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Favicon</label>
                            <input type="file" name="site_favicon"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @if ($settings->site_favicon)
                                <img src="{{ asset('storage/' . $settings->site_favicon) }}"
                                    class="mt-2 h-8 bg-slate-100 p-1 rounded">
                            @endif
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 shadow rounded-lg">
                    <h3 class="text-lg font-bold mb-4 border-b pb-2">Footer Text (Multilingual)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-blue-600">Footer Text (ID)</label>
                            <textarea name="footer_text_id" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ $settings->footer_text_id }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-indigo-600">Footer Text (EN)</label>
                            <textarea name="footer_text_en" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ $settings->footer_text_en }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 shadow rounded-lg">
                    <h3 class="text-lg font-bold mb-4 border-b pb-2">Social Media Links</h3>
                    @php $socials = json_decode($settings->social_links, true) ?? []; @endphp
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium">Instagram URL</label>
                            <input type="text" name="social[instagram]" value="{{ $socials['instagram'] ?? '' }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                placeholder="https://instagram.com/...">
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Facebook URL</label>
                            <input type="text" name="social[facebook]" value="{{ $socials['facebook'] ?? '' }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                placeholder="https://facebook.com/...">
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Twitter (X) URL</label>
                            <input type="text" name="social[twitter]" value="{{ $socials['twitter'] ?? '' }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                placeholder="https://twitter.com/...">
                        </div>
                        <div>
                            <label class="block text-sm font-medium">LinkedIn URL</label>
                            <input type="text" name="social[linkedin]" value="{{ $socials['linkedin'] ?? '' }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                placeholder="https://linkedin.com/...">
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 shadow rounded-lg">
                    <h3 class="text-lg font-bold mb-4 border-b pb-2">Contact Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium">Email</label>
                            <input type="email" name="contact_email" value="{{ $settings->contact_email }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Phone</label>
                            <input type="text" name="contact_phone" value="{{ $settings->contact_phone }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Address</label>
                            <input type="text" name="contact_address" value="{{ $settings->contact_address }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 shadow-lg">Save
                        Changes</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

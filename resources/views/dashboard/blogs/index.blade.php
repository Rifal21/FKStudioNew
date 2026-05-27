<x-app-layout>
    <x-slot name="header">
        {{ __('Blog Post Management') }}
    </x-slot>

    <!-- jQuery & Summernote-Lite CDNs -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

    <!-- Premium Custom Styles for Summernote to match our elegant Glassmorphic UI -->
    <style>
        .note-editor.note-frame {
            border: none !important;
            background: transparent !important;
            font-family: 'Inter', sans-serif !important;
        }
        .note-toolbar {
            background: #f8fafc !important;
            border: none !important;
            border-bottom: 1px solid rgba(15, 23, 42, 0.05) !important;
            padding: 0.75rem !important;
            border-radius: 1.5rem 1.5rem 0 0 !important;
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 0.35rem !important;
        }
        .note-editable {
            background: transparent !important;
            padding: 1.5rem !important;
            font-size: 0.95rem !important;
            line-height: 1.8 !important;
            color: #1e293b !important;
            min-height: 380px !important;
        }
        .note-btn {
            background: white !important;
            border: 1px solid rgba(15, 23, 42, 0.06) !important;
            color: #475569 !important;
            border-radius: 0.65rem !important;
            padding: 0.4rem 0.75rem !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02) !important;
            transition: all 0.2s ease !important;
        }
        .note-btn:hover {
            background: #f1f5f9 !important;
            color: #0f172a !important;
            border-color: rgba(15, 23, 42, 0.1) !important;
        }
        .note-btn.active {
            background: #2563eb !important;
            color: white !important;
            border-color: #2563eb !important;
        }
        .note-dropdown-menu {
            border-radius: 1rem !important;
            border: 1px solid rgba(15, 23, 42, 0.08) !important;
            box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.1) !important;
            padding: 0.5rem !important;
            background: white !important;
        }
        .note-dropdown-item {
            padding: 0.5rem 1rem !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            color: #334155 !important;
            border-radius: 0.5rem !important;
            transition: all 0.2s ease !important;
        }
        .note-dropdown-item:hover {
            background: #f1f5f9 !important;
            color: #0f172a !important;
        }
        .note-modal-content {
            border-radius: 2rem !important;
            border: none !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
            overflow: hidden !important;
        }
        .note-modal-header {
            padding: 1.5rem 2rem !important;
            background: #f8fafc !important;
            border-bottom: 1px solid rgba(15, 23, 42, 0.05) !important;
        }
        .note-modal-title {
            font-size: 14px !important;
            font-weight: 800 !important;
            color: #0f172a !important;
            text-transform: uppercase !important;
            tracking: 0.05em !important;
        }
        .note-modal-body {
            padding: 2rem !important;
        }
        .note-form-label {
            font-size: 11px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            color: #64748b !important;
            margin-bottom: 0.5rem !important;
        }
        .note-input {
            border-radius: 0.75rem !important;
            border: 1px solid rgba(15, 23, 42, 0.1) !important;
            padding: 0.65rem 1rem !important;
            font-size: 13px !important;
        }
        .note-input:focus {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1) !important;
        }
        .note-modal-footer {
            padding: 1.25rem 2rem !important;
            background: #f8fafc !important;
            border-top: 1px solid rgba(15, 23, 42, 0.05) !important;
        }
    </style>

    <div class="space-y-10 pb-20" x-data="{
        editMode: false,
        currentBlog: {},
        newPreview: null,
        initEdit(blog) {
            this.currentBlog = blog;
            this.editMode = true;
            this.newPreview = null;
            $('#editor-textarea').summernote('code', blog.content || '');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        cancelEdit() {
            this.editMode = false;
            this.currentBlog = {};
            this.newPreview = null;
            $('#editor-textarea').summernote('code', '');
        },
        handleFileChange(e) {
            const file = e.target.files[0];
            if (file) {
                this.newPreview = URL.createObjectURL(file);
            }
        }
    }">
        <!-- Form Section -->
        <div class="glass p-10 rounded-[3rem] shadow-xl relative overflow-hidden" data-aos="fade-down">
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-blue-500/5 rounded-full blur-3xl"></div>
            
            <div class="flex justify-between items-center mb-8">
                <h3 class="text-2xl font-black text-slate-900 flex items-center">
                    <span class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fa-solid" :class="editMode ? 'fa-pen-to-square' : 'fa-plus'"></i>
                    </span>
                    <span x-text="editMode ? 'Edit Blog Post' : 'Add New Blog Post'"></span>
                </h3>
                <button x-show="editMode" @click="cancelEdit()" 
                    class="px-6 py-2 bg-slate-100 text-slate-500 font-bold rounded-xl hover:bg-slate-200 transition-all">
                    Cancel Edit
                </button>
            </div>

            <!-- Add/Edit Form -->
            <form :action="editMode ? '{{ route('dashboard.blogs.index') }}/' + currentBlog.id : '{{ route('dashboard.blogs.store') }}'" 
                method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PATCH">
                </template>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Title ID -->
                    <div class="space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Post Title (ID)</label>
                        <input type="text" name="title_id" required x-model="currentBlog.title_id"
                            class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-bold">
                    </div>
                    
                    <!-- Title EN -->
                    <div class="space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Post Title (EN)</label>
                        <input type="text" name="title_en" required x-model="currentBlog.title_en"
                            class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold">
                    </div>

                    <!-- Category ID -->
                    <div class="space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Category (ID)</label>
                        <input type="text" name="category_id" required x-model="currentBlog.category_id"
                            class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-medium"
                            placeholder="e.g. Tips & Trik">
                    </div>

                    <!-- Category EN -->
                    <div class="space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Category (EN)</label>
                        <input type="text" name="category_en" required x-model="currentBlog.category_en"
                            class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-indigo-500 transition-all font-medium"
                            placeholder="e.g. Tips & Tricks">
                    </div>

                    <!-- Author Name -->
                    <div class="md:col-span-2 space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Author / Penulis</label>
                        <input type="text" name="author_name" x-model="currentBlog.author_name"
                            class="block w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 transition-all font-bold"
                            placeholder="e.g. Rifal Kurniawan (Leave blank to use dynamic administrator name)">
                    </div>

                    <!-- Unified Content Body with fully featured Summernote-Lite -->
                    <div class="md:col-span-2 space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Content Body (Complete Creative Text Suite & Nextcloud Media)</label>
                        <div class="rounded-[2rem] overflow-hidden bg-slate-50 border border-slate-200/60 shadow-inner">
                            <textarea id="editor-textarea" name="content" required class="w-full"></textarea>
                        </div>
                    </div>

                    <!-- Cover Image & Publish Status -->
                    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-8 pt-4 border-t border-slate-50">
                        <div class="space-y-4">
                            <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-1">Cover Preview Image</label>
                            <div class="flex items-center space-x-4">
                                <div class="w-24 h-16 bg-slate-100 rounded-xl overflow-hidden shadow-sm flex items-center justify-center border-2 border-white ring-1 ring-slate-100 relative">
                                    <template x-if="newPreview">
                                        <img :src="newPreview" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!newPreview && currentBlog.image">
                                        <img :src="currentBlog.media_url" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!newPreview && !currentBlog.image">
                                        <i class="fa-solid fa-image text-slate-300"></i>
                                    </template>
                                </div>
                                <div class="flex-1">
                                    <input type="file" name="image" @change="handleFileChange" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3 pt-6">
                            <input type="checkbox" name="is_published" id="is_published" value="1"
                                class="rounded border-slate-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                :checked="currentBlog.is_published">
                            <label for="is_published" class="text-sm font-bold text-slate-700 select-none">Publish immediately</label>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit"
                        class="px-12 py-5 bg-slate-900 text-white font-black rounded-[2rem] hover:bg-black hover:scale-105 shadow-2xl shadow-slate-900/20 transition-all uppercase tracking-[0.2em] text-xs">
                        <span x-text="editMode ? 'Save Post Changes' : 'Publish Blog Post'"></span>
                    </button>
                </div>
            </form>
        </div>

        <!-- List Section -->
        <div class="space-y-6">
            <h3 class="text-xl font-black text-slate-900 flex items-center ml-4">
                <span class="w-8 h-8 bg-slate-100 text-slate-500 rounded-lg flex items-center justify-center mr-3 text-sm">
                    <i class="fa-solid fa-newspaper"></i>
                </span>
                Blog Posts ({{ count($blogs) }})
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach ($blogs as $blog)
                    <div class="glass p-0 rounded-[3rem] shadow-xl overflow-hidden group flex flex-col md:flex-row card-hover" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                        <!-- Image Side -->
                        <div class="w-full md:w-48 h-48 md:h-auto relative overflow-hidden shrink-0">
                            @if ($blog->image)
                                <img src="{{ $blog->media_url }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            @else
                                <div class="w-full h-full bg-slate-100 flex items-center justify-center">
                                    <i class="fa-solid fa-camera text-4xl text-slate-200"></i>
                                </div>
                            @endif
                            <div class="absolute top-4 left-4">
                                <span class="px-3 py-1 bg-white/70 backdrop-blur-md rounded-full text-[10px] font-black uppercase tracking-widest text-slate-900 shadow-sm">{{ $blog->category_id }}</span>
                            </div>
                        </div>

                        <!-- Content Side -->
                        <div class="flex-1 p-8 flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="text-xl font-black text-slate-900 tracking-tight leading-tight line-clamp-1">{{ $blog->title_id }}</h4>
                                    <div class="flex space-x-2">
                                        <button @click="initEdit({{ $blog->toJson() }})" class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all">
                                            <i class="fa-solid fa-pen text-xs"></i>
                                        </button>
                                        <form action="{{ route('dashboard.blogs.destroy', $blog->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" onclick="return confirm('Hapus blog post ini?')" class="w-8 h-8 bg-red-50 text-red-600 rounded-lg flex items-center justify-center hover:bg-red-600 hover:text-white transition-all">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2 mb-4">
                                    <p class="text-[10px] font-bold text-slate-400 italic uppercase tracking-widest line-clamp-1">{{ $blog->title_en }}</p>
                                    <span class="text-[10px] text-slate-300">•</span>
                                    <span class="text-[10px] text-slate-400 font-extrabold uppercase">By: {{ $blog->author_name ?: $blog->author->name }}</span>
                                </div>
                                <p class="text-sm text-slate-500 line-clamp-2 leading-relaxed mb-4">{{ strip_tags($blog->content) }}</p>
                            </div>

                            <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                                <div class="flex items-center space-x-2">
                                    <div class="w-1.5 h-1.5 rounded-full {{ $blog->is_published ? 'bg-emerald-500 animate-pulse' : 'bg-amber-500' }}"></div>
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        {{ $blog->is_published ? 'Published' : 'Draft' }}
                                    </span>
                                </div>
                                <span class="text-[10px] text-slate-400 font-bold">
                                    {{ $blog->published_at ? $blog->published_at->format('M d, Y') : 'Not Published' }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if(count($blogs) === 0)
                <div class="text-center py-20 glass rounded-[3rem] border-2 border-dashed">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-200 text-4xl">
                        <i class="fa-solid fa-folder-open"></i>
                    </div>
                    <p class="text-slate-400 font-bold">No blog posts published yet</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Initialize Summernote-Lite Script -->
    <script>
        $(document).ready(function() {
            $('#editor-textarea').summernote({
                placeholder: 'Tulis konten isi artikel lengkap di sini / Write your rich articles here...',
                tabsize: 2,
                height: 400,
                dialogsInBody: true,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
                    ['fontname', ['fontname']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph', 'height']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video', 'hr']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ],
                callbacks: {
                    onChange: function(contents, $editable) {
                        // Force manual Alpine.js sync
                        const alpineEl = document.querySelector('[x-data]');
                        if (alpineEl && alpineEl.__x) {
                            alpineEl.__x.$data.currentBlog.content = contents;
                        }
                    },
                    onImageUpload: function(files) {
                        uploadEditorImage(files[0]);
                    }
                }
            });

            // Upload image function for Summernote
            function uploadEditorImage(file) {
                let data = new FormData();
                data.append("image", file);
                
                $.ajax({
                    url: '{{ route("dashboard.blogs.upload_image") }}',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: data,
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.url) {
                            $('#editor-textarea').summernote('insertImage', response.url);
                        }
                    },
                    error: function(err) {
                        console.error('Error uploading image through Summernote:', err);
                    }
                });
            }
        });
    </script>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">Blog</x-slot>

    {{-- Summernote-Lite (editor teks kaya) + jQuery, hanya dimuat di halaman ini --}}
    @push('head')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    @endpush

    <div class="mx-auto max-w-7xl space-y-6" x-data="{
        open: false,
        editMode: false,
        currentBlog: {},
        newPreview: null,
        setEditor(html) {
            if (window.jQuery && window.jQuery.fn.summernote) {
                window.jQuery('#editor-textarea').summernote('code', html || '');
            }
        },
        openCreate() {
            this.editMode = false;
            this.currentBlog = {};
            this.newPreview = null;
            this.setEditor('');
            this.open = true;
        },
        initEdit(blog) {
            this.currentBlog = blog;
            this.editMode = true;
            this.newPreview = null;
            this.setEditor(blog.content);
            this.open = true;
        },
        cancelEdit() {
            this.open = false;
        },
        handleFileChange(e) {
            const file = e.target.files[0];
            if (file) {
                this.newPreview = URL.createObjectURL(file);
            }
        }
    }" @keydown.escape.window="if (!document.querySelector('.note-editor.fullscreen') && !document.querySelector('.note-modal.open')) cancelEdit()">
        <x-ui.page-header title="Blog" description="Tulis dan kelola artikel yang tampil di halaman blog.">
            <x-slot:actions>
                <x-ui.button icon="fa-plus" @click="openCreate()">Tulis artikel</x-ui.button>
            </x-slot:actions>
        </x-ui.page-header>

        @if ($errors->any())
            <div class="rounded-lg border border-danger-500/40 bg-danger-50 p-4 text-small text-danger-700 dark:bg-danger-500/15 dark:text-red-300" role="alert">
                <p class="font-semibold">Artikel belum tersimpan. Periksa isian berikut:</p>
                <ul class="mt-1 list-disc space-y-0.5 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (count($blogs) > 0)
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($blogs as $blog)
                    <x-ui.card :padded="false" hover class="flex flex-col overflow-hidden" data-aos="fade-up" data-aos-delay="{{ min($loop->index, 6) * 50 }}">
                        <div class="relative aspect-[16/10] bg-brand-gradient-soft">
                            @if ($blog->image)
                                <img src="{{ $blog->media_url }}" alt="{{ $blog->title_id }}" loading="lazy" class="h-full w-full object-cover">
                            @else
                                <div class="flex h-full w-full items-center justify-center text-4xl text-brand-text/60">
                                    <i class="fa-solid fa-newspaper" aria-hidden="true"></i>
                                </div>
                            @endif
                            <x-ui.badge variant="neutral" class="absolute left-3 top-3 bg-surface shadow-soft">{{ $blog->category_id }}</x-ui.badge>
                        </div>

                        <div class="flex flex-1 flex-col p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h3 class="line-clamp-2 text-lg font-bold leading-tight">{{ $blog->title_id }}</h3>
                                    <p class="mt-0.5 line-clamp-1 text-small text-muted">{{ $blog->title_en }}</p>
                                </div>
                                <div class="-mr-2 -mt-1 flex shrink-0 items-center">
                                    <button type="button" @click="initEdit({{ $blog->toJson() }})" class="btn-icon" aria-label="Ubah artikel {{ $blog->title_id }}">
                                        <i class="fa-solid fa-pen" aria-hidden="true"></i>
                                    </button>
                                    <form action="{{ route('dashboard.blogs.destroy', $blog->id) }}" method="POST"
                                        data-confirm="Artikel ini akan dihapus permanen beserta gambar sampulnya." data-confirm-title="Hapus artikel?" data-confirm-ok="Ya, hapus">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-icon hover:bg-danger-50 hover:text-danger-700 dark:hover:bg-danger-500/15 dark:hover:text-red-300" aria-label="Hapus artikel {{ $blog->title_id }}">
                                            <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <p class="mt-2 text-caption text-muted"><i class="fa-regular fa-user mr-1" aria-hidden="true"></i> {{ $blog->author_name ?: $blog->author->name }}</p>
                            <p class="mt-3 line-clamp-2 flex-1 text-small text-muted">{{ strip_tags($blog->content) }}</p>

                            <div class="divider mt-5"></div>
                            <div class="mt-4 flex items-center justify-between gap-3">
                                @if ($blog->is_published)
                                    <x-ui.badge variant="success" dot>Terbit</x-ui.badge>
                                @else
                                    <x-ui.badge variant="warning" dot>Draf</x-ui.badge>
                                @endif
                                <span class="text-caption text-muted">
                                    {{ $blog->published_at ? $blog->published_at->format('d M Y') : 'Belum terbit' }}
                                </span>
                            </div>
                        </div>
                    </x-ui.card>
                @endforeach
            </div>
        @else
            <x-ui.card :padded="false">
                <x-ui.empty-state icon="fa-newspaper" title="Belum ada artikel" text="Tulis artikel pertama untuk dibagikan kepada pengunjung.">
                    <x-ui.button icon="fa-plus" @click="openCreate()">Tulis artikel</x-ui.button>
                </x-ui.empty-state>
            </x-ui.card>
        @endif

        {{-- Modal tulis / ubah artikel --}}
        <div x-show="open" x-cloak x-transition.opacity.duration.150ms
            class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center sm:p-4"
            role="dialog" aria-modal="true" aria-labelledby="blog-modal-title">
            <div class="absolute inset-0 bg-ink/50 backdrop-blur-sm" @click="cancelEdit()" aria-hidden="true"></div>

            <div class="card relative max-h-[90vh] w-full max-w-4xl overflow-y-auto rounded-b-none sm:rounded-lg">
                <div class="sticky top-0 z-10 flex items-center justify-between gap-4 border-b border-line bg-surface px-5 py-4 md:px-6">
                    <h2 id="blog-modal-title" class="text-xl font-bold" x-text="editMode ? 'Ubah artikel' : 'Tulis artikel'"></h2>
                    <button type="button" class="btn-icon -mr-2" @click="cancelEdit()" aria-label="Tutup"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                </div>

                <form :action="editMode ? '{{ route('dashboard.blogs.index') }}/' + currentBlog.id : '{{ route('dashboard.blogs.store') }}'"
                    method="POST" enctype="multipart/form-data" class="space-y-6 p-5 md:p-6">
                    @csrf
                    <template x-if="editMode">
                        <input type="hidden" name="_method" value="PATCH">
                    </template>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label for="blog-title-id" class="label">Judul artikel (Indonesia)</label>
                            <input type="text" id="blog-title-id" name="title_id" required x-model="currentBlog.title_id" class="input">
                        </div>
                        <div>
                            <label for="blog-title-en" class="label">Judul artikel (Inggris)</label>
                            <input type="text" id="blog-title-en" name="title_en" required x-model="currentBlog.title_en" class="input">
                        </div>

                        <div>
                            <label for="blog-cat-id" class="label">Kategori (Indonesia)</label>
                            <input type="text" id="blog-cat-id" name="category_id" required x-model="currentBlog.category_id" placeholder="Contoh: Tips &amp; Trik" class="input">
                        </div>
                        <div>
                            <label for="blog-cat-en" class="label">Kategori (Inggris)</label>
                            <input type="text" id="blog-cat-en" name="category_en" required x-model="currentBlog.category_en" placeholder="Example: Tips &amp; Tricks" class="input">
                        </div>

                        <div class="md:col-span-2">
                            <label for="blog-author" class="label">Penulis</label>
                            <input type="text" id="blog-author" name="author_name" x-model="currentBlog.author_name" placeholder="Contoh: Rifal Kurniawan" class="input">
                            <p class="hint">Kosongkan untuk memakai nama administrator yang sedang masuk.</p>
                        </div>

                        <div class="md:col-span-2">
                            <label for="editor-textarea" class="label">Isi artikel</label>
                            <div class="overflow-hidden rounded-md border border-line bg-surface
                                [&_.note-editor.note-frame]:!rounded-none [&_.note-editor.note-frame]:!border-0 [&_.note-editor.note-frame]:!bg-transparent [&_.note-editor.note-frame]:!shadow-none
                                [&_.note-editor.fullscreen]:!bg-surface
                                [&_.note-toolbar]:!flex [&_.note-toolbar]:!flex-wrap [&_.note-toolbar]:!gap-1 [&_.note-toolbar]:!border-x-0 [&_.note-toolbar]:!border-t-0 [&_.note-toolbar]:!border-b [&_.note-toolbar]:!border-line [&_.note-toolbar]:!bg-surface-2 [&_.note-toolbar]:!p-2
                                [&_.note-btn]:!min-h-[36px] [&_.note-btn]:!rounded-sm [&_.note-btn]:!border [&_.note-btn]:!border-line [&_.note-btn]:!bg-surface [&_.note-btn]:!text-small [&_.note-btn]:!text-ink [&_.note-btn]:!shadow-none
                                [&_.note-btn:hover]:!bg-surface-2 [&_.note-btn.active]:!border-brand-600 [&_.note-btn.active]:!bg-brand-600 [&_.note-btn.active]:!text-surface
                                [&_.note-dropdown-menu]:!rounded-md [&_.note-dropdown-menu]:!border [&_.note-dropdown-menu]:!border-line [&_.note-dropdown-menu]:!bg-surface [&_.note-dropdown-menu]:!text-ink [&_.note-dropdown-menu]:!shadow-pop
                                [&_.note-dropdown-item]:!text-small [&_.note-dropdown-item]:!text-ink [&_.note-dropdown-item:hover]:!bg-surface-2
                                [&_.note-popover]:!border-line [&_.note-popover]:!bg-surface
                                [&_.note-editing-area]:!bg-transparent [&_.note-editable]:!min-h-[320px] [&_.note-editable]:!bg-transparent [&_.note-editable]:!p-4 [&_.note-editable]:!text-body [&_.note-editable]:!text-ink
                                [&_.note-placeholder]:!text-muted
                                [&_.note-statusbar]:!border-t [&_.note-statusbar]:!border-line [&_.note-statusbar]:!bg-surface-2">
                                <textarea id="editor-textarea" name="content" required class="w-full"></textarea>
                            </div>
                            <p class="hint">Gambar yang disisipkan akan diunggah otomatis ke penyimpanan media.</p>
                        </div>
                    </div>

                    <div class="divider"></div>

                    <div class="grid grid-cols-1 items-start gap-5 md:grid-cols-2">
                        <div>
                            <label for="blog-image" class="label">Gambar sampul</label>
                            <div class="flex items-center gap-4">
                                <div class="flex aspect-[16/10] w-28 shrink-0 items-center justify-center overflow-hidden rounded-md border border-line bg-brand-gradient-soft text-brand-text/60">
                                    <template x-if="newPreview">
                                        <img :src="newPreview" alt="Pratinjau gambar baru" class="h-full w-full object-cover">
                                    </template>
                                    <template x-if="!newPreview && currentBlog.image">
                                        <img :src="currentBlog.media_url" alt="Gambar saat ini" class="h-full w-full object-cover">
                                    </template>
                                    <template x-if="!newPreview && !currentBlog.image">
                                        <i class="fa-solid fa-image text-xl" aria-hidden="true"></i>
                                    </template>
                                </div>
                                <input type="file" id="blog-image" name="image" accept="image/*" @change="handleFileChange" class="block w-full min-w-0 text-small text-muted file:mr-3 file:min-h-[44px] file:cursor-pointer file:rounded-md file:border-0 file:bg-brand-soft file:px-4 file:py-2 file:text-small file:font-semibold file:text-brand-text hover:file:bg-brand-100">
                            </div>
                            <p class="hint">Kosongkan untuk mempertahankan gambar saat ini.</p>
                        </div>

                        <label for="is_published" class="flex min-h-[44px] cursor-pointer items-center gap-3 md:mt-7">
                            <input type="checkbox" name="is_published" id="is_published" value="1" :checked="currentBlog.is_published"
                                class="h-5 w-5 rounded border-line bg-surface text-brand-600 focus:ring-brand-500">
                            <span class="text-small font-semibold">Terbitkan artikel ini</span>
                        </label>
                    </div>

                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <x-ui.button variant="secondary" @click="cancelEdit()">Batal</x-ui.button>
                        <x-ui.button type="submit" icon="fa-check"><span x-text="editMode ? 'Simpan perubahan' : 'Simpan artikel'"></span></x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                $('#editor-textarea').summernote({
                    placeholder: 'Tulis isi artikel lengkap di sini...',
                    tabsize: 2,
                    height: 360,
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
                        onImageUpload: function (files) {
                            uploadEditorImage(files[0]);
                        }
                    }
                });

                // Unggah gambar dari editor ke penyimpanan media
                function uploadEditorImage(file) {
                    let data = new FormData();
                    data.append('image', file);

                    $.ajax({
                        url: '{{ route("dashboard.blogs.upload_image") }}',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: data,
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (response.url) {
                                $('#editor-textarea').summernote('insertImage', response.url);
                            }
                        },
                        error: function (err) {
                            console.error('Error uploading image through Summernote:', err);
                            if (window.Swal) {
                                Swal.fire({ icon: 'error', title: 'Gagal mengunggah gambar', text: 'Coba lagi dengan gambar lain.' });
                            }
                        }
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>

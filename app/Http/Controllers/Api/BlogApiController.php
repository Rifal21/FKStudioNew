<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogPostResource;
use App\Models\BlogPost;
use App\Traits\NextcloudStorage;
use App\Traits\HasMediaUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BlogApiController extends Controller
{
    use NextcloudStorage, HasMediaUrl;

    // ─────────────────────────────────────────────────────────────
    // PUBLIC ENDPOINTS (no token needed)
    // ─────────────────────────────────────────────────────────────

    /**
     * GET /api/blogs
     * List all published blog posts with optional search & category filter.
     */
    public function index(Request $request)
    {
        $query = BlogPost::where('is_published', true)
            ->with('author')
            ->latest('published_at');

        if ($request->filled('category')) {
            $query->where(function ($q) use ($request) {
                $q->where('category_id', $request->category)
                  ->orWhere('category_en', $request->category);
            });
        }

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('title_id', 'like', "%{$term}%")
                  ->orWhere('title_en', 'like', "%{$term}%")
                  ->orWhere('content', 'like', "%{$term}%");
            });
        }

        $perPage = min((int) $request->get('per_page', 10), 50);
        $blogs   = $query->paginate($perPage);

        return BlogPostResource::collection($blogs)->additional([
            'meta' => [
                'total'        => $blogs->total(),
                'per_page'     => $blogs->perPage(),
                'current_page' => $blogs->currentPage(),
                'last_page'    => $blogs->lastPage(),
            ],
        ]);
    }

    /**
     * GET /api/blogs/{slug}
     * Show a single published blog post by slug.
     */
    public function show(string $slug)
    {
        $blog = BlogPost::where('slug', $slug)
            ->where('is_published', true)
            ->with('author')
            ->firstOrFail();

        // Increment view counter
        $blog->increment('views');

        return new BlogPostResource($blog);
    }

    // ─────────────────────────────────────────────────────────────
    // PROTECTED ENDPOINTS (require Sanctum token + super_admin)
    // ─────────────────────────────────────────────────────────────

    /**
     * POST /api/auth/login
     * Get a Sanctum API token.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        $user = Auth::user();

        if (!$user->isSuperAdmin()) {
            Auth::logout();
            return response()->json([
                'message' => 'Akses ditolak. Hanya super admin yang bisa menggunakan API ini.',
            ], 403);
        }

        // Revoke all old tokens and issue a fresh one
        $user->tokens()->delete();
        $token = $user->createToken('blog-api-token', ['blog:manage'])->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'token'   => $token,
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ],
        ]);
    }

    /**
     * POST /api/auth/logout
     * Revoke current token.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Token dicabut. Logout berhasil.']);
    }

    /**
     * GET /api/blogs/all
     * List ALL blog posts (draft + published) — admin only.
     */
    public function adminIndex(Request $request)
    {
        $query = BlogPost::with('author')->latest();

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('title_id', 'like', "%{$term}%")
                  ->orWhere('title_en', 'like', "%{$term}%");
            });
        }

        if ($request->filled('published')) {
            $query->where('is_published', filter_var($request->published, FILTER_VALIDATE_BOOLEAN));
        }

        $perPage = min((int) $request->get('per_page', 15), 50);
        $blogs   = $query->paginate($perPage);

        return BlogPostResource::collection($blogs)->additional([
            'meta' => [
                'total'        => $blogs->total(),
                'per_page'     => $blogs->perPage(),
                'current_page' => $blogs->currentPage(),
                'last_page'    => $blogs->lastPage(),
            ],
        ]);
    }

    /**
     * POST /api/blogs
     * Create a new blog post.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title_id'    => 'required|string|max:255',
            'title_en'    => 'required|string|max:255',
            'category_id' => 'required|string|max:100',
            'category_en' => 'required|string|max:100',
            'content'     => 'required|string',
            'author_name' => 'nullable|string|max:255',
            'is_published'=> 'nullable|boolean',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:5120',
        ]);

        // Generate unique slug from English title
        $slug = Str::slug($data['title_en']);
        $originalSlug = $slug;
        $count = 1;
        while (BlogPost::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        $data['slug']      = $slug;
        $data['author_id'] = $request->user()->id;

        $isPublished = filter_var($data['is_published'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $data['is_published'] = $isPublished;
        $data['published_at'] = $isPublished ? now() : null;

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadToNextcloud($request->file('image'), 'blogs');
        }

        $blog = BlogPost::create($data);
        $blog->load('author');

        return (new BlogPostResource($blog))
            ->additional(['message' => 'Blog post berhasil dibuat.'])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * PATCH /api/blogs/{blog}
     * Update an existing blog post.
     */
    public function update(Request $request, BlogPost $blog)
    {
        $data = $request->validate([
            'title_id'    => 'sometimes|required|string|max:255',
            'title_en'    => 'sometimes|required|string|max:255',
            'category_id' => 'sometimes|required|string|max:100',
            'category_en' => 'sometimes|required|string|max:100',
            'content'     => 'sometimes|required|string',
            'author_name' => 'nullable|string|max:255',
            'is_published'=> 'nullable|boolean',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:5120',
        ]);

        // Re-slug if English title changed
        if (isset($data['title_en']) && $blog->title_en !== $data['title_en']) {
            $slug = Str::slug($data['title_en']);
            $originalSlug = $slug;
            $count = 1;
            while (BlogPost::where('slug', $slug)->where('id', '!=', $blog->id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            $data['slug'] = $slug;
        }

        if (isset($data['is_published'])) {
            $isPublished = filter_var($data['is_published'], FILTER_VALIDATE_BOOLEAN);
            $data['is_published'] = $isPublished;
            if ($isPublished && !$blog->is_published) {
                $data['published_at'] = now();
            } elseif (!$isPublished) {
                $data['published_at'] = null;
            }
        }

        if ($request->hasFile('image')) {
            if ($blog->image) {
                $this->deleteFromNextcloud($blog->image);
            }
            $data['image'] = $this->uploadToNextcloud($request->file('image'), 'blogs');
        }

        $blog->update($data);
        $blog->load('author');

        return (new BlogPostResource($blog))
            ->additional(['message' => 'Blog post berhasil diperbarui.']);
    }

    /**
     * DELETE /api/blogs/{blog}
     * Delete a blog post and its cover image.
     */
    public function destroy(BlogPost $blog)
    {
        if ($blog->image) {
            $this->deleteFromNextcloud($blog->image);
        }
        $blog->delete();

        return response()->json(['message' => 'Blog post berhasil dihapus.']);
    }

    /**
     * POST /api/blogs/upload-image
     * Upload an inline editor image and return its public URL.
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp,gif|max:5120',
        ]);

        $path = $this->uploadToNextcloud($request->file('image'), 'blogs/editor');
        $url  = $this->getUrl($path);

        return response()->json(['url' => $url], 201);
    }
}

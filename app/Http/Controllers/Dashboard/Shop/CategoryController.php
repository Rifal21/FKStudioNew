<?php

namespace App\Http\Controllers\Dashboard\Shop;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = ProductCategory::withCount('products')->orderBy('sort_order')->orderBy('name_id')->get();

        return view('dashboard.shop.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        ProductCategory::create($this->validated($request));

        return back()->with('success', 'Kategori dibuat.');
    }

    public function update(Request $request, ProductCategory $category)
    {
        $category->update($this->validated($request, $category));

        return back()->with('success', 'Kategori diperbarui.');
    }

    public function destroy(ProductCategory $category)
    {
        // Products in it stay; they simply lose the category.
        $category->delete();

        return back()->with('success', 'Kategori dihapus.');
    }

    private function validated(Request $request, ?ProductCategory $category = null): array
    {
        $request->merge(['slug' => Str::slug($request->input('slug') ?: ($request->input('name_en') ?: $request->input('name_id')))]);

        $data = $request->validate([
            'name_id' => 'required|string|max:100',
            'name_en' => 'nullable|string|max:100',
            'slug' => ['required', 'string', 'max:120', Rule::unique('product_categories', 'slug')->ignore($category?->id)],
            'description_id' => 'nullable|string|max:1000',
            'description_en' => 'nullable|string|max:1000',
            'sort_order' => 'nullable|integer|min:0|max:100000',
        ]);

        return $data + ['is_active' => $request->boolean('is_active'), 'sort_order' => (int) ($data['sort_order'] ?? 0)];
    }
}

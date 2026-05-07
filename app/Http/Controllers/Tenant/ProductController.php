<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Traits\NextcloudStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    use NextcloudStorage;

    public function index()
    {
        $products = Product::orderBy('created_at', 'desc')->get();
        return view('tenant.dashboard.products.index', compact('products'));
    }

    public function create()
    {
        return view('tenant.dashboard.products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadToNextcloud($request->file('image'), 'products');
        }

        Product::create($data);

        return redirect()->route('tenant.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        return view('tenant.dashboard.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            if ($product->image) {
                $this->deleteFromNextcloud($product->image);
            }
            $data['image'] = $this->uploadToNextcloud($request->file('image'), 'products');
        }

        $product->update($data);

        return redirect()->route('tenant.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            $this->deleteFromNextcloud($product->image);
        }
        $product->delete();

        return redirect()->route('tenant.products.index')->with('success', 'Produk berhasil dihapus.');
    }
}

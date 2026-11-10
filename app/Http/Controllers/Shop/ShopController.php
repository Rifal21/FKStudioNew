<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\SiteSetting;
use App\Services\Shop\CartService;
use App\Services\Shop\ShopException;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public const SORTS = ['featured', 'newest', 'price_asc', 'price_desc'];

    public function index(Request $request)
    {
        $settings = SiteSetting::current();
        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'category' => (string) $request->query('category', ''),
            'type' => in_array($request->query('type'), [Product::DIGITAL, Product::PHYSICAL], true) ? $request->query('type') : '',
            'sort' => in_array($request->query('sort'), self::SORTS, true) ? $request->query('sort') : 'featured',
        ];

        $categories = ProductCategory::where('is_active', true)
            ->whereHas('products', fn ($q) => $q->visibleInShop($settings))
            ->orderBy('sort_order')->orderBy('name_id')
            ->get();

        $products = Product::visibleInShop($settings)
            ->with(['images', 'category', 'variants' => fn ($q) => $q->where('is_active', true)])
            ->when($filters['q'] !== '', function ($q) use ($filters) {
                $term = '%'.str_replace(['%', '_'], ['\%', '\_'], $filters['q']).'%';
                $q->where(fn ($w) => $w->where('name_id', 'like', $term)->orWhere('name_en', 'like', $term)
                    ->orWhere('short_description_id', 'like', $term)->orWhere('short_description_en', 'like', $term));
            })
            ->when($filters['category'] !== '', fn ($q) => $q->whereHas('category', fn ($c) => $c->where('slug', $filters['category'])))
            ->when($filters['type'] !== '', fn ($q) => $q->where('type', $filters['type']))
            ->when($filters['sort'] === 'featured', fn ($q) => $q->orderByDesc('is_featured')->orderBy('sort_order')->latest())
            ->when($filters['sort'] === 'newest', fn ($q) => $q->latest())
            ->when($filters['sort'] === 'price_asc', fn ($q) => $q->orderBy('min_price'))
            ->when($filters['sort'] === 'price_desc', fn ($q) => $q->orderByDesc('max_price'))
            ->paginate(12)
            ->withQueryString();

        return view('landing.shop.index', compact('settings', 'categories', 'products', 'filters'));
    }

    public function show(Request $request, string $slug, CartService $cart)
    {
        $settings = SiteSetting::current();
        $product = Product::where('slug', $slug)
            ->with(['images', 'category', 'options.values', 'variants' => fn ($q) => $q->where('is_active', true)->with('optionValues', 'image')])
            ->firstOrFail();

        abort_unless($product->isVisibleInShop($settings) && $product->variants->isNotEmpty(), 404);

        $this->addPendingCartItem($request, $product, $cart);

        $related = Product::visibleInShop($settings)
            ->whereKeyNot($product->id)
            ->when($product->product_category_id, fn ($q) => $q->where('product_category_id', $product->product_category_id))
            ->with(['images', 'variants' => fn ($q) => $q->where('is_active', true)])
            ->orderByDesc('is_featured')->latest()
            ->limit(4)
            ->get();

        return view('landing.shop.show', [
            'settings' => $settings,
            'product' => $product,
            'related' => $related,
            'variantData' => $product->storefrontData(),
        ]);
    }

    /** A guest clicked "add to cart", logged in, and came back here: finish what they started. */
    private function addPendingCartItem(Request $request, Product $product, CartService $cart): void
    {
        $pending = $request->session()->get('pending_cart_item');

        if (! $request->user() || ! $pending || ($pending['product_id'] ?? null) !== $product->id) {
            return;
        }

        $request->session()->forget('pending_cart_item');
        $variant = $product->variants->firstWhere('id', $pending['variant_id'] ?? null);
        $id = app()->getLocale() === 'id';

        try {
            if (! $variant) {
                throw new ShopException($id ? 'Varian yang Anda pilih sudah tidak tersedia.' : 'The option you picked is no longer available.');
            }

            $cart->add($request->user(), $variant, (int) ($pending['quantity'] ?? 1));
            session()->flash('success', $id ? 'Produk ditambahkan ke keranjang.' : 'Added to your cart.');
        } catch (ShopException $e) {
            session()->flash('error', $e->getMessage());
        }
    }
}

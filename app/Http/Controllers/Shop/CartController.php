<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\SiteSetting;
use App\Services\Shop\CartService;
use App\Services\Shop\ShopException;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cart)
    {
    }

    public function index(Request $request)
    {
        $settings = SiteSetting::current();
        $summary = $this->cart->summarize($this->cart->items($request->user()), $settings);

        return view('landing.shop.cart', compact('settings', 'summary'));
    }

    /** Guests are sent to log in first; the choice is remembered and added when they come back. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'variant_id' => 'required|string',
            'quantity' => 'nullable|integer|min:1|max:'.CartService::MAX_QUANTITY,
            'buy_now' => 'nullable|boolean',
        ]);

        $variant = ProductVariant::with('product')->findOrFail($data['variant_id']);
        $id = app()->getLocale() === 'id';

        if (! $request->user()) {
            $request->session()->put('pending_cart_item', [
                'product_id' => $variant->product_id,
                'variant_id' => $variant->id,
                'quantity' => (int) ($data['quantity'] ?? 1),
            ]);
            $request->session()->put('url.intended', route('shop.show', $variant->product->slug));

            return redirect()->route('login')->with('status', $id
                ? 'Masuk atau daftar dulu untuk menambahkan produk ke keranjang. Setelah itu Anda kembali ke halaman produk.'
                : 'Log in or sign up to add products to your cart. You will come back to the product page afterwards.');
        }

        try {
            $this->cart->add($request->user(), $variant, (int) ($data['quantity'] ?? 1));
        } catch (ShopException $e) {
            return back()->with('error', $e->getMessage());
        }

        if ($request->boolean('buy_now')) {
            return redirect()->route('shop.checkout');
        }

        return back()->with('success', $id ? 'Produk ditambahkan ke keranjang.' : 'Added to your cart.');
    }

    public function update(Request $request, CartItem $cartItem)
    {
        abort_unless($cartItem->user_id === $request->user()->id, 404);

        $data = $request->validate(['quantity' => 'required|integer|min:1|max:'.CartService::MAX_QUANTITY]);

        try {
            $this->cart->update($cartItem, (int) $data['quantity']);
        } catch (ShopException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back();
    }

    public function destroy(Request $request, CartItem $cartItem)
    {
        abort_unless($cartItem->user_id === $request->user()->id, 404);

        $cartItem->delete();

        return back()->with('success', app()->getLocale() === 'id' ? 'Produk dihapus dari keranjang.' : 'Removed from your cart.');
    }
}

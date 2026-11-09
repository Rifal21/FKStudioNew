<?php

namespace App\Services\Shop;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Support\Collection;

class CartService
{
    public const MAX_QUANTITY = 99;

    public function items(User $user): Collection
    {
        return CartItem::where('user_id', $user->id)
            ->with(['variant.product.images', 'variant.image'])
            ->orderBy('created_at')
            ->get();
    }

    public function count(User $user): int
    {
        return (int) CartItem::where('user_id', $user->id)->sum('quantity');
    }

    public function add(User $user, ProductVariant $variant, int $quantity = 1): CartItem
    {
        $quantity = max(1, $quantity);
        $item = CartItem::firstOrNew(['user_id' => $user->id, 'product_variant_id' => $variant->id]);
        $newQuantity = min(self::MAX_QUANTITY, ($item->exists ? $item->quantity : 0) + $quantity);

        $this->assertPurchasable($variant, $newQuantity);

        $item->quantity = $newQuantity;
        $item->save();

        return $item;
    }

    public function update(CartItem $item, int $quantity): CartItem
    {
        $quantity = min(self::MAX_QUANTITY, max(1, $quantity));
        $this->assertPurchasable($item->variant, $quantity);
        $item->update(['quantity' => $quantity]);

        return $item;
    }

    public function clear(User $user): void
    {
        CartItem::where('user_id', $user->id)->delete();
    }

    /**
     * One line per cart item with its current price and whether it can still be bought.
     *
     * @return array{lines: Collection, subtotal: int, has_physical: bool, has_issues: bool, weight: int}
     */
    public function summarize(Collection $items, ?SiteSetting $settings = null): array
    {
        $settings ??= SiteSetting::current();
        $locale = app()->getLocale();

        $lines = $items->map(function (CartItem $item) use ($settings, $locale) {
            $variant = $item->variant;
            $product = $variant?->product;
            $issue = null;

            if (! $variant || ! $product || ! $variant->purchasable(1, $settings)) {
                $issue = $locale === 'id' ? 'Produk ini sudah tidak tersedia.' : 'This product is no longer available.';
            } elseif ($variant->stock !== null && $variant->stock < $item->quantity) {
                $issue = $locale === 'id' ? "Stok tersisa {$variant->stock}." : "Only {$variant->stock} left.";
            }

            $price = (int) ($variant?->price ?? 0);

            return (object) [
                'item' => $item,
                'variant' => $variant,
                'product' => $product,
                'quantity' => $item->quantity,
                'unit_price' => $price,
                'subtotal' => $price * $item->quantity,
                'issue' => $issue,
                'weight' => (int) ($variant?->weight_grams ?? 0) * $item->quantity,
            ];
        });

        $valid = $lines->whereNull('issue');

        return [
            'lines' => $lines,
            'subtotal' => (int) $valid->sum('subtotal'),
            'has_physical' => $valid->contains(fn ($l) => $l->product?->type === Product::PHYSICAL),
            'has_issues' => $lines->whereNotNull('issue')->isNotEmpty(),
            'weight' => (int) $valid->sum('weight'),
        ];
    }

    private function assertPurchasable(?ProductVariant $variant, int $quantity): void
    {
        $id = app()->getLocale() === 'id';

        if (! $variant || ! $variant->purchasable(1)) {
            throw new ShopException($id ? 'Produk ini sedang tidak tersedia.' : 'This product is not available right now.');
        }

        if ($variant->stock !== null && $variant->stock < $quantity) {
            throw new ShopException($id
                ? "Stok tidak cukup. Tersisa {$variant->stock}."
                : "Not enough stock. Only {$variant->stock} left.");
        }
    }
}

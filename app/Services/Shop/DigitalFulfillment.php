<?php

namespace App\Services\Shop;

use App\Models\DigitalEntitlement;
use App\Models\Product;
use App\Models\ProductLicenseKey;
use App\Models\ShopOrder;
use App\Models\ShopOrderItem;

/**
 * Gives the buyer access to what they paid for: one entitlement per unit bought,
 * each with its own download allowance and (for license products) its own key.
 */
class DigitalFulfillment
{
    /**
     * Safe to call again: existing entitlements are kept, missing license keys are filled in.
     * Returns the names of products whose license keys ran out.
     *
     * @return list<string>
     */
    public function grant(ShopOrder $order): array
    {
        $order->loadMissing('items.product', 'items.variant');
        $missingKeys = [];

        foreach ($order->items as $item) {
            if ($item->product_type !== Product::DIGITAL || ! $order->user_id) {
                continue;
            }

            $product = $item->product;
            $existing = $item->entitlements()->count();

            for ($i = $existing; $i < $item->quantity; $i++) {
                DigitalEntitlement::create([
                    'user_id' => $order->user_id,
                    'shop_order_item_id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'max_downloads' => $product?->max_downloads,
                    'expires_at' => $product?->download_expiry_days ? now()->addDays($product->download_expiry_days) : null,
                ]);
            }

            if ($product?->digital_delivery === Product::DELIVERY_LICENSE && ! $this->assignKeys($item)) {
                $missingKeys[] = $item->displayName();
            }
        }

        return $missingKeys;
    }

    /** Fills every entitlement of the item that has no key yet. Returns false if the pool ran dry. */
    public function assignKeys(ShopOrderItem $item): bool
    {
        $variant = $item->variant;

        foreach ($item->entitlements()->whereNull('product_license_key_id')->whereNull('revoked_at')->get() as $entitlement) {
            $key = $variant ? ProductLicenseKey::usableFor($variant)->lockForUpdate()->first() : null;

            if (! $key) {
                return false;
            }

            $key->update(['shop_order_item_id' => $item->id, 'assigned_at' => now()]);
            $entitlement->update(['product_license_key_id' => $key->id]);
        }

        return true;
    }

    public function revoke(ShopOrder $order): void
    {
        DigitalEntitlement::whereIn('shop_order_item_id', $order->items()->pluck('id'))
            ->whereNull('revoked_at')
            ->update(['revoked_at' => now()]);
    }

    /** Orders whose license items still have entitlements without a key. */
    public static function hasMissingKeys(ShopOrder $order): bool
    {
        return DigitalEntitlement::whereIn('shop_order_item_id', $order->items->where('product_type', Product::DIGITAL)->pluck('id'))
            ->whereNull('product_license_key_id')
            ->whereNull('revoked_at')
            ->whereHas('product', fn ($q) => $q->where('digital_delivery', Product::DELIVERY_LICENSE))
            ->exists();
    }
}

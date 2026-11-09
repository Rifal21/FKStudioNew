<?php

namespace App\Services\Shop;

/**
 * Shop prices include PPN. This splits a tax-inclusive amount into DPP (base) and PPN.
 * The result is stored on the order for the admin; customers never see it.
 */
class ShopTax
{
    /** @return array{base: int, tax: int} */
    public static function split(int $grossAmount, float $rate): array
    {
        if ($grossAmount <= 0 || $rate <= 0) {
            return ['base' => max(0, $grossAmount), 'tax' => 0];
        }

        $tax = (int) round($grossAmount * $rate / (100 + $rate));

        return ['base' => $grossAmount - $tax, 'tax' => $tax];
    }
}

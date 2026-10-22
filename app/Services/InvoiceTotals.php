<?php

namespace App\Services;

class InvoiceTotals
{
    /**
     * Discount applies to the subtotal, tax applies to what is left after the discount.
     * Both can be a percentage or a fixed rupiah amount.
     *
     * @return array{subtotal: float, discount: float, tax: float, total: float}
     */
    public static function compute(float $subtotal, float $discount, ?string $discountType, float $tax, ?string $taxType): array
    {
        $discountAmount = ($discountType ?? 'percent') === 'percent' ? $subtotal * ($discount / 100) : $discount;
        $taxAmount = ($taxType ?? 'percent') === 'percent' ? ($subtotal - $discountAmount) * ($tax / 100) : $tax;

        return [
            'subtotal' => $subtotal,
            'discount' => $discountAmount,
            'tax' => $taxAmount,
            'total' => $subtotal - $discountAmount + $taxAmount,
        ];
    }
}

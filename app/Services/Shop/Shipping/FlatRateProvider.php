<?php

namespace App\Services\Shop\Shipping;

use App\Models\SiteSetting;
use App\Models\UserAddress;

class FlatRateProvider implements ShippingRateProvider
{
    public function rates(UserAddress $destination, int $weightGrams, SiteSetting $settings): array
    {
        if ($settings->shop_flat_shipping_cost === null) {
            return [];
        }

        return [[
            'code' => 'flat',
            'service' => 'standard',
            'label' => app()->getLocale() === 'id' ? 'Pengiriman standar' : 'Standard shipping',
            'cost' => (int) $settings->shop_flat_shipping_cost,
            'etd' => null,
        ]];
    }
}

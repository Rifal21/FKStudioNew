<?php

namespace App\Http\Controllers\Dashboard\Shop;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function edit()
    {
        return view('dashboard.shop.settings', ['settings' => SiteSetting::current()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'shop_tax_rate' => 'required|numeric|min:0|max:100',
            'shop_order_expiry_minutes' => 'required|integer|min:15|max:10080',
            'shop_manual_expiry_minutes' => 'required|integer|min:15|max:10080',
            'shop_admin_phone' => 'nullable|string|max:30',
            'shop_origin_address' => 'nullable|string|max:1000',
            'shop_origin_area_id' => 'nullable|string|max:100',
            'shop_flat_shipping_cost' => 'nullable|integer|min:0|max:100000000',
        ]);

        foreach (['shop_enabled', 'shop_physical_enabled', 'shop_manual_payment_enabled', 'shop_manual_unique_code'] as $flag) {
            $data[$flag] = $request->boolean($flag);
        }

        $settings = SiteSetting::first();

        if (! $settings) {
            return back()->with('error', 'Simpan Pengaturan umum terlebih dahulu.');
        }

        $settings->update($data);

        return back()->with('success', 'Pengaturan toko disimpan.');
    }
}

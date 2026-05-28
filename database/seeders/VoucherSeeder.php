<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Voucher::create([
            'code'       => 'FKSTUDIO10',
            'type'       => 'percent',
            'value'      => 10.00,
            'max_uses'   => 100,
            'used_count' => 0,
            'expires_at' => now()->addDays(30),
            'is_active'  => true,
        ]);

        Voucher::create([
            'code'       => 'DISKON50K',
            'type'       => 'fixed',
            'value'      => 50000.00,
            'max_uses'   => 50,
            'used_count' => 0,
            'expires_at' => now()->addDays(30),
            'is_active'  => true,
        ]);

        Voucher::create([
            'code'       => 'LAUNCHING20',
            'type'       => 'percent',
            'value'      => 20.00,
            'max_uses'   => 10,
            'used_count' => 0,
            'expires_at' => now()->addDays(14),
            'is_active'  => true,
        ]);
    }
}

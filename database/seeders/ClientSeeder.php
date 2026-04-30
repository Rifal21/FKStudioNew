<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        Client::create(['name' => 'Google', 'logo' => 'google.svg', 'order' => 1]);
        Client::create(['name' => 'Microsoft', 'logo' => 'microsoft.svg', 'order' => 2]);
        Client::create(['name' => 'Apple', 'logo' => 'apple.svg', 'order' => 3]);
        Client::create(['name' => 'Meta', 'logo' => 'meta.svg', 'order' => 4]);
    }
}

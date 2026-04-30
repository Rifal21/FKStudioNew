<?php

namespace Database\Seeders;

use App\Models\Owner;
use Illuminate\Database\Seeder;

class OwnerSeeder extends Seeder
{
    public function run(): void
    {
        Owner::create([
            'name' => 'Falkur',
            'role_id' => 'Pendiri & CEO',
            'role_en' => 'Founder & CEO',
            'bio_id' => 'Visi saya adalah membawa teknologi modern ke setiap bisnis.',
            'bio_en' => 'My vision is to bring modern technology to every business.',
            'image' => 'owner-1.jpg',
            'instagram_url' => 'https://instagram.com',
            'linkedin_url' => 'https://linkedin.com',
        ]);
    }
}

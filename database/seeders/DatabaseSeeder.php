<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin Profile
        User::updateOrCreate(
            ['email' => 'rifal@gmail.com'],
            [
                'name' => 'Rifal Kurniawan',
                'password' => Hash::make('falkur21'),
                'role' => 'super_admin',
                'email_verified_at' => now(),
            ]
        );

        // Run Section Seeders
        $this->call([
            SiteSettingSeeder::class,
            HeroSectionSeeder::class,
            AboutSectionSeeder::class,
            ServiceSeeder::class,
            ProjectSeeder::class,
            TestimonialSeeder::class,
            ClientSeeder::class,
            OwnerSeeder::class,
            PackageSeeder::class,
            BlogPostSeeder::class,
        ]);
    }
}

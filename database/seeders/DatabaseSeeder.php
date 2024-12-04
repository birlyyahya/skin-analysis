<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'birly',
            'email' => 'birly@gmail.com',
            'email_verified_at' => now(),
            'password' => md5('12345678'),
            'remember_token' => str()->random(12),
        ]);
    }
}

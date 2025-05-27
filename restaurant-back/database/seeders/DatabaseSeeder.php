<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Shton përdoruesin testues
        \App\Models\User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin'
        ]);

        \App\Models\User::factory()->create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => bcrypt('user123'),
            'role' => 'user'
        ]);

        // Thërret seeder-in e tavolinave
        $this->call([
            TableSeeder::class,
        ]);
    }
}

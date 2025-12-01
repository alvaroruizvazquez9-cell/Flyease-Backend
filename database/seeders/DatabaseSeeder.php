<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@flyease.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Crear un usuario normal para pruebas
        User::create([
            'name' => 'Test User',
            'email' => 'user@flyease.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);
    }
}

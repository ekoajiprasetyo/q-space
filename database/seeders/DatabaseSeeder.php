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
        // Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@q-link.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Teacher
        User::create([
            'name' => 'Guru Q-Store',
            'email' => 'guru@q-link.test',
            'password' => Hash::make('password'),
            'role' => 'guru',
        ]);

        // Student
        User::create([
            'name' => 'Siswa Q-Store',
            'email' => 'siswa@q-link.test',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);
        
        // Standard Test User
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);
    }
}

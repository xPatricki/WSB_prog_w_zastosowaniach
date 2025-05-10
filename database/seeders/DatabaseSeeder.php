<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create demo admin user
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('admin'),
                'email_verified_at' => now(),
                'role' => 'admin',
            ]
        );


        // Create regular user
        User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        // Create BookKeeper user
        User::create([
            'name' => 'BookKeeper',
            'email' => 'bookkeeper@example.com',
            'password' => Hash::make('password'),
            'role' => 'bookkeeper',
        ]);
        
        // Run the book seeder
        $this->call([
            BookSeeder::class,
        ]);
    }
}

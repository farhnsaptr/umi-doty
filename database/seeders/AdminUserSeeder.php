<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengguna;
use App\Models\Peran;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; // Added to check if Peran table exists

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if the 'peran' table exists and has the 'Admin' role
        // This is a basic check; for production, ensure migrations run first
        try {
            $adminRole = Peran::firstOrCreate(
                ['nama_peran' => 'Admin']
            );
        } catch (\Illuminate\Database\QueryException $e) {
            $this->command->warn('Could not seed Admin role. Ensure "peran" table exists and migrations have run.');
            $this->command->warn('Error: ' . $e->getMessage());
            return; // Stop seeding if roles cannot be handled
        }


        // Check if an admin user with the username 'admin' already exists
        $adminUser = Pengguna::where('username', 'admin')->first();

        if (!$adminUser) {
            // Create the admin user
            Pengguna::create([
                'id_peran' => $adminRole->id,
                'nama_lengkap' => 'Administrator',
                'username' => 'admin',
                'email' => 'admin@example.com', // Added the email field
                'password_hash' => Hash::make('password'), // Hashed password
            ]);

            $this->command->info('Admin user created successfully.');
            $this->command->info('Username: admin');
            $this->command->info('Password: password');
            $this->command->warn('IMPORTANT: Please log in and change the password immediately!');

        } else {
            $this->command->info('Admin user with username "admin" already exists.');
        }
    }
}
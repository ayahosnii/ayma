<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Make sure to import your User model
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create the admin role if it doesn't exist
        $role = Role::firstOrCreate(['name' => 'admin']);

        // Check if the admin user already exists
        if (!User::where('email', 'admin@example.com')->exists()) {
            $user = User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('securepassword'), // Use a secure password
            ]);

            // Assign the admin role to the user
            $user->assignRole($role);
        }
    }
}

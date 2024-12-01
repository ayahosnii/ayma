<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Ensure your User model is imported
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
        // Create the admin role with the 'api' guard if it doesn't exist
        $role = Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'api']
        );

        // Check if the admin user already exists
        if (!User::where('email', 'admin@ayma.com')->exists()) {
            // Create the admin user
            $user = User::create([
                'name' => 'Admin User',
                'email' => 'admin@ayma.com',
                'password' => Hash::make('password'),
            ]);

            // Assign the admin role to the user
            $user->assignRole($role);
        }
    }
}

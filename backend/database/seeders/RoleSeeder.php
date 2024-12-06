<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
class RoleSeeder extends Seeder
{
    public function run()
    {
        // Define POS system roles with capitalized first letter
        $roles = [
            'Super-admin' => 'Has full access to everything, including user management.',
            'Admin' => 'Can manage products, orders, and view reports, but cannot manage users.',
            'Manager' => 'Can view and edit products, manage orders, and view reports.',
            'Cashier' => 'Can only create and view orders.',
            'Customer' => 'Can only view orders and make purchases.',
            'Inventory-manager' => 'Manages stock levels, receives new inventory, and manages product categories.',
            'Support' => 'Handles customer support tickets, queries, and returns.',
            'Supplier' => 'Can manage their own products, view orders, and update stock levels.',
            'Delivery' => 'Responsible for delivering orders and updating order statuses.',
            'Accountant' => 'Handles financial records, invoicing, and reports related to sales and payments.',
            'Store-manager' => 'Oversees store operations, including staff management and sales.',
            'Salesperson' => 'Handles sales, assists customers, and processes transactions.',
        ];

        // Create roles with descriptions (optional)
        foreach ($roles as $role => $description) {
            Role::firstOrCreate(
                ['name' => $role],
                ['guard_name' => 'api']
            );
        }
    }
}

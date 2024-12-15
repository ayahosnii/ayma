<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Define the permissions based on POS system functionality with hyphens
        $permissions = [
            // Product Permissions
            'view-products',
            'create-products',
            'edit-products',
            'delete-products',
            'manage-product-categories',

            // Order Permissions
            'view-orders',
            'create-orders',
            'edit-orders',
            'delete-orders',
            'view-order-history',

            // User Permissions
            'manage-users',
            'view-user-details',
            'assign-roles-to-users',

            // Reporting Permissions
            'view-sales-reports',
            'view-inventory-reports',

            // Customer Permissions
            'view-customer-feedback',
            'manage-customer-loyalty-programs',
        ];

        // Create permissions for the `api` guard
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['guard_name' => 'api'] // Explicitly set the guard to 'api'
            );
        }

        // Assign permissions to roles for the `api` guard
        $this->assignPermissionsToRoles();
    }

    private function assignPermissionsToRoles()
    {
        // Define the roles with the `api` guard explicitly
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'Super-admin'],
            ['guard_name' => 'api']
        );

        $adminRole = Role::firstOrCreate(
            ['name' => 'Admin'],
            ['guard_name' => 'api']
        );

        $managerRole = Role::firstOrCreate(
            ['name' => 'Manager'],
            ['guard_name' => 'api']
        );

        $cashierRole = Role::firstOrCreate(
            ['name' => 'Cashier'],
            ['guard_name' => 'api']
        );

        $customerRole = Role::firstOrCreate(
            ['name' => 'Customer'],
            ['guard_name' => 'api']
        );

        $deliveryRole = Role::firstOrCreate(
            ['name' => 'Delivery'],
            ['guard_name' => 'api']
        );

        // Super Admin: Full permissions
        $superAdminRole->givePermissionTo(Permission::all());

        // Admin: All permissions except user management
        $adminRole->givePermissionTo([
            'view-products',
            'create-products',
            'edit-products',
            'delete-products',
            'manage-product-categories',
            'view-orders',
            'create-orders',
            'edit-orders',
            'delete-orders',
            'view-order-history',
            'view-sales-reports',
            'view-inventory-reports',
        ]);

        // Manager: Can manage products and orders
        $managerRole->givePermissionTo([
            'view-products',
            'create-products',
            'edit-products',
            'view-orders',
            'create-orders',
            'edit-orders',
            'view-order-history',
            'view-sales-reports',
            'view-inventory-reports',
        ]);

        // Cashier: Can only create and view orders
        $cashierRole->givePermissionTo([
            'view-orders',
            'create-orders',
        ]);

        // Customer: Can view orders and manage their own account
        $customerRole->givePermissionTo([
            'view-orders',
        ]);
        // Customer: Can view orders and manage their own account
        $deliveryRole->givePermissionTo([
            'view-orders',
        ]);
    }
}

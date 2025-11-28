<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Define roles
        $roles = [
            'super-admin',
            'admin',
        ];

        // Define permissions per your sidebar spec
        $permissions = [
            // User management (super admin only)
            'users.view', 'users.create', 'users.update', 'users.delete',

            // Roles & Permissions (super admin only)
            'roles.view', 'roles.create', 'roles.update', 'roles.delete',
            'permissions.view', 'permissions.create', 'permissions.update', 'permissions.delete',
            'user-roles.update',

            // Master Products
            'products.view', 'products.create', 'products.update', 'products.delete',
            'product-batches.view', 'product-batches.create', 'product-batches.update', 'product-batches.delete',
            'product-batches.report',

            // Penjualan
            'penjualan.view', 'penjualan.create', 'penjualan.update', 'penjualan.delete',
            'surat-jalan.view', 'surat-jalan.create', 'surat-jalan.update', 'surat-jalan.delete',
            'transactions.history',

            // Finance
            'budget-target.view', 'budget-target.create', 'budget-target.update', 'budget-target.delete',
            'finance.input.view', 'finance.input.create', 'finance.input.update', 'finance.input.delete',
            'finance.history',

            // Customer
            'customers.view', 'customers.create', 'customers.update', 'customers.delete',
        ];

        // Create permissions
        foreach ($permissions as $perm) {
            Permission::findOrCreate($perm, 'web');
        }

        // Create roles
        foreach ($roles as $r) {
            Role::findOrCreate($r, 'web');
        }

        // Assign permissions to roles
        $super = Role::findByName('super-admin', 'web');
        $admin = Role::findByName('admin', 'web');

        // Super admin gets everything
        $super->syncPermissions(Permission::all());

        // Admin gets a subset
        $adminPermissions = [
            // Master Products
            'products.view', 'products.create', 'products.update', 'products.delete',
            'product-batches.view', 'product-batches.create', 'product-batches.update', 'product-batches.delete',
            'product-batches.report',
            // Penjualan
            'penjualan.view', 'penjualan.create', 'penjualan.update', 'penjualan.delete',
            'surat-jalan.view', 'surat-jalan.create', 'surat-jalan.update', 'surat-jalan.delete',
            'transactions.history',
            // Finance (Input Keuangan + History)
            'finance.input.view', 'finance.input.create', 'finance.input.update', 'finance.input.delete',
            'finance.history',
            // Customer
            'customers.view', 'customers.create', 'customers.update', 'customers.delete',
        ];
        $admin->syncPermissions($adminPermissions);
    }
}

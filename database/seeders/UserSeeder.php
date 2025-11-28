<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
	public function run(): void
	{
		// Ensure roles exist (seeded by RolePermissionSeeder)
		$superRole = Role::findByName('super-admin', 'web');
		$adminRole = Role::findByName('admin', 'web');

		// Create Super Admin user
		$super = User::firstOrCreate(
			['email' => 'superadmin@example.com'],
			[
				'name' => 'Super Admin',
				'password' => Hash::make('password'),
				'status' => 'active',
			]
		);
		if (!$super->hasRole($superRole->name)) {
			$super->assignRole($superRole);
		}

		// Create Admin user
		$admin = User::firstOrCreate(
			['email' => 'admin@example.com'],
			[
				'name' => 'Admin',
				'password' => Hash::make('password'),
				'status' => 'active',
			]
		);
		if (!$admin->hasRole($adminRole->name)) {
			$admin->assignRole($adminRole);
		}
	}
}


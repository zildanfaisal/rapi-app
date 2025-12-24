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

        // ============================================
        // SUPER ADMIN ACCOUNTS (3 users)
        // ============================================

        // 1. Pa Ipong (Super Admin)
        $paIpong = User::firstOrCreate(
            ['email' => 'ipong@gmail.com'],
            [
                'name' => 'Pa Ipong',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        if (!$paIpong->hasRole($superRole->name)) {
            $paIpong->assignRole($superRole);
        }

        // 2. Pa Adi (Super Admin)
        $paAdi = User::firstOrCreate(
            ['email' => 'adi@gmail.com'],
            [
                'name' => 'Pa Adi',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        if (!$paAdi->hasRole($superRole->name)) {
            $paAdi->assignRole($superRole);
        }

        // 3. Bang Bagas (Super Admin)
        $bangBagas = User::firstOrCreate(
            ['email' => 'bagas@gmail.com'],
            [
                'name' => 'Bang Bagas',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        if (!$bangBagas->hasRole($superRole->name)) {
            $bangBagas->assignRole($superRole);
        }

        // ============================================
        // ADMIN ACCOUNT (1 user)
        // ============================================

        // 4. Bule (Admin)
        $bule = User::firstOrCreate(
            ['email' => 'bule@gmail.com'],
            [
                'name' => 'Bule',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        if (!$bule->hasRole($adminRole->name)) {
            $bule->assignRole($adminRole);
        }

        // Output info
        $this->command->info('✅ Default users created successfully:');
        $this->command->info('   - Pa Ipong (Super Admin): ipong@gmail.com');
        $this->command->info('   - Pa Adi (Super Admin): adi@gmail.com');
        $this->command->info('   - Bang Bagas (Super Admin): bagas@gmail.com');
        $this->command->info('   - Bule (Admin): bule@gmail.com');
        $this->command->info('   Default password: password');
    }
}

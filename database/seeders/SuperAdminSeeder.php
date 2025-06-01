<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create or get the superadmin role
        $role = Role::firstOrCreate([
            'name' => 'superadmin',
        ], [
            'permissions' => json_encode(['*']),
        ]);

        // Create the superadmin user
        $user = User::firstOrCreate([
            'email' => 'superadmin@example.com',
        ], [
            'password' => Hash::make('SuperSecurePassword123!'),
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'organization' => 'CGS Events',
        ]);

        // Attach the superadmin role
        if (!$user->roles()->where('name', 'superadmin')->exists()) {
            $user->roles()->attach($role->id);
        }
    }
} 
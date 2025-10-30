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
            'email' => 'conferencescgs@gmail.com',
        ], [
            'password' => Hash::make('Password@#24'),
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'organization' => 'CGS Events',
            'email_verified_at' => now(),
        ]);

        // Attach the superadmin role
        if (!$user->roles()->where('name', 'superadmin')->exists()) {
            $user->roles()->attach($role->id);
        }
    }
} 
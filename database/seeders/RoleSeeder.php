<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'superadmin',
                'permissions' => json_encode(['*']),
            ],
            [
                'name' => 'admin',
                'permissions' => json_encode([
                    'conferences.*',
                    'participants.*',
                    'sessions.*',
                    'tasks.*',
                    'notifications.*',
                    'users.view',
                    'reports.*'
                ]),
            ],
            [
                'name' => 'organizer',
                'permissions' => json_encode([
                    'conferences.view',
                    'conferences.edit',
                    'participants.*',
                    'sessions.view',
                    'sessions.edit',
                    'tasks.view',
                    'tasks.edit',
                    'notifications.view',
                    'notifications.create'
                ]),
            ],
            [
                'name' => 'speaker',
                'permissions' => json_encode([
                    'sessions.view',
                    'sessions.edit',
                    'participants.view',
                    'notifications.view'
                ]),
            ],
            [
                'name' => 'attendee',
                'permissions' => json_encode([
                    'sessions.view',
                    'participants.view',
                    'notifications.view'
                ]),
            ],
            [
                'name' => 'tasker',
                'permissions' => json_encode([
                    'tasks.view',
                    'tasks.update',
                    'notifications.view'
                ]),
            ],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );
        }
    }
}

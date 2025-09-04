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
                'permissions' => ['*'],
            ],
            [
                'name' => 'admin',
                'permissions' => [
                    'conferences.*',
                    'participants.*',
                    'sessions.*',
                    'tasks.*',
                    'notifications.*',
                    'users.view',
                    'reports.*'
                ],
            ],
            [
                'name' => 'organizer',
                'permissions' => [
                    'conferences.view',
                    'conferences.edit',
                    'participants.*',
                    'sessions.view',
                    'sessions.edit',
                    'tasks.view',
                    'tasks.edit',
                    'notifications.view',
                    'notifications.create'
                ],
            ],
            [
                'name' => 'speaker',
                'permissions' => [
                    'sessions.view',
                    'sessions.edit',
                    'participants.view',
                    'notifications.view'
                ],
            ],
            [
                'name' => 'attendee',
                'permissions' => [
                    'sessions.view',
                    'participants.view',
                    'notifications.view'
                ],
            ],
            [
                'name' => 'tasker',
                'permissions' => [
                    'tasks.view',
                    'tasks.update',
                    'notifications.view'
                ],
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

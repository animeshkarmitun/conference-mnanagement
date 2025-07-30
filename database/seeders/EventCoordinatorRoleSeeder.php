<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class EventCoordinatorRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Event Coordinator role
        Role::firstOrCreate([
            'name' => 'event_coordinator',
        ], [
            'permissions' => [
                'view_travel_manifests',
                'export_travel_manifests',
                'view_participants',
                'view_conferences',
                'view_sessions'
            ]
        ]);

        $this->command->info('Event Coordinator role created successfully!');
    }
}

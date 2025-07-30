<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class EventCoordinatorUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Event Coordinator user
        $user = User::firstOrCreate([
            'email' => 'coordinator@example.com',
        ], [
            'password' => bcrypt('password'),
            'first_name' => 'Event',
            'last_name' => 'Coordinator'
        ]);

        // Assign Event Coordinator role
        $role = Role::where('name', 'event_coordinator')->first();
        if ($role) {
            $user->roles()->sync([$role->id]);
            $this->command->info('Event Coordinator user created: coordinator@example.com (password: password)');
        } else {
            $this->command->error('Event Coordinator role not found!');
        }
    }
}

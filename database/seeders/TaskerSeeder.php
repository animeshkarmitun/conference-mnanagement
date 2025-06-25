<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Task;
use Illuminate\Support\Facades\Hash;

class TaskerSeeder extends Seeder
{
    public function run(): void
    {
        // Create or get the tasker role
        $role = Role::firstOrCreate([
            'name' => 'tasker',
        ], [
            'permissions' => json_encode(['tasks.view', 'tasks.update', 'notifications.view']),
        ]);

        // Create the tasker user
        $user = User::firstOrCreate([
            'email' => 'tasker@example.com',
        ], [
            'password' => Hash::make('TaskerPass123!'),
            'first_name' => 'Tasker',
            'last_name' => 'User',
            'organization' => 'CGS Events',
        ]);

        // Attach the tasker role
        if (!$user->roles()->where('name', 'tasker')->exists()) {
            $user->roles()->attach($role->id);
        }

        // Assign some tasks to the tasker
        $tasks = Task::take(3)->get();
        foreach ($tasks as $task) {
            $task->assigned_to = $user->id;
            $task->save();
        }
    }
} 
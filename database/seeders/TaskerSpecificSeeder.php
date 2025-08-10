<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Task;
use App\Models\Conference;
use Carbon\Carbon;

class TaskerSpecificSeeder extends Seeder
{
    public function run(): void
    {
        // Get the main tasker user
        $tasker = User::where('email', 'tasker@example.com')->first();
        
        if (!$tasker) {
            $this->command->error('Tasker user not found. Please run TaskerSeeder first.');
            return;
        }

        // Get superadmin for created_by field
        $superadmin = User::whereHas('roles', function ($query) {
            $query->where('name', 'superadmin');
        })->first();

        // Get conferences
        $conferences = Conference::all();

        if ($conferences->isEmpty()) {
            $this->command->error('No conferences found. Please run ConferenceSeeder first.');
            return;
        }

        // Define additional tasks specifically for the main tasker
        $additionalTasks = [
            [
                'title' => 'Review Conference Security Protocols',
                'description' => 'Conduct security assessment and ensure all safety measures are in place for upcoming conferences',
                'theme' => 'Security',
                'status' => 'in_progress',
                'priority' => 'high',
                'due_date' => now()->addDays(1),
                'conference_id' => $conferences->first()->id,
            ],
            [
                'title' => 'Coordinate Volunteer Training Session',
                'description' => 'Organize and conduct training session for conference volunteers on registration and assistance procedures',
                'theme' => 'Training',
                'status' => 'pending',
                'priority' => 'high',
                'due_date' => now()->addDays(2),
                'conference_id' => $conferences->first()->id,
            ],
            [
                'title' => 'Setup Emergency Response Plan',
                'description' => 'Establish emergency contact procedures and first aid stations at conference venues',
                'theme' => 'Safety',
                'status' => 'completed',
                'priority' => 'high',
                'due_date' => now()->subDays(1),
                'conference_id' => $conferences->first()->id,
            ],
            [
                'title' => 'Manage Conference Feedback Collection',
                'description' => 'Set up feedback forms and coordinate collection of attendee and speaker feedback',
                'theme' => 'Feedback',
                'status' => 'in_progress',
                'priority' => 'medium',
                'due_date' => now()->addDays(3),
                'conference_id' => $conferences->first()->id,
            ],
            [
                'title' => 'Coordinate Press and Media Relations',
                'description' => 'Manage press releases, media interviews, and coordinate with journalists covering the conference',
                'theme' => 'Media Relations',
                'status' => 'pending',
                'priority' => 'medium',
                'due_date' => now()->addDays(4),
                'conference_id' => $conferences->first()->id,
            ],
            [
                'title' => 'Setup Conference Mobile App Support',
                'description' => 'Provide technical support for conference mobile app users and troubleshoot issues',
                'theme' => 'Technology Support',
                'status' => 'in_progress',
                'priority' => 'medium',
                'due_date' => now()->addDays(2),
                'conference_id' => $conferences->first()->id,
            ],
            [
                'title' => 'Manage Conference Photography Team',
                'description' => 'Coordinate with photographers, ensure proper coverage of all sessions and events',
                'theme' => 'Media Coverage',
                'status' => 'pending',
                'priority' => 'low',
                'due_date' => now()->addDays(5),
                'conference_id' => $conferences->first()->id,
            ],
            [
                'title' => 'Organize Networking Event Setup',
                'description' => 'Arrange networking areas, ice breaker activities, and facilitate attendee connections',
                'theme' => 'Networking',
                'status' => 'pending',
                'priority' => 'medium',
                'due_date' => now()->addDays(3),
                'conference_id' => $conferences->first()->id,
            ],
            [
                'title' => 'Coordinate Accessibility Services',
                'description' => 'Ensure all venues are accessible, arrange sign language interpreters, and provide assistive technology',
                'theme' => 'Accessibility',
                'status' => 'completed',
                'priority' => 'high',
                'due_date' => now()->subDays(2),
                'conference_id' => $conferences->first()->id,
            ],
            [
                'title' => 'Manage Conference Merchandise',
                'description' => 'Oversee production and distribution of conference merchandise, badges, and promotional materials',
                'theme' => 'Logistics',
                'status' => 'in_progress',
                'priority' => 'medium',
                'due_date' => now()->addDays(1),
                'conference_id' => $conferences->first()->id,
            ],
        ];

        // Create tasks and assign them to the main tasker
        foreach ($additionalTasks as $taskData) {
            $task = Task::firstOrCreate([
                'conference_id' => $taskData['conference_id'],
                'title' => $taskData['title'],
            ], [
                'description' => $taskData['description'],
                'theme' => $taskData['theme'],
                'status' => $taskData['status'],
                'priority' => $taskData['priority'],
                'due_date' => $taskData['due_date'],
                'assigned_to' => $tasker->id,
                'created_by' => $superadmin->id,
                'notes' => 'Assigned to main tasker for conference management',
            ]);

            $this->command->info("Created task: {$task->title} for tasker: {$tasker->email}");
        }

        $this->command->info("Successfully created " . count($additionalTasks) . " tasks for tasker: {$tasker->email}");
    }
}

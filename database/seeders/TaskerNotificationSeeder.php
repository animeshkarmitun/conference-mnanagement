<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Notification;
use App\Models\Conference;

class TaskerNotificationSeeder extends Seeder
{
    public function run(): void
    {
        // Get the tasker user
        $tasker = User::where('email', 'tasker@example.com')->first();
        
        if (!$tasker) {
            $this->command->error('Tasker user not found. Please run TaskerSeeder first.');
            return;
        }

        // Get a conference for notifications
        $conference = Conference::first();
        
        if (!$conference) {
            $this->command->error('No conferences found. Please run ConferenceSeeder first.');
            return;
        }

        // Create sample notifications for the tasker
        $notifications = [
            [
                'message' => 'New task assigned: Review Conference Security Protocols',
                'type' => 'TaskUpdate',
                'related_model' => 'Task',
                'related_id' => 1, // Assuming task ID 1 exists
                'action_url' => '/tasks/1',
                'sent_at' => now()->subHours(2),
                'read_status' => false,
            ],
            [
                'message' => 'Task completed: Setup Emergency Response Plan',
                'type' => 'TaskUpdate',
                'related_model' => 'Task',
                'related_id' => 2, // Assuming task ID 2 exists
                'action_url' => '/tasks/2',
                'sent_at' => now()->subHours(4),
                'read_status' => true,
            ],
            [
                'message' => 'Task status updated: Coordinate Speaker Arrivals is now in progress',
                'type' => 'TaskUpdate',
                'related_model' => 'Task',
                'related_id' => 3, // Assuming task ID 3 exists
                'action_url' => '/tasks/3',
                'sent_at' => now()->subHours(6),
                'read_status' => false,
            ],
            [
                'message' => 'Conference reminder: Digital Marketing Summit 2024 starts in 2 days',
                'type' => 'SessionUpdate',
                'related_model' => 'Session',
                'related_id' => 1, // Assuming session ID 1 exists
                'action_url' => '/sessions/1',
                'sent_at' => now()->subHours(8),
                'read_status' => false,
            ],
            [
                'message' => 'New high priority task: Coordinate Volunteer Training Session',
                'type' => 'TaskUpdate',
                'related_model' => 'Task',
                'related_id' => 4, // Assuming task ID 4 exists
                'action_url' => '/tasks/4',
                'sent_at' => now()->subHours(12),
                'read_status' => true,
            ],
        ];

        foreach ($notifications as $notificationData) {
            Notification::firstOrCreate([
                'user_id' => $tasker->id,
                'conference_id' => $conference->id,
                'message' => $notificationData['message'],
            ], [
                'type' => $notificationData['type'],
                'related_model' => $notificationData['related_model'] ?? null,
                'related_id' => $notificationData['related_id'] ?? null,
                'action_url' => $notificationData['action_url'] ?? null,
                'sent_at' => $notificationData['sent_at'],
                'read_status' => $notificationData['read_status'],
            ]);
        }

        $this->command->info("Created " . count($notifications) . " notifications for tasker: {$tasker->email}");
    }
}

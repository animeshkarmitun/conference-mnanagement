<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Task;
use App\Models\Conference;
use Illuminate\Support\Facades\Hash;

class ConferenceTasksSeeder extends Seeder
{
    public function run(): void
    {
        // Create or get the tasker role
        $taskerRole = Role::firstOrCreate([
            'name' => 'tasker',
        ], [
            'permissions' => json_encode(['tasks.view', 'tasks.update', 'notifications.view']),
        ]);

        // Create multiple taskers
        $taskers = [
            [
                'email' => 'john.tasker@example.com',
                'first_name' => 'John',
                'last_name' => 'Smith',
                'organization' => 'CGS Events',
            ],
            [
                'email' => 'sarah.tasker@example.com',
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'organization' => 'CGS Events',
            ],
            [
                'email' => 'mike.tasker@example.com',
                'first_name' => 'Mike',
                'last_name' => 'Davis',
                'organization' => 'CGS Events',
            ],
            [
                'email' => 'lisa.tasker@example.com',
                'first_name' => 'Lisa',
                'last_name' => 'Wilson',
                'organization' => 'CGS Events',
            ],
        ];

        $taskerUsers = [];
        foreach ($taskers as $taskerData) {
            $user = User::firstOrCreate([
                'email' => $taskerData['email'],
            ], [
                'password' => Hash::make('TaskerPass123!'),
                'first_name' => $taskerData['first_name'],
                'last_name' => $taskerData['last_name'],
                'organization' => $taskerData['organization'],
            ]);

            // Attach the tasker role
            if (!$user->roles()->where('name', 'tasker')->exists()) {
                $user->roles()->attach($taskerRole->id);
            }

            $taskerUsers[] = $user;
        }

        // Get superadmin user for created_by field
        $superadmin = User::whereHas('roles', function ($query) {
            $query->where('name', 'superadmin');
        })->first();

        // Get all conferences
        $conferences = Conference::all();

        // Define tasks for each conference
        $conferenceTasks = [
            // Digital Marketing Summit 2024 (ongoing)
            [
                'conference_name' => 'Digital Marketing Summit 2024',
                'tasks' => [
                    [
                        'title' => 'Setup Registration Desk',
                        'description' => 'Prepare registration area with name tags, welcome packets, and check-in system',
                        'theme' => 'Logistics',
                        'status' => 'completed',
                        'priority' => 'high',
                        'due_date' => now()->subDays(1),
                    ],
                    [
                        'title' => 'Coordinate Speaker Arrivals',
                        'description' => 'Meet speakers at airport, arrange transportation to venue, and brief them on schedule',
                        'theme' => 'Speaker Management',
                        'status' => 'in_progress',
                        'priority' => 'high',
                        'due_date' => now()->addDays(1),
                    ],
                    [
                        'title' => 'Monitor Session Attendance',
                        'description' => 'Track attendance for each session and report to organizers',
                        'theme' => 'Monitoring',
                        'status' => 'in_progress',
                        'priority' => 'medium',
                        'due_date' => now()->addDays(2),
                    ],
                    [
                        'title' => 'Manage Networking Breaks',
                        'description' => 'Ensure refreshments are available and networking areas are properly set up',
                        'theme' => 'Catering',
                        'status' => 'pending',
                        'priority' => 'medium',
                        'due_date' => now()->addDays(3),
                    ],
                ]
            ],
            // Healthcare Innovation Conference (ongoing)
            [
                'conference_name' => 'Healthcare Innovation Conference',
                'tasks' => [
                    [
                        'title' => 'Setup Medical Equipment Demo Area',
                        'description' => 'Arrange and test all medical equipment for demonstrations',
                        'theme' => 'Technical Setup',
                        'status' => 'completed',
                        'priority' => 'high',
                        'due_date' => now()->subDays(1),
                    ],
                    [
                        'title' => 'Coordinate Healthcare Professional Check-ins',
                        'description' => 'Verify credentials and provide specialized welcome materials',
                        'theme' => 'Registration',
                        'status' => 'in_progress',
                        'priority' => 'high',
                        'due_date' => now()->addDays(1),
                    ],
                    [
                        'title' => 'Monitor CME Credit Tracking',
                        'description' => 'Track continuing medical education credits for attendees',
                        'theme' => 'Compliance',
                        'status' => 'in_progress',
                        'priority' => 'high',
                        'due_date' => now()->addDays(2),
                    ],
                    [
                        'title' => 'Arrange Medical Ethics Panel Setup',
                        'description' => 'Prepare panel discussion area with recording equipment',
                        'theme' => 'Session Setup',
                        'status' => 'pending',
                        'priority' => 'medium',
                        'due_date' => now()->addDays(3),
                    ],
                ]
            ],
            // AI & Machine Learning Expo (starting in 2 days)
            [
                'conference_name' => 'AI & Machine Learning Expo',
                'tasks' => [
                    [
                        'title' => 'Setup AI Demo Stations',
                        'description' => 'Install and test AI demonstration stations and interactive displays',
                        'theme' => 'Technical Setup',
                        'status' => 'in_progress',
                        'priority' => 'high',
                        'due_date' => now()->addDays(1),
                    ],
                    [
                        'title' => 'Coordinate Tech Speaker Arrivals',
                        'description' => 'Arrange transportation and accommodation for AI/ML speakers',
                        'theme' => 'Speaker Management',
                        'status' => 'in_progress',
                        'priority' => 'high',
                        'due_date' => now()->addDays(1),
                    ],
                    [
                        'title' => 'Prepare Workshop Materials',
                        'description' => 'Print and organize materials for hands-on AI workshops',
                        'theme' => 'Materials',
                        'status' => 'pending',
                        'priority' => 'medium',
                        'due_date' => now()->addDays(2),
                    ],
                    [
                        'title' => 'Setup Networking App',
                        'description' => 'Configure AI-powered networking app for attendees',
                        'theme' => 'Technology',
                        'status' => 'pending',
                        'priority' => 'medium',
                        'due_date' => now()->addDays(2),
                    ],
                ]
            ],
            // Sustainable Business Conference (starting in 2 days)
            [
                'conference_name' => 'Sustainable Business Conference',
                'tasks' => [
                    [
                        'title' => 'Arrange Eco-Friendly Catering',
                        'description' => 'Coordinate with sustainable food vendors and ensure zero-waste practices',
                        'theme' => 'Catering',
                        'status' => 'in_progress',
                        'priority' => 'high',
                        'due_date' => now()->addDays(1),
                    ],
                    [
                        'title' => 'Setup Green Technology Displays',
                        'description' => 'Install solar panels, EV charging stations, and other green tech demos',
                        'theme' => 'Technical Setup',
                        'status' => 'in_progress',
                        'priority' => 'high',
                        'due_date' => now()->addDays(1),
                    ],
                    [
                        'title' => 'Coordinate Carbon Offset Program',
                        'description' => 'Set up carbon offset tracking for attendee travel and conference activities',
                        'theme' => 'Sustainability',
                        'status' => 'pending',
                        'priority' => 'medium',
                        'due_date' => now()->addDays(2),
                    ],
                    [
                        'title' => 'Prepare Sustainability Report',
                        'description' => 'Create materials for sustainability impact reporting',
                        'theme' => 'Reporting',
                        'status' => 'pending',
                        'priority' => 'low',
                        'due_date' => now()->addDays(3),
                    ],
                ]
            ],
        ];

        // Create tasks and assign them to taskers
        $taskerIndex = 0;
        foreach ($conferenceTasks as $conferenceTask) {
            $conference = Conference::where('name', $conferenceTask['conference_name'])->first();
            
            if ($conference) {
                foreach ($conferenceTask['tasks'] as $taskData) {
                    $task = Task::firstOrCreate([
                        'conference_id' => $conference->id,
                        'title' => $taskData['title'],
                    ], [
                        'description' => $taskData['description'],
                        'theme' => $taskData['theme'],
                        'status' => $taskData['status'],
                        'priority' => $taskData['priority'],
                        'due_date' => $taskData['due_date'],
                        'assigned_to' => $taskerUsers[$taskerIndex % count($taskerUsers)]->id,
                        'created_by' => $superadmin->id,
                        'notes' => 'Assigned by system seeder',
                    ]);

                    $taskerIndex++;
                }
            }
        }
    }
} 
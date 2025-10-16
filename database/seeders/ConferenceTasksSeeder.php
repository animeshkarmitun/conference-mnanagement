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
            // Digital Marketing Summit 2025 (ongoing)
            [
                'conference_name' => 'Digital Marketing Summit 2025',
                'tasks' => [
                    [
                        'title' => 'Setup Registration Desk',
                        'description' => 'Prepare registration area with name tags, welcome packets, and check-in system',
                        'theme' => 'Logistics',
                        'status' => 'completed',
                        'priority' => 'high',
                        'due_date' => now()->subDays(2),
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
                    [
                        'title' => 'Setup Digital Marketing Demo Stations',
                        'description' => 'Install and test marketing automation tools and analytics dashboards',
                        'theme' => 'Technical Setup',
                        'status' => 'completed',
                        'priority' => 'high',
                        'due_date' => now()->subDays(1),
                    ],
                    [
                        'title' => 'Coordinate Social Media Coverage',
                        'description' => 'Manage live social media updates and engagement during sessions',
                        'theme' => 'Marketing',
                        'status' => 'in_progress',
                        'priority' => 'medium',
                        'due_date' => now()->addDays(1),
                    ],
                    [
                        'title' => 'Prepare Marketing Case Study Materials',
                        'description' => 'Print and distribute case study booklets for workshop participants',
                        'theme' => 'Materials',
                        'status' => 'pending',
                        'priority' => 'low',
                        'due_date' => now()->addDays(2),
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
                    [
                        'title' => 'Setup Telemedicine Demo Stations',
                        'description' => 'Install and test telemedicine equipment for live demonstrations',
                        'theme' => 'Technical Setup',
                        'status' => 'completed',
                        'priority' => 'high',
                        'due_date' => now()->subDays(1),
                    ],
                    [
                        'title' => 'Coordinate Medical Device Vendors',
                        'description' => 'Manage vendor setup and equipment demonstrations',
                        'theme' => 'Vendor Management',
                        'status' => 'in_progress',
                        'priority' => 'medium',
                        'due_date' => now()->addDays(1),
                    ],
                    [
                        'title' => 'Prepare Healthcare Compliance Materials',
                        'description' => 'Create and distribute HIPAA compliance and regulatory materials',
                        'theme' => 'Compliance',
                        'status' => 'pending',
                        'priority' => 'high',
                        'due_date' => now()->addDays(2),
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
                    [
                        'title' => 'Setup GPU Computing Clusters',
                        'description' => 'Install and test high-performance computing clusters for ML demos',
                        'theme' => 'Technical Setup',
                        'status' => 'pending',
                        'priority' => 'high',
                        'due_date' => now()->addDays(1),
                    ],
                    [
                        'title' => 'Coordinate AI Ethics Panel',
                        'description' => 'Arrange panel discussion on AI ethics and responsible development',
                        'theme' => 'Session Setup',
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
            // FinTech Revolution Summit (starting in 7 days)
            [
                'conference_name' => 'FinTech Revolution Summit',
                'tasks' => [
                    [
                        'title' => 'Setup Financial Technology Demos',
                        'description' => 'Install and test fintech applications and blockchain demonstrations',
                        'theme' => 'Technical Setup',
                        'status' => 'pending',
                        'priority' => 'high',
                        'due_date' => now()->addDays(5),
                    ],
                    [
                        'title' => 'Coordinate Banking Executive Arrivals',
                        'description' => 'Arrange transportation and accommodation for financial industry speakers',
                        'theme' => 'Speaker Management',
                        'status' => 'pending',
                        'priority' => 'high',
                        'due_date' => now()->addDays(6),
                    ],
                    [
                        'title' => 'Prepare Regulatory Compliance Materials',
                        'description' => 'Create materials on financial regulations and compliance requirements',
                        'theme' => 'Compliance',
                        'status' => 'pending',
                        'priority' => 'high',
                        'due_date' => now()->addDays(6),
                    ],
                    [
                        'title' => 'Setup Digital Payment Stations',
                        'description' => 'Install contactless payment and cryptocurrency demo stations',
                        'theme' => 'Technology',
                        'status' => 'pending',
                        'priority' => 'medium',
                        'due_date' => now()->addDays(6),
                    ],
                ]
            ],
            // Climate Change & Sustainability Forum (starting in 14 days)
            [
                'conference_name' => 'Climate Change & Sustainability Forum',
                'tasks' => [
                    [
                        'title' => 'Setup Environmental Monitoring Equipment',
                        'description' => 'Install air quality monitors and sustainability tracking displays',
                        'theme' => 'Technical Setup',
                        'status' => 'pending',
                        'priority' => 'high',
                        'due_date' => now()->addDays(12),
                    ],
                    [
                        'title' => 'Coordinate Environmental Expert Arrivals',
                        'description' => 'Arrange transportation for climate scientists and sustainability experts',
                        'theme' => 'Speaker Management',
                        'status' => 'pending',
                        'priority' => 'high',
                        'due_date' => now()->addDays(13),
                    ],
                    [
                        'title' => 'Prepare Carbon Footprint Calculator',
                        'description' => 'Set up interactive carbon footprint calculation tools for attendees',
                        'theme' => 'Technology',
                        'status' => 'pending',
                        'priority' => 'medium',
                        'due_date' => now()->addDays(13),
                    ],
                    [
                        'title' => 'Arrange Sustainable Catering',
                        'description' => 'Coordinate with local organic and sustainable food vendors',
                        'theme' => 'Catering',
                        'status' => 'pending',
                        'priority' => 'medium',
                        'due_date' => now()->addDays(12),
                    ],
                ]
            ],
            // EdTech Innovation Conference (starting in 21 days)
            [
                'conference_name' => 'EdTech Innovation Conference',
                'tasks' => [
                    [
                        'title' => 'Setup Educational Technology Labs',
                        'description' => 'Install VR/AR equipment and online learning platforms for demos',
                        'theme' => 'Technical Setup',
                        'status' => 'pending',
                        'priority' => 'high',
                        'due_date' => now()->addDays(19),
                    ],
                    [
                        'title' => 'Coordinate Educator Speaker Arrivals',
                        'description' => 'Arrange transportation for teachers, professors, and EdTech experts',
                        'theme' => 'Speaker Management',
                        'status' => 'pending',
                        'priority' => 'high',
                        'due_date' => now()->addDays(20),
                    ],
                    [
                        'title' => 'Prepare Interactive Learning Materials',
                        'description' => 'Create hands-on learning modules and educational content',
                        'theme' => 'Materials',
                        'status' => 'pending',
                        'priority' => 'medium',
                        'due_date' => now()->addDays(20),
                    ],
                    [
                        'title' => 'Setup Student Showcase Area',
                        'description' => 'Prepare exhibition space for student projects and innovations',
                        'theme' => 'Session Setup',
                        'status' => 'pending',
                        'priority' => 'medium',
                        'due_date' => now()->addDays(19),
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